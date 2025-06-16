<?php
require_once __DIR__ . '/lib/config.php';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuizMind AI - 智能答题引擎</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@unocss/reset/tailwind.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@unocss/runtime"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-gray-50 dark:bg-gray-900" un-cloak>
<!-- 导航栏 -->
<nav class="fixed top-0 w-full z-50 bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg shadow-sm">
    <div class="container mx-auto px-4 lg:px-6">
        <div class="flex items-center justify-between h-16">
            <a href="index.php" class="flex items-center space-x-2">
                <i class="bi bi-lightning-charge-fill text-2xl text-purple-600"></i>
                <span class="text-xl font-bold gradient-text">QuizMind AI</span>
            </a>
            <nav class="hidden md:flex items-center space-x-8">
                <a href="dashboard.php" class="text-gray-600 hover:text-purple-600 transition-colors flex items-center gap-2">
                    <i class="bi bi-graph-up"></i>
                    <span>统计面板</span>
                </a>
                <a href="docs.php" class="text-gray-600 hover:text-purple-600 transition-colors flex items-center gap-2">
                    <i class="bi bi-book"></i>
                    <span>使用文档</span>
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
            <a href="dashboard.php" class="block py-2 text-gray-600 hover:text-purple-600">
                <i class="bi bi-graph-up mr-2"></i>统计面板
            </a>
            <a href="docs.php" class="block py-2 text-gray-600 hover:text-purple-600">
                <i class="bi bi-book mr-2"></i>使用文档
            </a>
        </div>
    </div>
</nav>

<!-- Hero 区域 - 优化为全屏或紧凑布局 -->
<section class="hero-section relative overflow-hidden bg-gradient-to-br from-purple-50 to-pink-50 dark:from-gray-900 dark:to-gray-800 pt-16">
    <div class="absolute inset-0 bg-grid-gray-100/[0.03] dark:bg-grid-white/[0.03]"></div>
    <div class="container mx-auto px-4 flex-1 flex flex-col justify-center">
        <div class="text-center">
            <div class="animate-float inline-block mb-4">
                <i class="bi bi-lightning-charge-fill text-5xl md:text-6xl text-purple-600"></i>
            </div>
            <h1 class="text-3xl md:text-4xl lg:text-6xl font-bold text-gray-900 dark:text-white mb-4">
                Quiz<span class="gradient-text">Mind</span> AI
            </h1>
            <p class="text-lg md:text-xl text-gray-600 dark:text-gray-300 mb-8 max-w-2xl mx-auto">
                开源，免费，方便，安全的ocs AI答题API
            </p>

            <!-- 快速开始按钮 -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
                <button onclick="scrollToConfig()" class="px-8 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-full hover:shadow-lg transition-all transform hover:scale-105 font-medium">
                    <i class="bi bi-rocket-takeoff mr-2"></i>立即开始
                </button>
                <a href="docs.php" class="px-8 py-3 bg-white dark:bg-gray-800 text-purple-600 dark:text-purple-400 border-2 border-purple-600 dark:border-purple-400 rounded-full hover:shadow-lg transition-all font-medium relative z-10 inline-block">
                    <i class="bi bi-book mr-2"></i>查看文档
                </a>
            </div>

            <!-- 简化的步骤预览 -->
            <div class="grid md:grid-cols-3 gap-4 max-w-3xl mx-auto text-left">
                <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur rounded-lg p-4 flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-sm font-bold text-purple-600">1</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-sm">配置API</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-xs">填写OpenAI密钥</p>
                    </div>
                </div>
                <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur rounded-lg p-4 flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-sm font-bold text-purple-600">2</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-sm">生成配置</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-xs">获取OCS配置</p>
                    </div>
                </div>
                <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur rounded-lg p-4 flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-sm font-bold text-purple-600">3</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-sm">集成使用</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-xs">添加到OCS</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="hidden md:block text-center mt-8 pb-8">
            <button onclick="scrollToConfig()" class="scroll-indicator text-gray-400 hover:text-purple-600 transition-colors">
                <i class="bi bi-chevron-down text-3xl"></i>
            </button>
        </div>
    </div>
</section>

<!-- 主要内容区域 -->
<div id="main-content" class="container mx-auto px-4 py-12 max-w-6xl overflow-x-hidden">
    <!-- 导入配置模态框 -->
    <div id="importModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        <i class="bi bi-cloud-download mr-2"></i>导入OCS配置
                    </h3>
                </div>
                <div class="p-6 overflow-y-auto">
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        请粘贴您之前生成的完整OCS配置（JSON格式）：
                    </p>
                    <textarea id="import-config-text" rows="10"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white font-mono text-sm"
                              placeholder='[{
  "name": "QuizMind AI智能题库",
  "url": "...",
  "data": {
    "config": "..."
  }
}]'></textarea>
                    <div id="import-error" class="hidden mt-2 text-red-600 text-sm">
                        <i class="bi bi-exclamation-circle mr-1"></i>
                        <span id="import-error-text"></span>
                    </div>
                </div>
                <div class="p-6 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-2">
                    <button onclick="closeImportModal()" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        取消
                    </button>
                    <button onclick="importConfig()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="bi bi-check-circle mr-2"></i>导入
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-8 max-w-full overflow-hidden">
        <!-- 配置生成器 -->
        <div id="config" class="max-w-full overflow-hidden min-w-0">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 lg:p-8">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="bi bi-gear-fill text-white text-xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">配置生成器</h2>
                    </div>
                    <button onclick="showImportModal()" class="w-full sm:w-auto px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
                        <i class="bi bi-cloud-download mr-2"></i>导入配置
                    </button>
                </div>

                <!-- 配置状态提示 -->
                <div id="config-status" class="hidden mb-4 p-3 rounded-lg">
                    <p class="text-sm">
                        <i class="bi bi-info-circle mr-1"></i>
                        <span id="config-status-text"></span>
                    </p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            OpenAI API Key <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" id="api-key"
                                   class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                   placeholder="sk-...">
                            <button onclick="togglePasswordVisibility('api-key')" type="button"
                                    class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700">
                                <i class="bi bi-eye" id="api-key-toggle"></i>
                            </button>
                        </div>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            <i class="bi bi-shield-check mr-1"></i>您的密钥将被加密处理，确保安全
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            API Base URL
                        </label>
                        <input type="text" id="api-base" value="https://api.openai.com/v1"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            如使用第三方API服务，请修改此地址
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            模型列表 <span class="text-gray-500">(每行一个，按优先级排序)</span>
                        </label>
                        <textarea id="models" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white font-mono text-sm"
                                  placeholder="gpt-3.5-turbo&#10;gpt-4&#10;gpt-4-turbo">gpt-3.5-turbo</textarea>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            支持多个模型，系统会按顺序尝试直到成功
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            访问令牌 <span class="text-gray-500">(可选)</span>
                        </label>
                        <div class="relative">
                            <input type="password" id="access-token"
                                   class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                   placeholder="留空表示不需要令牌">
                            <button onclick="togglePasswordVisibility('access-token')" type="button"
                                    class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700">
                                <i class="bi bi-eye" id="access-token-toggle"></i>
                            </button>
                        </div>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            设置后，API调用需要提供此令牌以增加安全性
                        </p>
                    </div>

                    <div class="space-y-3 pt-2">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" id="cache-enabled" checked class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">启用缓存功能</span>
                            <span class="ml-2 text-sm text-gray-500">(避免重复调用AI)</span>
                        </label>

                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" id="data-collection" checked class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">收集题库数据</span>
                            <span class="ml-2 text-sm text-gray-500">(构建题库，提升响应速度)</span>
                        </label>
                    </div>

                    <button onclick="generateConfig()"
                            class="w-full mt-6 px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transition-all transform hover:scale-105 font-medium">
                        <i class="bi bi-magic mr-2"></i>生成OCS配置
                    </button>

                    <!-- 如果已有配置，显示更新按钮 -->
                    <button id="update-config-btn" onclick="generateConfig(true)"
                            class="hidden w-full px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all transform hover:scale-105 font-medium">
                        <i class="bi bi-arrow-clockwise mr-2"></i>更新OCS配置
                    </button>
                </div>
            </div>

            <div id="ocs-config-card" class="mt-8 bg-gray-900 rounded-2xl shadow-xl hidden max-w-full overflow-hidden">
                <div class="p-6 lg:p-8 max-w-full">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 gap-4">
                        <h3 class="text-xl font-bold text-white break-words">
                            <i class="bi bi-check-circle-fill text-green-400 mr-2"></i>生成的OCS配置
                        </h3>
                        <button onclick="copyConfig()" class="w-full sm:w-auto flex-shrink-0 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                            <i class="bi bi-clipboard mr-2"></i>复制
                        </button>
                    </div>
                    <div class="relative rounded-lg max-w-full overflow-hidden">
                        <div class="max-w-full overflow-x-auto">
                            <pre id="ocs-config-display" class="code-block max-w-full"></pre>
                        </div>
                    </div>
                    <div class="mt-4 p-4 bg-green-900/20 border border-green-800 rounded-lg">
                        <p class="text-green-400 text-sm break-words">
                            <i class="bi bi-info-circle mr-2"></i>
                            配置已生成！请将以上配置复制到OCS的"自定义题库"设置中。
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 测试工具 -->
        <div id="test" class="max-w-full overflow-hidden min-w-0">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 lg:p-8">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 gradient-primary rounded-xl flex items-center justify-center mr-4">
                        <i class="bi bi-play-circle-fill text-white text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">在线测试</h2>
                </div>

                <div id="test-warning" class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
                    <p class="text-yellow-800 dark:text-yellow-200 text-sm">
                        <i class="bi bi-exclamation-triangle mr-2"></i>
                        请先在左侧生成或导入配置后再进行测试
                    </p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            问题内容
                        </label>
                        <textarea id="question" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                  placeholder="请输入您的问题..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            问题类型
                        </label>
                        <select id="question-type"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">自动识别</option>
                            <option value="single">单选题</option>
                            <option value="multiple">多选题</option>
                            <option value="judgement">判断题</option>
                            <option value="completion">填空题</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            选项内容 <span class="text-gray-500">(可选)</span>
                        </label>
                        <textarea id="options" rows="4"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                  placeholder="A. 选项1&#10;B. 选项2&#10;C. 选项3&#10;D. 选项4"></textarea>
                    </div>

                    <button id="search-btn"
                            class="w-full mt-4 px-6 py-3 gradient-primary text-white rounded-lg hover:shadow-lg transition-all transform hover:scale-105 font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="bi bi-search mr-2"></i>获取答案
                    </button>
                </div>

                <!-- 快速测试按钮 -->
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">快速测试示例：</p>
                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="testAPI('single')" class="px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="bi bi-ui-radios mr-1"></i>单选题
                        </button>
                        <button onclick="testAPI('multiple')" class="px-3 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="bi bi-ui-checks mr-1"></i>多选题
                        </button>
                        <button onclick="testAPI('judgement')" class="px-3 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="bi bi-check-circle mr-1"></i>判断题
                        </button>
                        <button onclick="testAPI('completion')" class="px-3 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-colors text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="bi bi-pencil mr-1"></i>填空题
                        </button>
                    </div>
                    <div id="test-result" class="mt-4"></div>
                </div>
            </div>

            <!-- 加载状态 -->
            <div id="loading" class="hidden mt-6 text-center">
                <div class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
                    <div class="animate-spin mr-3">
                        <i class="bi bi-arrow-repeat text-purple-600 text-xl"></i>
                    </div>
                    <span class="text-gray-700 dark:text-gray-300">AI正在思考...</span>
                </div>
            </div>

            <!-- 结果显示 -->
            <div id="result" class="hidden mt-6 bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                <div class="flex items-center mb-4">
                    <i class="bi bi-lightbulb-fill text-yellow-500 text-2xl mr-3"></i>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">AI回答</h3>
                </div>
                <div id="answer-content"></div>
            </div>
        </div>
    </div>

    <!-- 特性介绍 -->
    <div class="mt-16 grid md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 hover:shadow-lg transition-shadow">
            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mb-4">
                <i class="bi bi-lightning-charge text-purple-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">智能题库</h3>
            <p class="text-gray-600 dark:text-gray-400">自动构建题库，相同问题直接返回答案，无需重复调用AI</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 hover:shadow-lg transition-shadow">
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mb-4">
                <i class="bi bi-shield-check text-green-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">安全可靠</h3>
            <p class="text-gray-600 dark:text-gray-400">AES-256加密传输，支持高并发访问，数据安全有保障</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 hover:shadow-lg transition-shadow">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mb-4">
                <i class="bi bi-plug text-blue-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">易于集成</h3>
            <p class="text-gray-600 dark:text-gray-400">完全兼容OCS题库接口，支持配置导入导出，即插即用</p>
        </div>
    </div>
</div>

<!-- 页脚 -->
<footer class="mt-20 border-t border-gray-200 dark:border-gray-800">
    <div class="container mx-auto px-4 py-8">
        <div class="text-center">
            <p class="text-gray-600 dark:text-gray-400">
                <span class="font-semibold">QuizMind AI</span> v<?php echo $config['version']; ?>
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">
                Powered by OpenAI · Built with <i class="bi bi-heart-fill text-red-500"></i>
            </p>
        </div>
    </div>
</footer>

<script>
    const BASE_URL = '<?php echo BASE_URL; ?>';

    function toggleMobileMenu() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    }

    function togglePasswordVisibility(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '-toggle');

        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }

    function showImportModal() {
        document.getElementById('importModal').classList.remove('hidden');
    }

    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
        document.getElementById('import-config-text').value = '';
        document.getElementById('import-error').classList.add('hidden');
    }

    function scrollToConfig() {
        document.getElementById('config').scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    // 点击模态框外部关闭
    document.getElementById('importModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeImportModal();
        }
    });
</script>
<script src="assets/script.js"></script>
</body>
</html>