<?php
require_once dirname(__DIR__) . '/lib/config.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['success' => false, 'message' => '仅支持POST请求']));
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['config'])) {
    http_response_code(400);
    exit(json_encode(['success' => false, 'message' => '未提供配置数据']));
}

try {
    $config = Config::getInstance();
    $decryptedConfig = $config->decrypt($input['config']);

    if (!$decryptedConfig) {
        http_response_code(400);
        exit(json_encode(['success' => false, 'message' => '配置解密失败，请检查配置是否正确']));
    }

    echo json_encode([
        'success' => true,
        'config' => $decryptedConfig
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '解密过程发生错误: ' . $e->getMessage()
    ]);
}