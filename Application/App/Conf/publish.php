<?php

/**
 * 正式环境配置文件
 */
$domain = '//www.logistics.com';
return array(
    'SHOW_PAGE_TRACE' => false, //关闭TRACE调试功能
    'DOMAIN' => $domain,
    //模板静态文件资源相关配置
    'TMPL_PARSE_STRING' => array(
        '__APP__' => $domain . '/Resources/app',
    )
);
