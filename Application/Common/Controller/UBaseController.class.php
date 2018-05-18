<?php

/**
 * 公共模块—需要登录验证的基类控制器
 */

namespace Common\Controller;

class UBaseController extends BaseController {

    public $userInfo;

    public function _initialize() {
        parent::_initialize();
    }

}
