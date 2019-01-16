<?php
namespace app\index\controller;

class Brand extends \common\controller\Base{
    /**首页
     */
    public function index(){
        $unlockingFooterCart = unlockingFooterCartConfig([15]);
        $this->assign('unlockingFooterCart', $unlockingFooterCart);            
        return $this->fetch();
    }

    public function edit(){
        return $this->fetch('info_tpl');
    }
}