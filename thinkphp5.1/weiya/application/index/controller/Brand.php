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
        $brandInfo = [
            'brand_type'=>1,
            'brand_name'=>'gaasg',
            'brand_logo'=>'temp/2019011615535029895.jpeg',
            'trademark_certificate'=>'temp/2019011615535288073.jpeg',
            'trademark_authorization'=>'temp/2019011615535288073.jpeg',
        ];
        $this->assign('info',$brandInfo);
        return $this->fetch('info_tpl');
    }
}