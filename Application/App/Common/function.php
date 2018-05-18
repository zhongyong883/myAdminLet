<?php

function D_B($data) {
    echo "<pre>";
    var_export($data);
    echo "</pre>";
}

/**
 * 获取语言包列表
 * @param string $rule
 * @return type
 */
function getLangListByRule($rule = '') {
    $lang = L();
    $list = array();

    if (empty($rule)) {
        $rule = "/^[^_]/";
    }
    foreach ($lang as $key => $val) {
        if (preg_match($rule, $key) == 1) {
            $list[$key] = $val;
        }
    }
    return $list;
}
