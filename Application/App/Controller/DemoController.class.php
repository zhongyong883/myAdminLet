<?php

namespace App\Controller;

use Common\Controller\UBaseController;

class DemoController extends UBaseController {

    public function lists() {
        $this->display("Demo:lists");
    }

    public function edit() {
        $this->display("Demo:edit"); 
    }

}
