<?php
// 启动会话
session_start();

class Config {
    private static $instance = null;
    private $config;
    private const ENCRYPTION_KEY = 'YOUR_NEW_32_CHAR_SECRET_KEY_HERE';
    private const ENCRYPTION_METHOD = 'AES-256-CBC';

    private function __construct() {
        $this->config = $this->loadConfig();
        $this->initializeSystem();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function loadConfig() {
        $defaultConfig = [
            'version' => '2.2.0',
            'base_path' => '/ocs_ai_api',
            'debug' => false,
            'openai_api_key' => '',
            'openai_api_base' => 'https://api.openai.com/v1',
            'openai_models' => ['gpt-4o-mini'],
            'openai_temperature' => 0.7,
            'openai_max_tokens' => 500,
            'access_token' => '',
            'cache_enabled' => true,
            'cache_expiration' => 86400,
            'request_timeout' => 30,
            'log_enabled' => true,
            'log_level' => 'INFO',
            'data_collection' => true
        ];

        $encryptedConfig = $_REQUEST['config'] ?? null;
        if ($encryptedConfig) {
            $decryptedConfig = $this->decrypt($encryptedConfig);
            if ($decryptedConfig) {
                $_SESSION['temp_config'] = array_merge($defaultConfig, $decryptedConfig);
                return $_SESSION['temp_config'];
            }
        }

        return $_SESSION['temp_config'] ?? $defaultConfig;
    }

    public function encrypt($data) {
        $iv = openssl_random_pseudo_bytes(16);
        $encrypted = openssl_encrypt(
            json_encode($data),
            self::ENCRYPTION_METHOD,
            self::ENCRYPTION_KEY,
            0,
            $iv
        );
        return base64_encode($iv . $encrypted);
    }

    public function decrypt($encryptedData) {
        try {
            $data = base64_decode($encryptedData);
            if (!$data) return null;

            $iv = substr($data, 0, 16);
            $encrypted = substr($data, 16);

            $decrypted = openssl_decrypt(
                $encrypted,
                self::ENCRYPTION_METHOD,
                self::ENCRYPTION_KEY,
                0,
                $iv
            );

            return $decrypted ? json_decode($decrypted, true) : null;
        } catch (Exception $e) {
            return null;
        }
    }

    private function initializeSystem() {
        date_default_timezone_set('Asia/Shanghai');

        if (!isset($_SESSION['start_time'])) {
            $_SESSION['start_time'] = time();
        }

        $this->configureErrorReporting();
        $this->defineBasePaths();
        $this->loadDependencies();
    }

    private function configureErrorReporting() {
        if ($this->config['debug']) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        } else {
            error_reporting(0);
            ini_set('display_errors', 0);
        }
    }

    private function defineBasePaths() {
        define('BASE_PATH', $this->config['base_path']);
        define('BASE_URL', rtrim($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . BASE_PATH, '/'));
    }

    private function loadDependencies() {
        require_once __DIR__ . '/common.php';
        require_once __DIR__ . '/storage.php';
    }

    public function get($key = null) {
        return $key ? ($this->config[$key] ?? null) : $this->config;
    }
}

$config = Config::getInstance();