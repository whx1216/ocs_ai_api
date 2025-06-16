<?php
require_once __DIR__ . '/lib/config.php';

$questionBank = new QuestionBank();
$aiCache = new AICache(__DIR__ . '/cache', $config['cache_expiration']);

$uptime = time() - $_SESSION['start_time'];
$qbStats = $questionBank->getStats();
$cacheStats = $config['cache_enabled'] ? $aiCache->getStats() : null;

// 获取搜索参数
$searchKeyword = $_GET['q'] ?? '';
$searchType = $_GET['type'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;

// 搜索题库
if ($searchKeyword || $searchType) {
    $questions = $questionBank->search($searchKeyword, $searchType);
} else {
    $questions = $questionBank->getAll();
}

// 分页
$totalQuestions = count($questions);
$totalPages = ceil($totalQuestions / $perPage);
$offset = ($page - 1) * $perPage;
$questions = array_slice($questions, $offset, $perPage);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuizMind AI - 统计面板</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@unocss/reset/tailwind.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@unocss/runtime"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-gray-50 dark:bg-gray-900" un-cloak>

<!-- 导航栏 -->
<nav class="sticky top-0 z-50 bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg shadow-sm">
    <div class="container mx-auto px-4 lg:px-6">
        <div class="flex items-center justify-between h-16">
            <a href="index.php" class="flex items-center space-x-2">
                <i class="bi bi-lightning-charge-fill text-2xl text-purple-600"></i>
                <span class="text-xl font-bold gradient-text">QuizMind AI</span>
            </a>
            <nav class="hidden md:flex items-center space-x-8">
                <a href="dashboard.php" class="text-purple-600 font-medium flex items-center gap-2">
                    <i class="bi bi-graph-up"></i>
                    <span>统计面板</span>
                </a>
                <a href="docs.php" class="text-gray-600 hover:text-purple-600 transition-colors flex items-center gap-2">
                    <i class="bi bi-book"></i>
                    <span>API文档</span>
                </a>
            </nav>
            <!-- 移动端菜单按钮 -->
            <button class="md:hidden text-gray-600" onclick="toggleMobileMenu()">
                <i class="bi bi-list text-2xl"></i>
            </button>
        </div>
    </div>
    <!-- 移动端菜单 -->
    <div id="mobile-menu" class="hidden md:hidden border-t border-gray-200 dark:border-gray-700">
        <div class="px-4 py-2 space-y-1">
            <a href="dashboard.php" class="block py-2 text-purple-600 font-medium">
                <i class="bi bi-graph-up mr-2"></i>统计面板
            </a>
            <a href="docs.php" class="block py-2 text-gray-600 hover:text-purple-600">
                <i class="bi bi-book mr-2"></i>API文档
            </a>
        </div>
    </div>
</nav>

<div class="container mx-auto px-4 py-8 max-w-7xl">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">统计面板</h1>

    <!-- 统计卡片 -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">题库总数</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo number_format($qbStats['total_count']); ?></p>
                    <?php if ($qbStats['file_size'] > 0): ?>
                        <p class="text-xs text-gray-500 mt-1"><?php echo number_format($qbStats['file_size'] / 1024 / 1024, 2); ?> MB</p>
                    <?php endif; ?>
                </div>
                <i class="bi bi-database-fill text-3xl text-blue-500 opacity-50"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">AI缓存</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        <?php echo $cacheStats ? $cacheStats['valid'] : 0; ?> / <?php echo $cacheStats ? $cacheStats['total'] : 0; ?>
                    </p>
                    <p class="text-xs text-gray-500 mt-1">有效 / 总数</p>
                </div>
                <i class="bi bi-hdd-fill text-3xl text-green-500 opacity-50"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div class="w-full">
                    <p class="text-gray-500 dark:text-gray-400 text-sm mb-3">题型分布</p>
                    <?php
                    $dist = $qbStats['type_distribution'] ?? [];
                    $total = array_sum($dist);
                    $types = [
                        'single' => ['name' => '单选', 'color' => 'blue', 'icon' => 'ui-radios'],
                        'multiple' => ['name' => '多选', 'color' => 'green', 'icon' => 'ui-checks'],
                        'judgement' => ['name' => '判断', 'color' => 'yellow', 'icon' => 'check-circle'],
                        'completion' => ['name' => '填空', 'color' => 'purple', 'icon' => 'pencil']
                    ];
                    ?>
                    <div class="space-y-2">
                        <?php foreach ($types as $key => $type): ?>
                            <?php
                            $count = $dist[$key] ?? 0;
                            $percentage = $total > 0 ? ($count / $total * 100) : 0;
                            ?>
                            <div class="flex items-center">
                                <i class="bi bi-<?php echo $type['icon']; ?> text-<?php echo $type['color']; ?>-500 mr-2 text-xs"></i>
                                <span class="text-xs text-gray-600 dark:text-gray-400 w-8"><?php echo $type['name']; ?></span>
                                <div class="flex-1 mx-2">
                                    <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                        <div class="progress-bar h-full bg-<?php echo $type['color']; ?>-500 rounded-full"
                                             style="width: <?php echo $percentage; ?>%"></div>
                                    </div>
                                </div>
                                <span class="text-xs text-gray-600 dark:text-gray-400 w-8 text-right"><?php echo $count; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">运行时长</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo formatUptime($uptime); ?></p>
                    <p class="text-xs text-gray-500 mt-1">活跃模型: <?php echo count($config['openai_models']); ?></p>
                </div>
                <i class="bi bi-clock-fill text-3xl text-red-500 opacity-50"></i>
            </div>
        </div>
    </div>

    <!-- 题库浏览 -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 overflow-hidden">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                <i class="bi bi-collection mr-2"></i>题库浏览
            </h2>
            <div class="relative">
                <button onclick="toggleDropdown()" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors flex items-center gap-2">
                    <i class="bi bi-download"></i>导出题库
                    <i class="bi bi-chevron-down text-xs"></i>
                </button>
                <div id="exportDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-lg shadow-xl z-10">
                    <a href="api/export.php?format=json" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-t-lg">
                        <i class="bi bi-filetype-json mr-2"></i>JSON格式
                    </a>
                    <a href="api/export.php?format=csv" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-b-lg">
                        <i class="bi bi-filetype-csv mr-2"></i>CSV格式
                    </a>
                </div>
            </div>
        </div>

        <!-- 搜索栏 -->
        <form method="get" class="mb-6 flex flex-col sm:flex-row gap-2">
            <input type="text" name="q" value="<?php echo h($searchKeyword); ?>"
                   placeholder="搜索题目、选项或答案..."
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <select name="type" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">所有类型</option>
                <option value="single" <?php echo $searchType === 'single' ? 'selected' : ''; ?>>单选题</option>
                <option value="multiple" <?php echo $searchType === 'multiple' ? 'selected' : ''; ?>>多选题</option>
                <option value="judgement" <?php echo $searchType === 'judgement' ? 'selected' : ''; ?>>判断题</option>
                <option value="completion" <?php echo $searchType === 'completion' ? 'selected' : ''; ?>>填空题</option>
            </select>
            <button type="submit" class="px-6 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-colors">
                <i class="bi bi-search"></i>
            </button>
        </form>

        <!-- 题目列表 - 移动端优化 -->
        <div class="overflow-x-auto -mx-6 px-6">
            <table class="w-full min-w-[600px]">
                <thead>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <th class="text-left py-3 px-4 text-xs sm:text-sm">时间</th>
                    <th class="text-left py-3 px-4 text-xs sm:text-sm">类型</th>
                    <th class="text-left py-3 px-4 text-xs sm:text-sm">问题</th>
                    <th class="text-left py-3 px-4 text-xs sm:text-sm hidden sm:table-cell">选项</th>
                    <th class="text-left py-3 px-4 text-xs sm:text-sm">答案</th>
                    <th class="text-center py-3 px-4 text-xs sm:text-sm">访问</th>
                    <th class="text-center py-3 px-4 text-xs sm:text-sm">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($questions)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-8 text-gray-500">
                            <?php echo $searchKeyword || $searchType ? '没有找到匹配的题目' : '题库暂无数据'; ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($questions as $q): ?>
                        <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="py-3 px-4 text-xs">
                                <?php
                                $date = DateTime::createFromFormat('Y-m-d H:i:s', $q['created_at']);
                                echo $date->format('m-d H:i');
                                ?>
                            </td>
                            <td class="py-3 px-4">
                                <span class="type-badge px-2 py-1 text-xs rounded-full
                                    <?php
                                $typeColors = [
                                    'single' => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
                                    'multiple' => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
                                    'judgement' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
                                    'completion' => 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300'
                                ];
                                echo $typeColors[$q['type']] ?? 'bg-gray-100 text-gray-700';
                                ?>">
                                    <?php
                                    $types = [
                                        'single' => '单选',
                                        'multiple' => '多选',
                                        'judgement' => '判断',
                                        'completion' => '填空'
                                    ];
                                    echo $types[$q['type']] ?? '未知';
                                    ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 max-w-[200px] sm:max-w-xs truncate text-xs sm:text-sm" title="<?php echo h($q['question']); ?>">
                                <?php echo h($q['question']); ?>
                            </td>
                            <td class="py-3 px-4 max-w-xs truncate hidden sm:table-cell text-xs sm:text-sm" title="<?php echo h($q['options']); ?>">
                                <?php echo h($q['options'] ?: '无'); ?>
                            </td>
                            <td class="py-3 px-4 max-w-[100px] sm:max-w-xs truncate font-medium text-purple-600 text-xs sm:text-sm" title="<?php echo h($q['answer']); ?>">
                                <?php echo h($q['answer']); ?>
                            </td>
                            <td class="py-3 px-4 text-center text-xs sm:text-sm">
                                <?php echo $q['access_count']; ?>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <button onclick='showDetail(<?php echo json_encode($q); ?>)'
                                        class="text-blue-500 hover:text-blue-700">
                                    <i class="bi bi-eye text-sm"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- 分页 -->
        <?php if ($totalPages > 1): ?>
            <div class="mt-6 flex justify-center overflow-x-auto">
                <nav class="flex space-x-2">
                    <?php if ($page > 1): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>"
                           class="px-3 py-2 bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    <?php endif; ?>

                    <?php
                    $startPage = max(1, $page - 1);
                    $endPage = min($totalPages, $page + 1);

                    if ($startPage > 1): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => 1])); ?>"
                           class="px-3 py-2 bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                            1
                        </a>
                        <?php if ($startPage > 2): ?>
                            <span class="px-2 py-2">...</span>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"
                           class="px-3 py-2 rounded <?php echo $i === $page ? 'bg-purple-500 text-white' : 'bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($endPage < $totalPages): ?>
                        <?php if ($endPage < $totalPages - 1): ?>
                            <span class="px-2 py-2">...</span>
                        <?php endif; ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $totalPages])); ?>"
                           class="px-3 py-2 bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                            <?php echo $totalPages; ?>
                        </a>
                    <?php endif; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>"
                           class="px-3 py-2 bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </nav>
            </div>
        <?php endif; ?>
    </div>

    <!-- 系统信息 -->
    <div class="grid md:grid-cols-2 gap-6 mt-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                <i class="bi bi-cpu mr-2"></i>系统信息
            </h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">PHP版本</span>
                    <span class="text-gray-900 dark:text-white"><?php echo PHP_VERSION; ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">服务器时间</span>
                    <span class="text-gray-900 dark:text-white"><?php echo date('Y-m-d H:i:s'); ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">内存使用</span>
                    <span class="text-gray-900 dark:text-white"><?php echo number_format(memory_get_usage(true) / 1024 / 1024, 2); ?> MB</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-600 dark:text-gray-400">题库创建时间</span>
                    <span class="text-gray-900 dark:text-white"><?php echo $qbStats['created_at'] ?? '未创建'; ?></span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                <i class="bi bi-gear mr-2"></i>配置信息
            </h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">活跃模型</span>
                    <span class="text-gray-900 dark:text-white text-right text-xs"><?php echo implode(', ', $config['openai_models']); ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">缓存状态</span>
                    <span class="text-gray-900 dark:text-white"><?php echo $config['cache_enabled'] ? '已启用' : '未启用'; ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">缓存有效期</span>
                    <span class="text-gray-900 dark:text-white"><?php echo $config['cache_expiration'] / 3600; ?> 小时</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-600 dark:text-gray-400">题库收集</span>
                    <span class="text-gray-900 dark:text-white"><?php echo $config['data_collection'] ? '已启用' : '未启用'; ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 详情模态框 -->
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-2xl w-full max-h-[80vh] overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">题目详情</h3>
            </div>
            <div class="p-6 overflow-y-auto max-h-[60vh]">
                <div class="space-y-4">
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">创建时间</label>
                        <p id="modal-time" class="text-gray-900 dark:text-white"></p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">问题</label>
                        <pre id="modal-question" class="bg-gray-100 dark:bg-gray-700 p-3 rounded-lg mt-1 whitespace-pre-wrap text-sm"></pre>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">类型</label>
                        <p id="modal-type" class="text-gray-900 dark:text-white"></p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">选项</label>
                        <pre id="modal-options" class="bg-gray-100 dark:bg-gray-700 p-3 rounded-lg mt-1 whitespace-pre-wrap text-sm"></pre>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">答案</label>
                        <pre id="modal-answer" class="bg-gray-100 dark:bg-gray-700 p-3 rounded-lg mt-1 whitespace-pre-wrap text-purple-600 font-medium text-sm"></pre>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">访问次数</label>
                        <p id="modal-access" class="text-gray-900 dark:text-white"></p>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 dark:border-gray-700 text-right">
                <button onclick="closeModal()" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                    关闭
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleMobileMenu() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    }

    function toggleDropdown() {
        document.getElementById('exportDropdown').classList.toggle('hidden');
    }

    // 点击外部关闭下拉菜单
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.relative')) {
            document.getElementById('exportDropdown').classList.add('hidden');
        }
    });

    function showDetail(record) {
        const types = {
            'single': '单选题',
            'multiple': '多选题',
            'judgement': '判断题',
            'completion': '填空题'
        };

        document.getElementById('modal-time').textContent = record.created_at;
        document.getElementById('modal-question').textContent = record.question;
        document.getElementById('modal-type').textContent = types[record.type] || '未知';
        document.getElementById('modal-options').textContent = record.options || '无';
        document.getElementById('modal-answer').textContent = record.answer;
        document.getElementById('modal-access').textContent = record.access_count + ' 次';
        document.getElementById('detailModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('detailModal').classList.add('hidden');
    }

    // 模态框外部点击关闭
    document.getElementById('detailModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
</body>
</html>