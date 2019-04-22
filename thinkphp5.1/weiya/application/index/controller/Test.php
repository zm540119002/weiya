<?php
namespace app\index\controller;
class Test extends \common\controller\Base{
    /**首页
     */
    public function test(){
        if(request()->isAjax()){
            $unlockingFooterCart = unlockingFooterCartConfig([0,2,1]);
            $this->assign('list',$unlockingFooterCart);
//            return json($unlockingFooterCart);
        }else{
            return $this->fetch();
        }
    }

    public function test1(){
        return $this->fetch();
    }

    public function test2(){
        return $this->fetch();
    }
}