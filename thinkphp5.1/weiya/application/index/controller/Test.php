<?php
namespace app\index\controller;
class Test extends \common\controller\Base{
    /**首页
     */
    public function index(){
        $unlockingFooterCart = unlockingFooterCartConfig([0,2,1]);
        $this->assign('unlockingFooterCart', $unlockingFooterCart);
        return $this->fetch();
    }
}