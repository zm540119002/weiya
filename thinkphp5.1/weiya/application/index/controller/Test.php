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
            $unlockingFooterCart = json_encode(unlockingFooterCartConfig([8,1]));
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