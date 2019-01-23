<?php
namespace app\index\controller;

class Collection extends \common\controller\Base{
    /**首页
     */
    public function index(){
        $unlockingFooterCart = unlockingFooterCartConfig([10,17]);
        $this->assign('unlockingFooterCart', $unlockingFooterCart);
        return $this->fetch();
    }

    public function Collection(){
        
    }
}