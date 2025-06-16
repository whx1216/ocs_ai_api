// 全局变量存储生成的配置
let generatedConfig = null;
let isImported = false;

document.addEventListener('DOMContentLoaded', function() {
    const searchBtn = document.getElementById('search-btn');
    const questionInput = document.getElementById('question');
    const typeSelect = document.getElementById('question-type');
    const optionsInput = document.getElementById('options');
    const loading = document.getElementById('loading');
    const result = document.getElementById('result');
    const answerContent = document.getElementById('answer-content');

    // 检查是否有配置
    function hasConfig() {
        return generatedConfig !== null;
    }

    // 更新按钮状态
    function updateButtonStates() {
        const hasConfigState = hasConfig();

        // 搜索按钮
        if (searchBtn) {
            searchBtn.disabled = !hasConfigState;
            if (!hasConfigState) {
                searchBtn.innerHTML = '<i class="bi bi-lock mr-2"></i>请先生成配置';
            } else {
                searchBtn.innerHTML = '<i class="bi bi-search mr-2"></i>获取答案';
            }
        }

        // 测试按钮
        const testButtons = document.querySelectorAll('[onclick^="testAPI"]');
        testButtons.forEach(btn => {
            btn.disabled = !hasConfigState;
        });

        // 更新提示信息
        const warningBox = document.getElementById('test-warning');
        if (warningBox) {
            if (hasConfigState) {
                warningBox.classList.add('hidden');
            } else {
                warningBox.classList.remove('hidden');
            }
        }

        // 更新配置状态显示
        updateConfigStatus();
    }

    // 更新配置状态显示
    function updateConfigStatus() {
        const statusDiv = document.getElementById('config-status');
        const statusText = document.getElementById('config-status-text');
        const updateBtn = document.getElementById('update-config-btn');

        if (hasConfig() && isImported) {
            statusDiv.classList.remove('hidden');
            statusDiv.classList.add('bg-green-50', 'dark:bg-green-900/20', 'border', 'border-green-200', 'dark:border-green-800');
            statusText.textContent = '已导入配置，您可以修改后重新生成';
            updateBtn.classList.remove('hidden');
        } else {
            statusDiv.classList.add('hidden');
            updateBtn.classList.add('hidden');
        }
    }

    // 初始化时更新按钮状态
    updateButtonStates();

    // 搜索按钮点击事件
    searchBtn?.addEventListener('click', async function() {
        if (!hasConfig()) {
            alert('请先在左侧生成配置！');
            return;
        }

        const question = questionInput.value.trim();

        if (!question) {
            alert('请输入问题内容');
            return;
        }

        // 显示加载动画
        loading.classList.remove('hidden');
        result.classList.add('hidden');

        try {
            // 准备参数
            const params = new URLSearchParams({
                title: question,
                type: typeSelect.value,
                options: optionsInput.value,
                config: generatedConfig,
                allow_collection: document.getElementById('data-collection')?.checked ?? true
            });

            const response = await fetch(`${BASE_URL}/api/search.php?${params}`);
            const data = await response.json();

            // 隐藏加载动画
            loading.classList.add('hidden');
            result.classList.remove('hidden');

            if (data.code === 1) {
                // 显示成功结果
                answerContent.innerHTML = `
                    <div class="mb-3">
                        <h6 class="text-sm text-gray-500 dark:text-gray-400">问题：</h6>
                        <p class="text-gray-900 dark:text-white">${escapeHtml(data.question)}</p>
                    </div>
                    <div>
                        <h6 class="text-sm text-gray-500 dark:text-gray-400">答案：</h6>
                        <p class="text-xl font-bold text-purple-600 dark:text-purple-400">${escapeHtml(data.answer)}</p>
                    </div>
                `;
                result.classList.remove('border-red-500');
            } else {
                // 显示错误信息
                answerContent.innerHTML = `
                    <div class="text-red-600 dark:text-red-400">
                        <i class="bi bi-exclamation-circle mr-2"></i>${escapeHtml(data.msg || '获取答案失败')}
                    </div>
                `;
                result.classList.add('border-red-500');
            }
        } catch (error) {
            // 显示网络错误
            loading.classList.add('hidden');
            result.classList.remove('hidden');
            answerContent.innerHTML = `
                <div class="text-red-600 dark:text-red-400">
                    <i class="bi bi-wifi-off mr-2"></i>网络请求失败：${escapeHtml(error.message)}
                </div>
            `;
            result.classList.add('border-red-500');
        }
    });

    // Enter键触发搜索
    questionInput?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (hasConfig()) {
                searchBtn.click();
            }
        }
    });

    // 将函数暴露到全局作用域
    window.updateButtonStates = updateButtonStates;
    window.hasConfig = hasConfig;
    window.updateConfigStatus = updateConfigStatus;
});

// 修复滚动函数 - 考虑导航栏高度
function scrollToConfig() {
    const element = document.getElementById('config');
    const navbarHeight = 64; // 导航栏高度 (h-16 = 4rem = 64px)
    const elementPosition = element.getBoundingClientRect().top + window.pageYOffset;
    const offsetPosition = elementPosition - navbarHeight - 20; // 额外20px间距

    window.scrollTo({
        top: offsetPosition,
        behavior: 'smooth'
    });
}

// 切换移动端菜单
function toggleMobileMenu() {
    const menu = document.getElementById('mobile-menu');
    menu.classList.toggle('hidden');
}

// 切换密码可见性
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

// 显示导入模态框
function showImportModal() {
    document.getElementById('importModal').classList.remove('hidden');
}

// 关闭导入模态框
function closeImportModal() {
    document.getElementById('importModal').classList.add('hidden');
    document.getElementById('import-config-text').value = '';
    document.getElementById('import-error').classList.add('hidden');
}

// 导入配置
async function importConfig() {
    const configText = document.getElementById('import-config-text').value.trim();
    const errorDiv = document.getElementById('import-error');
    const errorText = document.getElementById('import-error-text');

    if (!configText) {
        errorDiv.classList.remove('hidden');
        errorText.textContent = '请粘贴OCS配置';
        return;
    }

    try {
        // 解析JSON
        const ocsConfigArray = JSON.parse(configText);

        if (!Array.isArray(ocsConfigArray) || ocsConfigArray.length === 0) {
            throw new Error('配置格式错误，应为数组格式');
        }

        const ocsConfig = ocsConfigArray[0];

        // 检查必要字段
        if (!ocsConfig.data || !ocsConfig.data.config) {
            throw new Error('配置中缺少必要的config字段');
        }

        // 解密配置
        const response = await fetch(`${BASE_URL}/api/decrypt_config.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                config: ocsConfig.data.config
            })
        });

        const result = await response.json();

        if (!result.success) {
            throw new Error(result.message || '配置解密失败');
        }

        // 填充表单
        const config = result.config;
        document.getElementById('api-key').value = config.openai_api_key || '';
        document.getElementById('api-base').value = config.openai_api_base || 'https://api.openai.com/v1';
        document.getElementById('models').value = (config.openai_models || ['gpt-3.5-turbo']).join('\n');
        document.getElementById('access-token').value = config.access_token || '';
        document.getElementById('cache-enabled').checked = config.cache_enabled !== false;
        document.getElementById('data-collection').checked = config.data_collection !== false;

        // 保存配置
        generatedConfig = ocsConfig.data.config;
        isImported = true;

        // 更新状态
        if (window.updateButtonStates) {
            window.updateButtonStates();
        }

        // 显示当前OCS配置
        document.getElementById('ocs-config-display').textContent = JSON.stringify([ocsConfig], null, 2);
        document.getElementById('ocs-config-card').classList.remove('hidden');

        // 关闭模态框
        closeImportModal();

        // 显示成功提示
        const statusDiv = document.getElementById('config-status');
        const statusText = document.getElementById('config-status-text');
        statusDiv.classList.remove('hidden');
        statusDiv.classList.add('bg-green-50', 'dark:bg-green-900/20', 'border', 'border-green-200', 'dark:border-green-800');
        statusText.innerHTML = '<i class="bi bi-check-circle text-green-600 mr-1"></i>配置导入成功！';

        // 滚动到配置区域 - 使用修复后的函数
        setTimeout(() => {
            scrollToConfig();
        }, 100);

    } catch (error) {
        errorDiv.classList.remove('hidden');
        errorText.textContent = error.message;
    }
}

// 生成配置
async function generateConfig(isUpdate = false) {
    const apiKey = document.getElementById('api-key').value.trim();
    const apiBase = document.getElementById('api-base').value.trim();
    const models = document.getElementById('models').value.trim().split('\n').filter(m => m.trim());
    const accessToken = document.getElementById('access-token').value.trim();
    const cacheEnabled = document.getElementById('cache-enabled').checked;
    const dataCollection = document.getElementById('data-collection').checked;

    if (!apiKey) {
        alert('请输入OpenAI API Key');
        return;
    }

    if (models.length === 0) {
        alert('请至少输入一个模型名称');
        return;
    }

    // 构建配置对象
    const config = {
        openai_api_key: apiKey,
        openai_api_base: apiBase || 'https://api.openai.com/v1',
        openai_models: models,
        access_token: accessToken,
        cache_enabled: cacheEnabled,
        data_collection: dataCollection
    };

    // 显示生成中状态
    const generateBtn = event.target || document.querySelector('[onclick="generateConfig()"]');
    const originalText = generateBtn.innerHTML;
    generateBtn.disabled = true;
    generateBtn.innerHTML = '<i class="bi bi-hourglass-split mr-2"></i>生成中...';

    try {
        const response = await fetch(`${BASE_URL}/api/encrypt_config.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(config)
        });

        const data = await response.json();
        if (data.success) {
            // 保存配置到全局变量
            generatedConfig = data.encrypted;
            isImported = false; // 标记为新生成的配置

            // 生成OCS配置
            const ocsConfig = {
                name: "QuizMind AI智能题库",
                homepage: `${BASE_URL}/`,
                url: `${BASE_URL}/api/search.php`,
                method: "get",
                type: "GM_xmlhttpRequest", // 使用 GM_xmlhttpRequest 处理跨域
                contentType: "json",
                data: {
                    title: "${title}",
                    type: "${type}",
                    options: "${options}",
                    config: data.encrypted,
                    allow_collection: dataCollection ? "true" : "false"
                },
                handler: "return (res)=> res.code === 1 ? [res.question, res.answer] : [res.msg, undefined]"
            };

            // 如果设置了访问令牌，添加到data中
            if (accessToken) {
                ocsConfig.data.token = accessToken;
            }

            // 显示配置
            const configDisplay = document.getElementById('ocs-config-display');
            configDisplay.textContent = JSON.stringify([ocsConfig], null, 2);
            document.getElementById('ocs-config-card').classList.remove('hidden');

            // 检测是否需要横向滚动并添加提示
            setTimeout(() => {
                const codeBlock = document.getElementById('ocs-config-display');
                if (codeBlock.scrollWidth > codeBlock.clientWidth) {
                    console.log('提示：配置内容较长，可以横向滚动查看完整内容');
                }
            }, 100);

            // 更新按钮状态
            if (window.updateButtonStates) {
                window.updateButtonStates();
            }

            // 滚动到配置区域 - 考虑导航栏高度
            setTimeout(() => {
                const ocsCard = document.getElementById('ocs-config-card');
                const navbarHeight = 64;
                const elementPosition = ocsCard.getBoundingClientRect().top + window.pageYOffset;
                const offsetPosition = elementPosition - navbarHeight - 20;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }, 100);

            // 显示成功消息
            generateBtn.innerHTML = isUpdate ? '<i class="bi bi-check-circle mr-2"></i>配置已更新！' : '<i class="bi bi-check-circle mr-2"></i>生成成功！';
            setTimeout(() => {
                generateBtn.innerHTML = originalText;
                generateBtn.disabled = false;
            }, 2000);
        } else {
            throw new Error(data.message || '配置生成失败');
        }
    } catch (error) {
        alert('配置生成失败：' + error.message);
        generateBtn.innerHTML = originalText;
        generateBtn.disabled = false;
    }
}

// 复制配置
function copyConfig() {
    const config = document.getElementById('ocs-config-display').textContent;

    // 创建临时文本框
    const textarea = document.createElement('textarea');
    textarea.value = config;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);

    // 选择并复制
    textarea.select();
    textarea.setSelectionRange(0, 99999); // 移动设备兼容

    try {
        document.execCommand('copy');

        // 更新按钮状态
        const copyBtn = event.target.closest('button');
        const originalText = copyBtn.innerHTML;
        copyBtn.innerHTML = '<i class="bi bi-check mr-2"></i>已复制！';
        copyBtn.classList.remove('bg-purple-600', 'hover:bg-purple-700');
        copyBtn.classList.add('bg-green-600');

        setTimeout(() => {
            copyBtn.innerHTML = originalText;
            copyBtn.classList.remove('bg-green-600');
            copyBtn.classList.add('bg-purple-600', 'hover:bg-purple-700');
        }, 2000);
    } catch (err) {
        alert('复制失败，请手动复制');
    }

    // 清理
    document.body.removeChild(textarea);
}

// 测试API函数
async function testAPI(type) {
    if (!generatedConfig) {
        alert('请先生成配置！');
        return;
    }

    const testCases = {
        single: {
            title: '中国的首都是哪个城市？',
            type: 'single',
            options: 'A. 上海\nB. 北京\nC. 广州\nD. 深圳'
        },
        multiple: {
            title: '以下哪些是中国的四大发明？',
            type: 'multiple',
            options: 'A. 造纸术\nB. 印刷术\nC. 火药\nD. 指南针\nE. 蒸汽机\nF. 电灯'
        },
        judgement: {
            title: '地球是太阳系中第三颗行星。',
            type: 'judgement',
            options: ''
        },
        completion: {
            title: '中国的四大名著包括《红楼梦》、《西游记》、《水浒传》和《_______》。',
            type: 'completion',
            options: ''
        }
    };

    const testCase = testCases[type];
    if (!testCase) return;

    const resultDiv = document.getElementById('test-result');
    resultDiv.innerHTML = '<div class="flex items-center text-gray-600"><div class="animate-spin mr-2"><i class="bi bi-arrow-repeat"></i></div>正在测试...</div>';

    try {
        const params = new URLSearchParams(testCase);
        params.append('config', generatedConfig);
        params.append('allow_collection', document.getElementById('data-collection')?.checked ?? true);

        const response = await fetch(`${BASE_URL}/api/search.php?${params}`);
        const data = await response.json();

        if (data.code === 1) {
            resultDiv.innerHTML = `
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="bi bi-check-circle-fill text-green-500 mr-2 mt-1"></i>
                        <div class="flex-1">
                            <p class="font-medium text-green-900 dark:text-green-100">测试成功！</p>
                            <p class="text-sm text-green-700 dark:text-green-300 mt-1">
                                <span class="font-medium">问题：</span>${escapeHtml(data.question)}
                            </p>
                            <p class="text-sm text-green-700 dark:text-green-300 mt-1">
                                <span class="font-medium">答案：</span><span class="font-bold text-green-800 dark:text-green-200">${escapeHtml(data.answer)}</span>
                            </p>
                        </div>
                    </div>
                </div>
            `;
        } else {
            resultDiv.innerHTML = `
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="bi bi-x-circle-fill text-red-500 mr-2 mt-1"></i>
                        <div class="flex-1">
                            <p class="font-medium text-red-900 dark:text-red-100">测试失败</p>
                            <p class="text-sm text-red-700 dark:text-red-300 mt-1">${escapeHtml(data.msg)}</p>
                        </div>
                    </div>
                </div>
            `;
        }
    } catch (error) {
        resultDiv.innerHTML = `
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                <div class="flex items-start">
                    <i class="bi bi-wifi-off text-red-500 mr-2 mt-1"></i>
                    <div class="flex-1">
                        <p class="font-medium text-red-900 dark:text-red-100">网络错误</p>
                        <p class="text-sm text-red-700 dark:text-red-300 mt-1">${escapeHtml(error.message)}</p>
                    </div>
                </div>
            </div>
        `;
    }
}

// HTML转义函数
function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, m => map[m]);
}

// 调试函数
function debugConfig() {
    console.log('Current config:', generatedConfig);
    console.log('Has config:', !!generatedConfig);
    console.log('Is imported:', isImported);
}

// 点击模态框外部关闭 - 添加到 DOMContentLoaded 外面
document.addEventListener('DOMContentLoaded', function() {
    const importModal = document.getElementById('importModal');
    if (importModal) {
        importModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeImportModal();
            }
        });
    }
});