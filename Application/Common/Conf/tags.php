<?php
/**
 * 开启多语言行为，从而支持自定义语言包
 */
return array(
    'app_begin' => array('Behavior\CheckLangBehavior'),
);