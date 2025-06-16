<?php
require_once dirname(__DIR__) . '/lib/config.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$cache = null;
if ($config['cache_enabled']) {
    $cache = new SimpleCache(dirname(__DIR__) . '/cache', $config['cache_expiration']);
}

$cacheStats = $cache ? $cache->getStats() : null;

echo json_encode([
    'status' => 'ok',
    'message' => 'AI题库服务运行正常',
    'version' => $config['version'],
    'cache_enabled' => $config['cache_enabled'],
    'cache_stats' => $cacheStats,
    'models' => $config['openai_models'],
    'php_version' => PHP_VERSION,
    'server_time' => date('Y-m-d H:i:s')
]);
?>