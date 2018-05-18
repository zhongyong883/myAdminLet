<?php

// 应用入口文件

if (version_compare(PHP_VERSION, '5.3.0', '<')) {
    die('require PHP > 5.3.0 !');
}

define('APP_DEBUG', true);
define('RUNTIME_PATH', './Runtime/');
define('HTML_PATH', './Html/');
define('APP_PATH', './Application/');
define('WEB_PATH', dirname(__FILE__));
//定义项目环境状态  dev:开发环境；publish:线上环境
define('APP_STATUS', 'dev');

/**
 * 需要自动生成新模块代码时开启设置
 */
define('BIND_MODULE','App');
require './ThinkPHP/ThinkPHP.php';
