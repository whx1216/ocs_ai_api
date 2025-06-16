<?php
// 启动会话
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 定义加密密钥
define('CONFIG_ENCRYPTION_KEY', 'EduBrainAI2024SecretKey');

// 默认配置（仅用于系统基础设置）
$defaultConfig = [
    'version' => '2.2.0',
    'base_path' => '/ocs_ai_api',
    'debug' => false,
    // 以下配置需要通过API传入
    'openai_api_key' => '',
    'openai_api_base' => 'https://api.openai.com/v1',
    'openai_models' => ['gpt-3.5-turbo'],
    'openai_temperature' => 0.7,
    'openai_max_tokens' => 500,
    'access_token' => '',
    'cache_enabled' => true,
    'cache_expiration' => 86400, // 24小时
    'request_timeout' => 30,
    'log_enabled' => true,
    'log_level' => 'INFO',
    'data_collection' => true // 是否收集题库
];

// 加密/解密函数
function encryptConfig($config) {
    $data = json_encode($config);
    $method = 'AES-256-CBC';
    $iv = openssl_random_pseudo_bytes(16);
    $encrypted = openssl_encrypt($data, $method, CONFIG_ENCRYPTION_KEY, 0, $iv);
    return base64_encode($iv . $encrypted);
}

function decryptConfig($encryptedData) {
    try {
        $data = base64_decode($encryptedData);
        if (!$data) return null;

        $method = 'AES-256-CBC';
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);

        $decrypted = openssl_decrypt($encrypted, $method, CONFIG_ENCRYPTION_KEY, 0, $iv);
        if (!$decrypted) return null;

        return json_decode($decrypted, true);
    } catch (Exception $e) {
        return null;
    }
}

// 从请求中获取配置
function getConfig() {
    global $defaultConfig;

    // 尝试从请求中获取加密的配置
    $encryptedConfig = $_REQUEST['config'] ?? null;
    if ($encryptedConfig) {
        $decryptedConfig = decryptConfig($encryptedConfig);
        if ($decryptedConfig) {
            // 合并配置
            $config = array_merge($defaultConfig, $decryptedConfig);
            $_SESSION['temp_config'] = $config;
            return $config;
        }
    }

    // 尝试从会话中获取临时配置
    if (isset($_SESSION['temp_config'])) {
        return $_SESSION['temp_config'];
    }

    // 返回默认配置（但API功能将不可用）
    return $defaultConfig;
}

// 获取当前配置
$config = getConfig();

// 设置时区
date_default_timezone_set('Asia/Shanghai');

// 记录启动时间
if (!isset($_SESSION['start_time'])) {
    $_SESSION['start_time'] = time();
}

// 错误处理
if ($config['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// 定义基础路径
define('BASE_PATH', $config['base_path']);
define('BASE_URL', rtrim($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . BASE_PATH, '/'));

// 加载其他库文件
require_once __DIR__ . '/common.php';
require_once __DIR__ . '/storage.php';
?>