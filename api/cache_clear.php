<?php
require_once dirname(__DIR__) . '/lib/config.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

if (!verifyAccessToken()) {
    echo json_encode(['success' => false, 'message' => '无效的访问令牌']);
    exit;
}

if ($config['cache_enabled']) {
    $cache = new SimpleCache(dirname(__DIR__) . '/cache', $config['cache_expiration']);
    $count = $cache->clear();
    echo json_encode(['success' => true, 'message' => "缓存已清除，共删除 {$count} 条记录"]);
} else {
    echo json_encode(['success' => false, 'message' => '缓存未启用']);
}
?>