<?php
namespace app\weiya_customization\controller;

class Store extends \common\controller\Base{
    /**首页
     */
    public function index(){
        if(request()->isAjax()){
        }else{
            return $this->fetch();
        }
    }

    /**供应商店商品列表
     */
    public function goodsList(){
       if(request()->isAjax()){

        }else{
           $storeId = input('param.store_id','int');
           if(!positiveInteger($storeId)){
               return 111;
           }
            $this->assign('storeId',$storeId);
            return $this->fetch();
        }
    }

    /**供应商店商品列表
     */
    public function getInfo(){
        if(!request()->isGet()){
            return errorMsg('请求方式错误');
        }
        $model =  new \app\purchase\model\Store;
        $config = [
            'where' => [
                ['s.id','=',input('get.storeId','int')],
                ['s.run_type','=',1],
            ],'join' => [
                ['record r','r.id = s.foreign_id','left'],
                ['brand b','b.id = s.foreign_id','left']
            ],'field' => [
                's.id','s.store_type','s.run_type','s.is_default','case s.store_type when 1 then r.logo_img when 2 then b.brand_img END as logo_img',
                'case s.store_type when 1 then r.short_name when 2 then b.name END as name',
            ],
        ];
        return $model -> getInfo($config);
    }


}