<?php
namespace app\index\controller;
class Test extends \common\controller\Base{
    /**首页
     */
    public function test(){
        if(request()->isAjax()){
            $unlockingFooterCart = unlockingFooterCartConfig([0,2,1]);
            return json($unlockingFooterCart);
        }else{
            $unlockingFooterCart =json_encode(unlockingFooterCartConfig([0,2,1]),JSON_UNESCAPED_UNICODE);
            $this->assign('unlockingFooterCart',$unlockingFooterCart);
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