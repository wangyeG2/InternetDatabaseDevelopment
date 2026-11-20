<?php
/**
 * PHP built-in server router:
 * 真实存在的静态文件直接由内置服务器返回，其余走 Yii 路由
 */
if (php_sapi_name() === 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if ($path !== '/' && is_file(__DIR__ . $path)) {
        return false;
    }
}
require __DIR__ . '/index.php';
