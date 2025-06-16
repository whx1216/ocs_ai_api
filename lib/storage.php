<?php
// 题库存储管理类

class QuestionBank {
    private $dataFile;
    private $lockFile;
    private $maxRetries = 10;
    private $retryDelay = 100000; // 100ms in microseconds

    public function __construct($dataDir = null) {
        if (!$dataDir) {
            $dataDir = dirname(__DIR__) . '/data';
        }

        if (!is_dir($dataDir)) {
            mkdir($dataDir, 0777, true);
        }

        $this->dataFile = $dataDir . '/question_bank.json';
        $this->lockFile = $dataDir . '/question_bank.lock';

        // 初始化数据文件
        if (!file_exists($this->dataFile)) {
            $this->initDataFile();
        }
    }

    private function initDataFile() {
        $initialData = [
            'version' => '1.0',
            'created_at' => date('Y-m-d H:i:s'),
            'total_count' => 0,
            'questions' => []
        ];
        file_put_contents($this->dataFile, json_encode($initialData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    private function acquireLock() {
        $retry = 0;
        while ($retry < $this->maxRetries) {
            $fp = fopen($this->lockFile, 'c');
            if (flock($fp, LOCK_EX | LOCK_NB)) {
                return $fp;
            }
            fclose($fp);
            usleep($this->retryDelay);
            $retry++;
        }
        throw new Exception('无法获取文件锁，请稍后重试');
    }

    private function releaseLock($fp) {
        flock($fp, LOCK_UN);
        fclose($fp);
        @unlink($this->lockFile);
    }

    public function addQuestion($question, $type, $options, $answer) {
        $lock = $this->acquireLock();

        try {
            // 读取现有数据
            $data = json_decode(file_get_contents($this->dataFile), true);

            // 生成唯一ID（基于内容的哈希，避免重复）
            $id = md5($question . $type . $options);

            // 检查是否已存在
            $exists = false;
            foreach ($data['questions'] as &$q) {
                if ($q['id'] === $id) {
                    // 更新访问次数和最后访问时间
                    $q['access_count']++;
                    $q['last_accessed'] = date('Y-m-d H:i:s');
                    $exists = true;
                    break;
                }
            }

            if (!$exists) {
                // 添加新问题
                $newQuestion = [
                    'id' => $id,
                    'question' => $question,
                    'type' => $type,
                    'options' => $options,
                    'answer' => $answer,
                    'created_at' => date('Y-m-d H:i:s'),
                    'last_accessed' => date('Y-m-d H:i:s'),
                    'access_count' => 1
                ];

                array_unshift($data['questions'], $newQuestion);
                $data['total_count']++;
            }

            // 保存数据（原子操作）
            $tempFile = $this->dataFile . '.tmp';
            file_put_contents($tempFile, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            rename($tempFile, $this->dataFile);

            logMessage('INFO', '题库记录已保存', [
                'id' => $id,
                'new' => !$exists,
                'total' => $data['total_count']
            ]);

        } finally {
            $this->releaseLock($lock);
        }
    }

    public function findAnswer($question, $type, $options) {
        // 生成查询ID
        $id = md5($question . $type . $options);

        // 读取数据（不需要锁，因为只是读取）
        if (!file_exists($this->dataFile)) {
            return null;
        }

        $data = json_decode(file_get_contents($this->dataFile), true);

        foreach ($data['questions'] as $q) {
            if ($q['id'] === $id) {
                logMessage('DEBUG', '题库命中', ['id' => $id]);
                return $q['answer'];
            }
        }

        return null;
    }

    public function getAll($limit = null, $offset = 0) {
        if (!file_exists($this->dataFile)) {
            return [];
        }

        $data = json_decode(file_get_contents($this->dataFile), true);
        $questions = $data['questions'];

        if ($limit !== null) {
            return array_slice($questions, $offset, $limit);
        }

        return $questions;
    }

    public function getStats() {
        if (!file_exists($this->dataFile)) {
            return [
                'total_count' => 0,
                'file_size' => 0,
                'created_at' => null
            ];
        }

        $data = json_decode(file_get_contents($this->dataFile), true);

        return [
            'total_count' => $data['total_count'],
            'file_size' => filesize($this->dataFile),
            'created_at' => $data['created_at'],
            'type_distribution' => $this->getTypeDistribution($data['questions'])
        ];
    }

    private function getTypeDistribution($questions) {
        $distribution = [
            'single' => 0,
            'multiple' => 0,
            'judgement' => 0,
            'completion' => 0,
            'other' => 0
        ];

        foreach ($questions as $q) {
            $type = $q['type'] ?: 'other';
            if (isset($distribution[$type])) {
                $distribution[$type]++;
            } else {
                $distribution['other']++;
            }
        }

        return $distribution;
    }

    public function export() {
        if (!file_exists($this->dataFile)) {
            return [];
        }

        $data = json_decode(file_get_contents($this->dataFile), true);
        return $data['questions'];
    }

    public function search($keyword, $type = null) {
        if (!file_exists($this->dataFile)) {
            return [];
        }

        $data = json_decode(file_get_contents($this->dataFile), true);
        $results = [];

        foreach ($data['questions'] as $q) {
            // 类型过滤
            if ($type && $q['type'] !== $type) {
                continue;
            }

            // 关键词搜索
            if ($keyword) {
                $searchFields = $q['question'] . ' ' . $q['options'] . ' ' . $q['answer'];
                if (stripos($searchFields, $keyword) === false) {
                    continue;
                }
            }

            $results[] = $q;
        }

        return $results;
    }
}

// AI响应缓存类（避免重复调用）
class AICache {
    private $cacheDir;
    private $expiration;

    public function __construct($cacheDir, $expiration = 86400) { // 默认24小时
        $this->cacheDir = $cacheDir;
        $this->expiration = $expiration;

        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }
    }

    private function getCacheKey($question, $type, $options) {
        return md5($question . '|' . $type . '|' . $options);
    }

    private function getCacheFile($key) {
        return $this->cacheDir . '/' . $key . '.cache';
    }

    public function get($question, $type = '', $options = '') {
        $key = $this->getCacheKey($question, $type, $options);
        $file = $this->getCacheFile($key);

        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
            if (time() - $data['time'] < $this->expiration) {
                logMessage('DEBUG', 'AI缓存命中，避免重复调用', ['key' => $key]);
                return $data['answer'];
            }
            // 过期则删除
            unlink($file);
        }
        return null;
    }

    public function set($question, $answer, $type = '', $options = '') {
        $key = $this->getCacheKey($question, $type, $options);
        $file = $this->getCacheFile($key);

        $data = [
            'time' => time(),
            'answer' => $answer
        ];

        file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE));
        logMessage('DEBUG', 'AI响应已缓存', ['key' => $key]);
    }

    public function clear() {
        $files = glob($this->cacheDir . '/*.cache');
        foreach ($files as $file) {
            unlink($file);
        }
        return count($files);
    }

    public function getStats() {
        $files = glob($this->cacheDir . '/*.cache');
        $totalSize = 0;
        $validCount = 0;

        foreach ($files as $file) {
            $totalSize += filesize($file);
            $data = json_decode(file_get_contents($file), true);
            if (time() - $data['time'] < $this->expiration) {
                $validCount++;
            }
        }

        return [
            'total' => count($files),
            'valid' => $validCount,
            'expired' => count($files) - $validCount,
            'size' => $totalSize
        ];
    }
}
?>