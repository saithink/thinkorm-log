<div style="padding:18px;max-width: 1024px;margin:0 auto;background-color:#fff;color:#333">
<h1>基于 webman 的ThinkOrm和 API日志记录</h1>

基于 <a href="https://www.workerman.net/webman" target="_blank">webman</a> 使用ThinkOrm时的日志记录工具，同时支持API请求日志记录
本插件基于 saithink/thinkorm-log 二次开发，由 guozhijian611 增强API日志功能

<h1>功能特性</h1>

- ThinkOrm SQL查询日志记录
- API请求响应日志记录
- 支持控制台输出和文件记录
- 可配置开关控制
- 美化的日志输出格式
- 支持忽略特定路径
- 统一的Bootstrap注册方式

<h1>安装</h1>

composer环境的安装命令如下

``` bash
composer require guozhijian611/thinkorm-log
```

安装之前确保已安装webman

<h1>配置文件</h1>

基础配置：<code>config/plugin/guozhijian611/thinkorm-log/app.php</code>

```php
return [
    // 是否启用日志记录
    'enable' => true,
    // 是否输出到控制台
    'console'   => true,
    // 是否记录到日志文件
    'file'  => false,
    
    // API日志配置
    'api_log' => [
        // 是否启用API日志
        'enable' => true,
        // 是否输出到控制台
        'console' => true,
        // 是否记录到日志文件
        'file' => false,
        // 忽略的路径（支持字符串匹配）
        'ignore_paths' => [
            'logs',
            'logview',
            '/logs',
            '/logview/'
        ]
    ]
];
```

<h1>日志输出示例</h1>

<h2>SQL日志</h2>
```
[2024-01-01 12:00:00]SELECT * FROM users WHERE id = 1 [0.001s]
```

<h2>API日志</h2>
```
┌────────────────────────2024-01-01 12:00:00───────────────────────────
│ 状态码: ✅ 200
│ 请求方式: POST
│ 接口: http://localhost:8787/api/user/login
│ 耗时: 25.67ms
│ 参数: {
│     "username": "admin",
│     "password": "123456"
│ }
│ 响应数据: {
│     "code": 0,
│     "msg": "登录成功",
│     "data": {
│         "token": "xxx"
│     }
│ }
└────────────────────────────────────────────────────
```

<h1>日志文件</h1>

- SQL日志文件：`runtime/logs/sql.log`
- API日志文件：`runtime/logs/api.log`

<h1>工作原理</h1>

插件通过 Bootstrap 类统一注册所有功能：
- SQL日志：通过 `Db::listen()` 监听数据库查询
- API日志：通过中间件配置文件注册全局中间件
- 配置控制：通过配置文件控制各功能的开关

<h1>作者</h1>

- 原作者：saithink
- 增强版作者：guozhijian611
- 项目地址：<a href="https://github.com/guozhijian611/thinkorm-log" target="_blank">https://github.com/guozhijian611/thinkorm-log</a>

</div>