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

if (!$input || !isset($input['openai_api_key'])) {
    http_response_code(400);
    exit(json_encode(['success' => false, 'message' => '无效的配置数据']));
}

$config = Config::getInstance();
$encrypted = $config->encrypt($input);

echo json_encode([
    'success' => true,
    'encrypted' => $encrypted
]);