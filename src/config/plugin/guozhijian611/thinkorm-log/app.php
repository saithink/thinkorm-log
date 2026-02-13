<?php

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
