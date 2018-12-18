<?php
namespace common\controller;

/**店铺基础类，需要判断是否入驻店铺的类需要继承
 */
class StoreBase extends FactoryBase
{
    protected $store = null;
    protected $storeList = null;

    public function __construct(){
        parent::__construct();
        //获取厂商店铺详情列表
        \common\cache\Store::remove($this->factory['id']);
        $list = \common\cache\Store::get($this->factory['id']);
        $this->assign('storeList', $list);
        $count = count($list);
        if($count > 1){
            //多家店判断是否有默认店铺
            foreach ($list as $val){
                if($val['is_default']){
                    $this->store = $val;
                }
            }
        }elseif($count == 1){
            $this->store = $list[0];
        }elseif(!$count) {
            $this->success('没有店铺，请申请', 'Store/edit');
        }
        $this->assign('store', $this->store);
        return $this->storeList;
    }

    //设置默认产商
    public function setDefaultStore(){
        $model = new \common\model\Store();
        return $model->setDefaultStore($this->factory['id']);
    }
}