# QuizMind AI

QuizMind AI 是一个基于 OpenAI API 的智能题库系统,可以轻松集成到 OCS 中实现自动答题功能。

## 🚀 快速使用

### 无需部署 - 直接使用

**推荐方式：** 直接使用我们提供的服务，无需任何部署步骤！

- 🌐 **官方网站**: [https://whx1216.top/ocs_ai_api](https://whx1216.top/ocs_ai_api)
- 🔒 **隐私承诺**: 我们承诺保护您的隐私和数据安全
- ⚡ **即开即用**: 只需填写API Key即可生成OCS配置
- 🔄 **实时更新**: 自动享受最新功能和优化

### 自己部署 - 最高安全性

如果您对安全性有极高要求，推荐自己部署：

```bash
git clone https://github.com/whx1216/ocs_ai_api.git
cd quizmind-ai
chmod -R 755 .
chmod -R 777 cache/ data/ logs/
```

## ⚠️ 安全提醒

- ✅ **官方网站**: 我承诺保护用户隐私，所有数据加密处理。服务端不储存任何隐私。
- ✅ **自己部署**: 完全掌控数据，安全性最高
- ❌ **第三方部署**: 请谨慎使用他人部署的服务，可能存在安全风险

## 功能特性

- 🎯 支持多种题型：单选、多选、判断、填空
- 🧠 智能题型识别和答案解析
- 💾 本地题库缓存和收集
- 🔄 多模型轮询调用
- 📊 完整的统计分析
- 🔐 安全的配置加密

## 系统要求（仅自己部署需要）

- PHP 7.4+ (推荐 PHP 8.0+)
- OpenSSL 扩展
- cURL 扩展
- JSON 扩展
- 可写文件系统权限

## 使用教程

### 方式一：使用官方网站（推荐）

1. 访问 https://whx1216.top/ocs_ai_api
2. 在配置生成器中填写OpenAI API Key
3. 选择AI模型，配置高级选项
4. 点击"生成OCS配置"
5. 复制配置到OCS中使用

### 方式二：自己部署

1. 按照上面的部署步骤安装
2. 配置Web服务器
3. 访问您的域名开始使用

## 配置说明

主配置文件位于 `lib/config.php`:



php



```
$defaultConfig = [
    'version' => '2.2.0',          // 版本号
    'base_path' => '/quizmind-ai', // 部署路径 
    'debug' => false,              // 调试模式
    'cache_expiration' => 86400,   // 缓存时间（秒）
    'log_level' => 'INFO',         // 日志级别
];
```

## 安全配置（自己部署时必须修改）

请务必修改默认加密密钥:



php



```
function encryptConfig($config) {
    $key = 'YOUR_NEW_32_CHAR_SECRET_KEY_HERE'; // 32位密钥
    // ...
}
```

## API 文档

### 搜索接口

```
GET /api/search.php

参数:
- title: 问题内容(必需)
- type: 题目类型(可选) 
- options: 选项内容(可选)
- config: 加密配置(必需)
```

完整 API 文档请参考 [在线文档](https://whx1216.top/docs.php)。

## 许可证

本项目基于 [GNU General Public License v3.0](https://ai.whx1216.top/LICENSE) 开源。

#### 如需帮助,请:

- 查看 [常见问题](https://whx1216.top/docs.php#faq)
- 提交 [Issues](https://github.com/whx1216/ocs_ai_api/issues)
