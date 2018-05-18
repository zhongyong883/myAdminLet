<?php

/**
 * 开发环境配置文件
 */
$domain = '//www.logistics.com';
return array(
    'DOMAIN' => $domain,
    /**
     * 模板静态文件资源
     */
    'TMPL_PARSE_STRING' => array(
        '__APP__' => $domain . '/Resources/app',
    ),
    /**
     * 使用系统默认异常页面
     */
    'TMPL_EXCEPTION_FILE' => THINK_PATH . 'Tpl/think_exception.tpl',
    /**
     * TRACE调试功能
     */
    'SHOW_PAGE_TRACE' => false,
);
