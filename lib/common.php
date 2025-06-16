<?php
// 公共函数库

// 生成URL
function url($path = '') {
    return BASE_URL . '/' . ltrim($path, '/');
}

// 记录日志
function logMessage($level, $message, $context = []) {
    global $config;

    if (!$config['log_enabled']) {
        return;
    }

    $logLevels = ['DEBUG' => 0, 'INFO' => 1, 'WARNING' => 2, 'ERROR' => 3];
    $currentLevel = $logLevels[$config['log_level']] ?? 1;
    $messageLevel = $logLevels[$level] ?? 1;

    if ($messageLevel < $currentLevel) {
        return;
    }

    $logDir = dirname(__DIR__) . '/logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }

    $logFile = $logDir . '/' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $contextStr = !empty($context) ? ' ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
    $logEntry = "[$timestamp] [$level] $message$contextStr\n";

    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

// 验证访问令牌
function verifyAccessToken() {
    global $config;

    if (empty($config['access_token'])) {
        return true;
    }

    $token = $_SERVER['HTTP_X_ACCESS_TOKEN'] ?? $_GET['token'] ?? '';
    return $token === $config['access_token'];
}

// HTML转义
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// 格式化运行时间
function formatUptime($seconds) {
    $days = floor($seconds / 86400);
    $hours = floor(($seconds % 86400) / 3600);
    $minutes = floor(($seconds % 3600) / 60);

    return "{$days}天{$hours}小时{$minutes}分钟";
}

// 构建AI提示词
function buildPrompt($question, $options, $type) {
    $prompt = "问题: $question\n";

    $typeHints = [
        'single' => '这是一道单选题。',
        'multiple' => '这是一道多选题，答案请用#符号分隔。',
        'judgement' => '这是一道判断题，需要回答：正确/对/true/√ 或者 错误/错/false/×。',
        'completion' => '这是一道填空题。'
    ];

    if (isset($typeHints[$type])) {
        $prompt .= $typeHints[$type] . "\n";
    }

    if ($options) {
        $prompt .= "选项:\n$options\n";
    }

    $prompt .= "请直接给出答案，不要解释。";

    return $prompt;
}

// 处理答案格式
function processAnswer($answer, $type, $options = '') {
    $answer = trim($answer);

    if ($type === 'multiple') {
        // 如果答案是纯选项字母(如 A B D)
        if (preg_match('/^[A-F\s]+$/i', $answer)) {
            // 将选项文字提取出来
            $optionsArray = parseOptions($options);
            $letters = preg_split('/\s+/', $answer);
            $fullAnswers = [];

            foreach ($letters as $letter) {
                $letter = strtoupper(trim($letter));
                if (isset($optionsArray[$letter])) {
                    $fullAnswers[] = $optionsArray[$letter];
                }
            }

            if (!empty($fullAnswers)) {
                $answer = implode('#', $fullAnswers);
            }
        }
        // 如果答案中没有分隔符,尝试提取选项内容
        else if (strpos($answer, '#') === false) {
            $parts = preg_split('/[,，、\s]+/', $answer);
            if (count($parts) > 1) {
                $answer = implode('#', array_unique($parts));
            }
        }
    }

    return $answer;
}

function parseOptions($options) {
    $result = [];
    if (empty($options)) return $result;

    // 按行分割选项
    $lines = explode("\n", $options);
    foreach ($lines as $line) {
        // 匹配选项字母和内容 (如 A:选项内容 或 A.选项内容)
        if (preg_match('/^([A-F])[\.:](.+)$/i', trim($line), $matches)) {
            $letter = strtoupper($matches[1]);
            $content = trim($matches[2]);
            $result[$letter] = $content;
        }
    }

    return $result;
}

// OpenAI API调用 - 支持多模型轮询
function callOpenAI($prompt, $modelIndex = 0) {
    global $config;

    $models = $config['openai_models'];
    if ($modelIndex >= count($models)) {
        throw new Exception("所有模型均调用失败");
    }

    $model = $models[$modelIndex];
    logMessage('INFO', "调用OpenAI API", ['model' => $model, 'prompt_length' => strlen($prompt)]);

    $data = [
        'model' => $model,
        'messages' => [
            [
                'role' => 'system',
                'content' => '你是一个专业的考试答题助手。请按以下格式回答:
                - 单选题: 直接回答完整的选项内容(如: "地球"而不是"A")
                - 多选题: 用#号分隔多个完整的选项内容(如: "太阳#月球#地球"而不是"A B C")
                - 判断题: 只回答 正确/对/true/√ 或 错误/错/false/×
                - 填空题: 直接给出答案
                请只回答答案,不要包含任何解释。'
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ],
        'temperature' => $config['openai_temperature'],
        'max_tokens' => $config['openai_max_tokens']
    ];

    $ch = curl_init($config['openai_api_base'] . '/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $config['openai_api_key']
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, $config['request_timeout']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        logMessage('ERROR', "CURL错误", ['error' => $error]);
        // 尝试下一个模型
        return callOpenAI($prompt, $modelIndex + 1);
    }

    if ($httpCode != 200) {
        logMessage('ERROR', "API请求失败", ['http_code' => $httpCode, 'response' => $response]);
        // 尝试下一个模型
        return callOpenAI($prompt, $modelIndex + 1);
    }

    $result = json_decode($response, true);
    if (isset($result['error'])) {
        logMessage('ERROR', "API错误", ['error' => $result['error']]);
        // 尝试下一个模型
        return callOpenAI($prompt, $modelIndex + 1);
    }

    $answer = $result['choices'][0]['message']['content'];
    logMessage('INFO', "API调用成功", ['model' => $model, 'answer' => $answer]);

    return $answer;
}
?>