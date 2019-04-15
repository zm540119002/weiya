<?php
namespace app\index\controller;
class Test extends \common\controller\Base{
    /**首页
     */
    public function index(){
        return $this->fetch();
    }
}

