<?php

/**
 * 公共模块基础配置
 */
return array(
    /**
     * 允许访问的模块列表
     */
    'MODULE_ALLOW_LIST' => array('App'),
    /**
     * 默认模块
     */
    'DEFAULT_MODULE' => 'App',
    'URL_MODEL' => 2,
    /**
     * 日志设置
     */
    'LOG_RECORD' => true,
    'LOG_LEVEL' => 'EMERG,ALERT,CRIT,ERR',
    /**
     * 语言包设置
     */
    'LANG_SWITCH_ON' => true,
    'LANG_LIST' => 'zh-cn,en-us,zh-tw',
    'VAR_LANGUAGE' => 1
);
