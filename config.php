<?php
return [
    /*
     * 数据库配置
     */
    'db' => [
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'name' => env('DB_DATABASE', 'NAME'),
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', 'root'),
        'charset' => env('DB_CHARSET', 'utf8mb4'),
        'table' => env('DB_TABLE', 'url_shortener')
    ],

    /*
     * 站点配置
     */
    'options' => [
        'domain' => env('DOMAIN', 'DOMAIN') // 包括协议头 'http://' 或 'https://'
    ]
];
