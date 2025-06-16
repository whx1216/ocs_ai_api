<?php
require_once dirname(__DIR__) . '/lib/config.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

if (!verifyAccessToken()) {
    echo json_encode(['success' => false, 'message' => '无效的访问令牌']);
    exit;
}

$uptime = time() - $_SESSION['start_time'];

// 初始化组件
$questionBank = new QuestionBank();
$aiCache = new AICache(dirname(__DIR__) . '/cache', $config['cache_expiration']);

// 获取统计信息
$qbStats = $questionBank->getStats();
$cacheStats = $config['cache_enabled'] ? $aiCache->getStats() : null;

// 获取日志统计
$logDir = dirname(__DIR__) . '/logs';
$logFiles = is_dir($logDir) ? glob($logDir . '/*.log') : [];
$totalLogSize = 0;
foreach ($logFiles as $file) {
    $totalLogSize += filesize($file);
}

echo json_encode([
    'version' => $config['version'],
    'uptime' => $uptime,
    'uptime_formatted' => formatUptime($uptime),
    'models' => $config['openai_models'],
    'cache_enabled' => $config['cache_enabled'],
    'cache_stats' => $cacheStats,
    'question_bank' => $qbStats,
    'log_files_count' => count($logFiles),
    'log_total_size' => $totalLogSize,
    'php_version' => PHP_VERSION,
    'memory_usage' => memory_get_usage(true),
    'peak_memory_usage' => memory_get_peak_usage(true)
]);
?>