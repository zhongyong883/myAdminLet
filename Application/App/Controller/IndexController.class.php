<?php

/**
 * 外部框架页面
 */

namespace App\Controller;
use Common\Controller\UBaseController;

class IndexController extends UBaseController {

    public function index(){
        $this->display("Index:index");
    }
    
    public function welcome(){
        echo "欢迎页面"; 
    }

}
