<?php
require_once dirname(__DIR__) . '/lib/config.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Access-Token');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

$startTime = microtime(true);

// 检查是否提供了配置
if (!isset($_REQUEST['config'])) {
    echo json_encode(['code' => 0, 'msg' => '缺少配置参数。请使用配置生成器生成配置。']);
    exit;
}

// 处理配置参数
$config = getConfig();
if (empty($config['openai_api_key'])) {
    echo json_encode(['code' => 0, 'msg' => '无效的配置参数']);
    exit;
}

if (!verifyAccessToken()) {
    echo json_encode(['code' => 0, 'msg' => '无效的访问令牌']);
    exit;
}

// 获取参数
$question = $_REQUEST['title'] ?? '';
$type = $_REQUEST['type'] ?? '';
$options = $_REQUEST['options'] ?? '';
$allowCollection = $_REQUEST['allow_collection'] ?? $config['data_collection'];

logMessage('INFO', '接收到搜索请求', [
    'question' => substr($question, 0, 50) . '...',
    'type' => $type,
    'allow_collection' => $allowCollection
]);

if (empty($question)) {
    echo json_encode(['code' => 0, 'msg' => '未提供问题内容']);
    exit;
}

try {
    // 初始化题库和缓存
    $questionBank = new QuestionBank();
    $aiCache = null;

    if ($config['cache_enabled']) {
        $aiCache = new AICache(dirname(__DIR__) . '/cache', $config['cache_expiration']);
    }

    $answer = null;

    // 1. 首先从题库查找
    $answer = $questionBank->findAnswer($question, $type, $options);

    // 2. 如果题库没有，检查AI缓存
    if (!$answer && $aiCache) {
        $answer = $aiCache->get($question, $type, $options);
    }

    // 3. 如果都没有，调用AI
    if (!$answer) {
        // 构建提示
        $prompt = buildPrompt($question, $options, $type);

        // 调用AI
        $answer = callOpenAI($prompt);

        // 处理答案格式
        $answer = processAnswer($answer, $type);

        // 保存到AI缓存（如果启用）
        if ($aiCache) {
            $aiCache->set($question, $answer, $type, $options);
        }
    }

    // 4. 保存到题库（如果允许收集）
    if ($allowCollection !== 'false' && $allowCollection !== false && $allowCollection !== '0') {
        $questionBank->addQuestion($question, $type, $options, $answer);
    } else {
        logMessage('INFO', '用户选择不收集此题目');
    }

    $processTime = round((microtime(true) - $startTime) * 1000, 2);
    logMessage('INFO', '搜索请求完成', ['process_time' => $processTime . 'ms']);

    echo json_encode([
        'code' => 1,
        'question' => $question,
        'answer' => $answer
    ]);

} catch (Exception $e) {
    logMessage('ERROR', '搜索请求失败', ['error' => $e->getMessage()]);
    echo json_encode([
        'code' => 0,
        'msg' => '发生错误: ' . $e->getMessage()
    ]);
}
?>