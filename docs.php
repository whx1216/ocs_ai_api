<?php
require_once __DIR__ . '/lib/config.php';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuizMind AI - 使用文档</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@unocss/reset/tailwind.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@unocss/runtime"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        [un-cloak] {
            display: none;
        }

        .gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .code-block {
            background: #1e293b;
            color: #e2e8f0;
            padding: 1rem;
            border-radius: 0.5rem;
            overflow-x: auto;
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 0.875rem;
            line-height: 1.5;
        }

        .inline-code {
            background: #e5e7eb;
            padding: 0.125rem 0.375rem;
            border-radius: 0.25rem;
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 0.875em;
        }

        .dark .inline-code {
            background: #374151;
        }

        /* 目录固定 */
        @media (min-width: 1024px) {
            .toc {
                position: sticky;
                top: 5rem;
                max-height: calc(100vh - 6rem);
                overflow-y: auto;
            }
        }

        .toc::-webkit-scrollbar {
            width: 4px;
        }

        .toc::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 2px;
        }

        .dark .toc::-webkit-scrollbar-thumb {
            background: #4b5563;
        }

        /* 视频教程样式 */
        .video-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            border-radius: 0.5rem;
            background: #000;
        }

        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        /* 步骤卡片 */
        .step-card {
            position: relative;
            padding-left: 3rem;
        }

        .step-card::before {
            content: attr(data-step);
            position: absolute;
            left: 0;
            top: 0.25rem;
            width: 2rem;
            height: 2rem;
            background: #7c3aed;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
    </style>
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
                <a href="dashboard.php"
                   class="text-gray-600 hover:text-purple-600 transition-colors flex items-center gap-2">
                    <i class="bi bi-graph-up"></i>
                    <span>统计面板</span>
                </a>
                <a href="docs.php" class="text-purple-600 font-medium flex items-center gap-2">
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
            <a href="docs.php" class="block py-2 text-purple-600 font-medium">
                <i class="bi bi-book mr-2"></i>使用文档
            </a>
        </div>
    </div>
</nav>

<div class="container mx-auto px-4 py-8 max-w-7xl">
    <div class="lg:grid lg:grid-cols-4 lg:gap-8">
        <!-- 左侧目录 -->
        <aside class="hidden lg:block">
            <div class="toc bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">目录</h3>
                <nav class="space-y-2">
                    <div class="pb-2 mb-2 border-b border-gray-200 dark:border-gray-700">
                        <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-2">使用教程</p>
                        <a href="#quick-guide"
                           class="block text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 py-1">快速上手</a>
                        <a href="#generate-config"
                           class="block text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 py-1 pl-4">生成配置</a>
                        <a href="#import-config"
                           class="block text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 py-1 pl-4">导入配置</a>
                        <a href="#ocs-integration"
                           class="block text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 py-1">OCS集成</a>
                        <a href="#test-usage"
                           class="block text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 py-1">测试使用</a>
                        <a href="#advanced-features"
                           class="block text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 py-1">高级功能</a>
                    </div>
                    <div class="pb-2 mb-2 border-b border-gray-200 dark:border-gray-700">
                        <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-2">部署指南</p>
                        <a href="#deployment"
                           class="block text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 py-1">部署要求</a>
                        <a href="#installation"
                           class="block text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 py-1">安装步骤</a>
                        <a href="#configuration"
                           class="block text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 py-1">配置说明</a>
                        <a href="#security"
                           class="block text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 py-1">安全设置</a>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-2">参考</p>
                        <a href="#api-reference"
                           class="block text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 py-1">API文档</a>
                        <a href="#faq"
                           class="block text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 py-1">常见问题</a>
                        <a href="#troubleshooting"
                           class="block text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 py-1">故障排除</a>
                    </div>
                </nav>
            </div>
        </aside>

        <!-- 主内容 -->
        <main class="lg:col-span-3">
            <div class="prose prose-gray dark:prose-invert max-w-none">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">使用文档</h1>

                <!-- 快速上手 -->
                <section id="quick-guide" class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                        <i class="bi bi-rocket text-purple-600 mr-2"></i>快速上手
                    </h2>

                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl p-6 mb-6">
                        <p class="text-lg text-gray-700 dark:text-gray-300">
                            只需 <strong>3 分钟</strong>，即可将 QuizMind AI 集成到您的 OCS 题库中！
                        </p>
                    </div>

                    <div class="grid md:grid-cols-3 gap-4 mb-8">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition-shadow">
                            <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="bi bi-gear-fill text-purple-600 text-2xl"></i>
                            </div>
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-2">1. 配置API</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">填写 OpenAI API Key</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition-shadow">
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="bi bi-file-earmark-code text-green-600 text-2xl"></i>
                            </div>
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-2">2. 生成配置</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">一键生成 OCS 配置</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition-shadow">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="bi bi-plug text-blue-600 text-2xl"></i>
                            </div>
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-2">3. 添加到OCS</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">粘贴配置即可使用</p>
                        </div>
                    </div>
                </section>

                <!-- 生成配置 -->
                <section id="generate-config" class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                        <i class="bi bi-file-earmark-code text-purple-600 mr-2"></i>生成配置
                    </h2>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">配置步骤</h3>

                            <div class="space-y-6">
                                <div class="step-card" data-step="1">
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">填写 API Key</h4>
                                    <p class="text-gray-700 dark:text-gray-300 mb-3">
                                        访问 QuizMind AI 主页，在"配置生成器"中填写您的 OpenAI API Key。
                                    </p>
                                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                        <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                            <i class="bi bi-info-circle mr-1"></i>
                                            <strong>获取 API Key：</strong>登录 <a
                                                    href="https://platform.openai.com/api-keys" target="_blank"
                                                    class="underline">OpenAI 控制台</a>，创建新的 API Key。
                                        </p>
                                    </div>
                                </div>

                                <div class="step-card" data-step="2">
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">选择模型</h4>
                                    <p class="text-gray-700 dark:text-gray-300 mb-3">
                                        在"模型列表"中选择要使用的 AI 模型，支持多个模型轮询。
                                    </p>
                                    <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4">
                                        <p class="text-sm font-mono text-gray-800 dark:text-gray-200">
                                            推荐配置：<br>
                                            gpt-4o-mini （速度快，成本低）<br>
                                            o3-mini （效果好，成本高）
                                        </p>
                                    </div>
                                </div>

                                <div class="step-card" data-step="3">
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">高级选项</h4>
                                    <ul class="list-disc list-inside space-y-2 text-gray-700 dark:text-gray-300">
                                        <li><strong>API Base URL</strong>：使用第三方服务时修改</li>
                                        <li><strong>访问令牌</strong>：增加额外的安全验证</li>
                                        <li><strong>缓存功能</strong>：避免重复调用，节省成本</li>
                                        <li><strong>题库收集</strong>：自动构建本地题库</li>
                                    </ul>
                                </div>

                                <div class="step-card" data-step="4">
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">生成配置</h4>
                                    <p class="text-gray-700 dark:text-gray-300 mb-3">
                                        点击"生成OCS配置"按钮，系统会自动生成加密的配置文件。
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- 导入配置 -->
                <section id="import-config" class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                        <i class="bi bi-cloud-download text-purple-600 mr-2"></i>导入已有配置
                    </h2>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                        <p class="text-gray-700 dark:text-gray-300 mb-4">
                            如果您之前已经生成过配置，可以直接导入使用：
                        </p>

                        <ol class="list-decimal list-inside space-y-3 text-gray-700 dark:text-gray-300">
                            <li>点击"导入配置"按钮</li>
                            <li>粘贴完整的 OCS 配置（JSON 格式）</li>
                            <li>系统会自动解析并填充表单</li>
                            <li>可以修改配置后重新生成</li>
                        </ol>

                        <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                            <p class="text-sm text-blue-800 dark:text-blue-200">
                                <i class="bi bi-lightbulb mr-1"></i>
                                <strong>提示：</strong>导入配置后，您的 API Key 会自动解密并填充到表单中，方便修改。
                            </p>
                        </div>
                    </div>
                </section>

                <!-- OCS集成 -->
                <section id="ocs-integration" class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                        <i class="bi bi-puzzle text-purple-600 mr-2"></i>OCS 集成教程
                    </h2>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">详细步骤</h3>

                            <div class="space-y-6">
                                <div class="step-card" data-step="1">
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">复制配置</h4>
                                    <p class="text-gray-700 dark:text-gray-300 mb-3">
                                        生成配置后，点击"复制"按钮，复制完整的 JSON 配置。
                                    </p>
                                    <pre class="code-block">[{
    "name": "QuizMind AI智能题库",
    "homepage": "https://your-domain.com/quizmind-ai/",
    "url": "https://your-domain.com/quizmind-ai/api/search.php",
    "method": "get",
    "type": "GM_xmlhttpRequest",
    "contentType": "json",
    "data": {
        "title": "${title}",
        "type": "${type}",
        "options": "${options}",
        "config": "加密的配置字符串...",
        "allow_collection": "true"
    },
    "handler": "return (res)=> res.code === 1 ? [res.question, res.answer] : [res.msg, undefined]"
}]</pre>
                                </div>

                                <div class="step-card" data-step="2">
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">打开 OCS 设置</h4>
                                    <p class="text-gray-700 dark:text-gray-300 mb-3">
                                        在 OCS 扩展中，点击设置图标，找到"自定义题库"选项。
                                    </p>
                                </div>

                                <div class="step-card" data-step="3">
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">添加题库</h4>
                                    <p class="text-gray-700 dark:text-gray-300 mb-3">
                                        将复制的配置粘贴到"自定义题库"文本框中，点击保存。
                                    </p>
                                </div>

                                <div class="step-card" data-step="4">
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">测试连接</h4>
                                    <p class="text-gray-700 dark:text-gray-300 mb-3">
                                        使用 OCS 的测试功能，验证题库是否正常工作。
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>
                </section>

                <!-- 测试使用 -->
                <section id="test-usage" class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                        <i class="bi bi-play-circle text-purple-600 mr-2"></i>测试使用
                    </h2>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                                <i class="bi bi-browser-chrome text-blue-600 mr-2"></i>网页测试
                            </h3>
                            <p class="text-gray-700 dark:text-gray-300 mb-4">
                                在 QuizMind AI 主页右侧的"在线测试"工具中：
                            </p>
                            <ol class="list-decimal list-inside space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                <li>输入问题内容</li>
                                <li>选择题目类型（可选）</li>
                                <li>输入选项内容（可选）</li>
                                <li>点击"获取答案"</li>
                            </ol>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                                <i class="bi bi-puzzle text-purple-600 mr-2"></i>OCS 测试
                            </h3>
                            <p class="text-gray-700 dark:text-gray-300 mb-4">
                                在 OCS 中使用快捷键或菜单：
                            </p>
                            <ul class="list-disc list-inside space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                <li>选中题目文本</li>
                                <li>右键选择"搜索答案"</li>
                                <li>选择"QuizMind AI智能题库"</li>
                                <li>等待 AI 返回答案</li>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                        <p class="text-green-800 dark:text-green-200">
                            <i class="bi bi-check-circle mr-2"></i>
                            <strong>快速测试：</strong>使用主页提供的示例题目，一键测试各种题型。
                        </p>
                    </div>
                </section>

                <!-- 高级功能 -->
                <section id="advanced-features" class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                        <i class="bi bi-gear-wide-connected text-purple-600 mr-2"></i>高级功能
                    </h2>

                    <div class="space-y-6">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                                <i class="bi bi-database text-blue-600 mr-2"></i>题库管理
                            </h3>
                            <p class="text-gray-700 dark:text-gray-300 mb-4">
                                访问"统计面板"可以查看和管理题库：
                            </p>
                            <ul class="list-disc list-inside space-y-2 text-gray-700 dark:text-gray-300">
                                <li>浏览所有收集的题目</li>
                                <li>按类型和关键词搜索</li>
                                <li>查看题型分布统计</li>
                                <li>导出题库为 JSON 或 CSV 格式</li>
                            </ul>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                                <i class="bi bi-speedometer2 text-green-600 mr-2"></i>性能优化
                            </h3>
                            <ul class="list-disc list-inside space-y-2 text-gray-700 dark:text-gray-300">
                                <li><strong>启用缓存</strong>：相同问题 24 小时内不会重复调用 AI</li>
                                <li><strong>题库收集</strong>：已有答案的题目直接返回，响应时间 < 100ms</li>
                                <li><strong>多模型轮询</strong>：主模型失败时自动切换备用模型</li>
                                <li><strong>并发控制</strong>：文件锁机制确保高并发下的数据一致性</li>
                            </ul>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                                <i class="bi bi-shield-lock text-red-600 mr-2"></i>安全特性
                            </h3>
                            <ul class="list-disc list-inside space-y-2 text-gray-700 dark:text-gray-300">
                                <li><strong>配置加密</strong>：API Key 使用 AES-256 加密传输</li>
                                <li><strong>访问令牌</strong>：可选的额外认证层</li>
                                <li><strong>请求验证</strong>：防止恶意请求和爬虫</li>
                                <li><strong>日志记录</strong>：完整的访问和错误日志</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- 部署指南 -->
                <section id="deployment" class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                        <i class="bi bi-server text-purple-600 mr-2"></i>部署要求
                    </h2>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">系统要求</h3>
                        <ul class="list-disc list-inside space-y-2 text-gray-700 dark:text-gray-300">
                            <li>PHP 7.4 或更高版本</li>
                            <li>OpenSSL 扩展（用于加密）</li>
                            <li>cURL 扩展（用于 API 调用）</li>
                            <li>JSON 扩展（用于数据处理）</li>
                            <li>可写的文件系统（用于缓存和题库）</li>
                        </ul>

                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 mt-6">推荐配置</h3>
                        <ul class="list-disc list-inside space-y-2 text-gray-700 dark:text-gray-300">
                            <li>PHP 8.0+（更好的性能）</li>
                            <li>HTTPS 证书（安全传输）</li>
                            <li>Redis/Memcached（可选，高级缓存）</li>
                            <li>CDN 加速（可选，静态资源）</li>
                        </ul>
                    </div>
                </section>

                <!-- 安装步骤 -->
                <section id="installation" class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                        <i class="bi bi-download text-purple-600 mr-2"></i>安装步骤
                    </h2>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                        <ol class="list-decimal list-inside space-y-4 text-gray-700 dark:text-gray-300">
                            <li>
                                <strong>下载代码</strong>
                                <pre class="code-block mt-2">git clone https://github.com/yourusername/quizmind-ai.git
cd quizmind-ai</pre>
                            </li>
                            <li>
                                <strong>设置权限</strong>
                                <pre class="code-block mt-2">chmod -R 755 .
chmod -R 777 cache/ data/ logs/</pre>
                            </li>
                            <li>
                                <strong>配置 Web 服务器</strong>
                                <p class="mt-2">Apache 已包含 .htaccess，Nginx 需要添加重写规则。</p>
                            </li>
                            <li>
                                <strong>访问安装</strong>
                                <p class="mt-2">打开浏览器访问您的域名即可开始使用。</p>
                            </li>
                        </ol>
                    </div>
                </section>

                <!-- 配置说明 -->
                <section id="configuration" class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                        <i class="bi bi-sliders text-purple-600 mr-2"></i>配置说明
                    </h2>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                        <p class="text-gray-700 dark:text-gray-300 mb-4">
                            主要配置文件位于 <code class="inline-code">lib/config.php</code>：
                        </p>

                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">基础配置</h4>
                        <pre class="code-block mb-4">$defaultConfig = [
    'version' => '2.2.0',          // 版本号
    'base_path' => '/quizmind-ai', // 部署路径
    'debug' => false,              // 调试模式
    'cache_expiration' => 86400,   // 缓存时间（秒）
    'log_level' => 'INFO',         // 日志级别
];</pre>

                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">高级配置</h4>
                        <ul class="list-disc list-inside space-y-2 text-gray-700 dark:text-gray-300">
                            <li><code class="inline-code">timeout</code>：API 请求超时时间</li>
                            <li><code class="inline-code">max_retries</code>：失败重试次数</li>
                            <li><code class="inline-code">rate_limit</code>：请求频率限制</li>
                        </ul>
                    </div>
                </section>

                <!-- 安全设置 -->
                <section id="security" class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                        <i class="bi bi-shield-lock text-purple-600 mr-2"></i>安全设置
                    </h2>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-6">
                            <p class="text-red-800 dark:text-red-200">
                                <i class="bi bi-exclamation-triangle mr-2"></i>
                                <strong>重要：</strong>生产环境部署前，请务必修改默认的加密密钥！
                            </p>
                        </div>

                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">修改加密密钥</h4>
                        <p class="text-gray-700 dark:text-gray-300 mb-2">
                            编辑 <code class="inline-code">lib/config.php</code> 文件：
                        </p>
                        <pre class="code-block mb-4">function encryptConfig($config) {
    $key = 'YOUR_NEW_32_CHAR_SECRET_KEY_HERE'; // 32位密钥
    // ...
}

function decryptConfig($encryptedData) {
    $key = 'YOUR_NEW_32_CHAR_SECRET_KEY_HERE'; // 保持一致
    // ...
}</pre>

                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2 mt-6">其他安全建议</h4>
                        <ul class="list-disc list-inside space-y-2 text-gray-700 dark:text-gray-300">
                            <li>使用 HTTPS 协议部署</li>
                            <li>设置强密码的访问令牌</li>
                            <li>定期备份题库数据</li>
                            <li>限制文件上传权限</li>
                            <li>启用 Web 应用防火墙</li>
                            <li>定期更新 PHP 和依赖库</li>
                        </ul>
                    </div>
                </section>

                <!-- API文档 -->
                <section id="api-reference" class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                        <i class="bi bi-code-square text-purple-600 mr-2"></i>API 文档
                    </h2>

                    <div class="space-y-6">
                        <!-- 搜索接口 -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">搜索接口</h3>

                            <div class="mb-4">
                                <span class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">GET</span>
                                <code class="inline-code ml-2">/api/search.php</code>
                            </div>

                            <h4 class="font-semibold text-gray-900 dark:text-white mb-2">请求参数</h4>
                            <div class="overflow-x-auto mb-4">
                                <table class="w-full text-sm">
                                    <thead>
                                    <tr class="border-b border-gray-200 dark:border-gray-700">
                                        <th class="text-left py-2">参数</th>
                                        <th class="text-left py-2">类型</th>
                                        <th class="text-left py-2">必需</th>
                                        <th class="text-left py-2">说明</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr class="border-b border-gray-100 dark:border-gray-700">
                                        <td class="py-2"><code class="inline-code">title</code></td>
                                        <td>string</td>
                                        <td>是</td>
                                        <td>问题内容</td>
                                    </tr>
                                    <tr class="border-b border-gray-100 dark:border-gray-700">
                                        <td class="py-2"><code class="inline-code">type</code></td>
                                        <td>string</td>
                                        <td>否</td>
                                        <td>题目类型</td>
                                    </tr>
                                    <tr class="border-b border-gray-100 dark:border-gray-700">
                                        <td class="py-2"><code class="inline-code">options</code></td>
                                        <td>string</td>
                                        <td>否</td>
                                        <td>选项内容</td>
                                    </tr>
                                    <tr class="border-b border-gray-100 dark:border-gray-700">
                                        <td class="py-2"><code class="inline-code">config</code></td>
                                        <td>string</td>
                                        <td>是</td>
                                        <td>加密配置</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <h4 class="font-semibold text-gray-900 dark:text-white mb-2">响应示例</h4>
                            <pre class="code-block">{
    "code": 1,
    "question": "中国的首都是哪个城市？",
    "answer": "北京"
}</pre>
                        </div>

                        <!-- 其他接口简介 -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">其他接口</h3>
                            <ul class="space-y-3">
                                <li>
                                    <span class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">GET</span>
                                    <code class="inline-code ml-2">/api/stats.php</code>
                                    <span class="text-gray-600 dark:text-gray-400 ml-2">- 获取系统统计信息</span>
                                </li>
                                <li>
                                    <span class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">GET</span>
                                    <code class="inline-code ml-2">/api/export.php</code>
                                    <span class="text-gray-600 dark:text-gray-400 ml-2">- 导出题库数据</span>
                                </li>
                                <li>
                                    <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">POST</span>
                                    <code class="inline-code ml-2">/api/encrypt_config.php</code>
                                    <span class="text-gray-600 dark:text-gray-400 ml-2">- 加密配置</span>
                                </li>
                                <li>
                                    <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">POST</span>
                                    <code class="inline-code ml-2">/api/decrypt_config.php</code>
                                    <span class="text-gray-600 dark:text-gray-400 ml-2">- 解密配置</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- 常见问题 -->
                <section id="faq" class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                        <i class="bi bi-question-circle text-purple-600 mr-2"></i>常见问题
                    </h2>

                    <div class="space-y-4">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-2">
                                Q: API Key 安全吗？
                            </h3>
                            <p class="text-gray-700 dark:text-gray-300">
                                A: 是的，API Key 在传输和存储时都经过 AES-256 加密，即使配置被截获也无法解密。
                            </p>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-2">
                                Q: 如何降低 API 成本？
                            </h3>
                            <p class="text-gray-700 dark:text-gray-300">
                                A: 启用缓存和题库收集功能，相同问题不会重复调用 AI。使用 gpt-3.5-turbo 模型成本更低。
                            </p>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-2">
                                Q: 支持哪些题目类型？
                            </h3>
                            <p class="text-gray-700 dark:text-gray-300">
                                A: 支持单选题、多选题、判断题、填空题。系统会自动识别题型，也可以手动指定。
                            </p>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-2">
                                Q: 如何备份题库？
                            </h3>
                            <p class="text-gray-700 dark:text-gray-300">
                                A: 访问统计面板，使用导出功能可以下载 JSON 或 CSV 格式的题库备份。
                            </p>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-2">
                                Q: 可以使用其他 AI 模型吗？
                            </h3>
                            <p class="text-gray-700 dark:text-gray-300">
                                A: 可以，只要兼容 OpenAI API 格式即可。修改 API Base URL 为第三方服务地址。
                            </p>
                        </div>
                    </div>
                </section>

                <!-- 故障排除 -->
                <section id="troubleshooting" class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                        <i class="bi bi-tools text-purple-600 mr-2"></i>故障排除
                    </h2>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">常见错误及解决方案</h3>

                        <div class="space-y-4">
                            <div class="border-l-4 border-red-500 pl-4">
                                <h4 class="font-semibold text-red-700 dark:text-red-400">API 调用失败</h4>
                                <ul class="list-disc list-inside mt-2 text-gray-700 dark:text-gray-300 text-sm">
                                    <li>检查 API Key 是否正确</li>
                                    <li>确认余额充足</li>
                                    <li>验证网络连接</li>
                                    <li>查看错误日志：<code class="inline-code">logs/error.log</code></li>
                                </ul>
                            </div>

                            <div class="border-l-4 border-yellow-500 pl-4">
                                <h4 class="font-semibold text-yellow-700 dark:text-yellow-400">配置无法保存</h4>
                                <ul class="list-disc list-inside mt-2 text-gray-700 dark:text-gray-300 text-sm">
                                    <li>检查目录权限：<code class="inline-code">chmod 777 data/</code></li>
                                    <li>确认磁盘空间充足</li>
                                    <li>验证 PHP 配置</li>
                                </ul>
                            </div>

                            <div class="border-l-4 border-blue-500 pl-4">
                                <h4 class="font-semibold text-blue-700 dark:text-blue-400">OCS 无法连接</h4>
                                <ul class="list-disc list-inside mt-2 text-gray-700 dark:text-gray-300 text-sm">
                                    <li>确认 URL 地址正确</li>
                                    <li>检查访问令牌设置</li>
                                    <li>查看浏览器控制台错误</li>
                                    <li>测试 API 端点：<code class="inline-code">curl YOUR_URL/api/search.php</code></li>
                                </ul>
                            </div>
                        </div>

                        <div class="mt-6 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                <i class="bi bi-lightbulb mr-1"></i>
                                <strong>调试模式：</strong>修改 <code class="inline-code">config.php</code> 中的 <code
                                        class="inline-code">'debug' => true</code> 可以查看详细错误信息。
                            </p>
                        </div>
                    </div>
                </section>

            </div>
        </main>
    </div>
</div>

<!-- 页脚 -->
<footer class="mt-20 border-t border-gray-200 dark:border-gray-800">
    <div class="container mx-auto px-4 py-8">
        <div class="text-center">
            <p class="text-gray-600 dark:text-gray-400">
                <span class="font-semibold">QuizMind AI</span> v<?php echo $config['version']; ?> · 使用文档
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">
                <a href="https://github.com/yourusername/quizmind-ai" class="hover:text-purple-600">
                    <i class="bi bi-github mr-1"></i>GitHub
                </a>
                <span class="mx-2">·</span>
                <a href="#" class="hover:text-purple-600">
                    <i class="bi bi-envelope mr-1"></i>联系支持
                </a>
            </p>
        </div>
    </div>
</footer>

<script>
    function toggleMobileMenu() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    }

    // 平滑滚动
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                const offset = 80; // 导航栏高度
                const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - offset;
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // 高亮当前章节
    window.addEventListener('scroll', function () {
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('.toc a');

        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop - 100;
            if (pageYOffset >= sectionTop) {
                current = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('text-purple-600', 'dark:text-purple-400', 'font-medium');
            if (link.getAttribute('href') === '#' + current) {
                link.classList.add('text-purple-600', 'dark:text-purple-400', 'font-medium');
            }
        });
    });
</script>
</body>
</html>