<?php
namespace app\index\controller;
class Test extends \common\controller\Base{
    /**测试
     */
    public function test(){
        if(request()->isAjax()){
            $unlockingFooterCart = unlockingFooterCartConfig([0,2,1]);
            return json_encode($unlockingFooterCart);
        }else{
            $unlockingFooterCart = unlockingFooterCartConfig([0,2,1]);
            print_r($unlockingFooterCart);
            $unlockingFooterCart = json_encode($unlockingFooterCart);
            $this->assign('unlockingFooterCart',$unlockingFooterCart);
            return $this->fetch();
        }
    }

    /**测试1
     */
    public function test1(){
        return $this->fetch();
    }

    /**测试2
     */
    public function test2(){
        return $this->fetch();
    }
}