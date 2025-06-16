<?php
// 缓存类

class SimpleCache {
    private $cacheDir;
    private $expiration;

    public function __construct($cacheDir, $expiration) {
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
                logMessage('DEBUG', '缓存命中', ['key' => $key]);
                return $data['answer'];
            }
            unlink($file);
            logMessage('DEBUG', '缓存过期', ['key' => $key]);
        }
        return null;
    }

    public function set($question, $answer, $type = '', $options = '') {
        $key = $this->getCacheKey($question, $type, $options);
        $file = $this->getCacheFile($key);

        $data = [
            'time' => time(),
            'answer' => $answer,
            'question' => $question,
            'type' => $type,
            'options' => $options
        ];

        file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE));
        logMessage('DEBUG', '缓存设置', ['key' => $key]);
    }

    public function clear() {
        $files = glob($this->cacheDir . '/*.cache');
        $count = count($files);
        foreach ($files as $file) {
            unlink($file);
        }
        logMessage('INFO', '清除缓存', ['count' => $count]);
        return $count;
    }

    public function size() {
        return count(glob($this->cacheDir . '/*.cache'));
    }

    public function getStats() {
        $files = glob($this->cacheDir . '/*.cache');
        $totalSize = 0;
        $validCount = 0;
        $expiredCount = 0;

        foreach ($files as $file) {
            $totalSize += filesize($file);
            $data = json_decode(file_get_contents($file), true);
            if (time() - $data['time'] < $this->expiration) {
                $validCount++;
            } else {
                $expiredCount++;
            }
        }

        return [
            'total' => count($files),
            'valid' => $validCount,
            'expired' => $expiredCount,
            'size' => $totalSize
        ];
    }
}
?>