<?php
namespace common\controller;

class FactoryStoreBase extends UserBase{
    protected $_storeList = null;
    protected $_factoryStoreList = null;
    protected $store = null;

    public function __construct(){
        parent::__construct();
        //采购商店铺列表
        $this->getFactoryStoreList();
        //获取当前店铺ID
        $sessionStoreId = session('currentStoreId','',config('custom.session_prefix'));
        $requestStoreId = (int)input('currentStoreId')?:(int)input('post.currentStoreId');
        if(($requestStoreId && $sessionStoreId!=$requestStoreId)){
            session('currentStoreId',$requestStoreId,config('custom.session_prefix'));
        }
        $currentStoreId = session('currentStoreId','',config('custom.session_prefix'));
        if($currentStoreId){
            $this->getCurrentStoreInfo($this->user['id'],$currentStoreId);
        }else{
            $countStoreList = count($this->_storeList);
            if($countStoreList == 0){
                if (request()->isAjax()) {
                    $this->success(config('custom.no_empower'),url($this->indexUrl),'no_empower',0);
                }else{
                    $this->error(config('custom.no_empower'),url($this->indexUrl),'none_store',0);
                }
            }elseif($countStoreList == 1){
                $this->store = $this->_storeList[0];
            }elseif($countStoreList > 1){
                if (!request()->isAjax()) {
//                    $this->success(config('custom.multi_store'),url($this->indexUrl),'multi_store',0);
                }
            }
        }
        if(!empty($this->store)){
            $this->store['id'] = $this->store['store_id'];
            //缓存当前店铺ID
            session('currentStoreId','',config('custom.session_prefix'),$this->store['store_id']);
        }
        $this->assign('store', $this->store);
    }


    /**缓存当前店铺信息
     */
    protected function getCurrentStoreInfo($userId,$storeId){
        $storeInfo = [];
        if($storeId){
            $model = new \common\model\UserStore();
            $config = [
                'field' => [
                    'us.id user_store_id','us.user_id','us.user_name',
                    's.id store_id','s.store_type','s.run_type','s.is_default','s.operational_model',
                    's.consignee_name','s.consignee_mobile_phone','s.detail_address',
                    's.province','s.city','s.area',
                    'case s.store_type when 1 then r.logo_img when 2 then b.brand_img END as logo_img',
                    'case s.store_type when 1 then r.short_name when 2 then b.name END as store_name',
                    'f.id factory_id','f.name factory_name','f.type factory_type',
                ],'join' => [
                    ['store s','s.id = us.store_id','left'],
                    ['record r','r.id = s.foreign_id','left'],
                    ['brand b','b.id = s.foreign_id','left'],
                    ['factory f','f.id = us.factory_id','left'],
                ],'where' => [
                    ['us.status','=',0],
                    ['us.user_id','=',$userId],
                    ['s.status','=',0],
                    ['s.id','=',$storeId],
                    ['f.status','=',0],
                    ['f.type','=',config('custom.type')],
                ],
            ];
            $storeInfo = $model->getInfo($config);
        }
        $this->store = $storeInfo;
    }

    /**组装店铺列表
     */
    protected function getFactoryStoreList(){
        $this->getStoreList();
        $storeListCount = count($this->_storeList);
        if($storeListCount>0){
            foreach ($this->_storeList as $item) {
                $storeInfoArr = [
                    'store_id' => $item['store_id'],
                    'store_name' => $item['store_name'],
                    'store_type' => $item['store_type'],
                    'run_type' => $item['run_type'],
                    'is_default' => $item['is_default'],
                    'operational_model' => $item['operational_model'],
                    'logo_img' => $item['logo_img'],
                ];
                $factory_id_arr = array_column($this->_factoryStoreList,'factory_id');
                if(!in_array($item['factory_id'],$factory_id_arr)){//factory不存在
                    $this->_factoryStoreList[] = [
                        'factory_id' => $item['factory_id'],
                        'factory_name' => $item['factory_name'],
                        'factory_type' => $item['factory_type'],
                        'storeList' => [$storeInfoArr],
                    ];
                }else{//factory存在
                    foreach ($this->_factoryStoreList as &$value){
                        if($value['factory_id'] == $item['factory_id']){
                            $value['storeList'][] = $storeInfoArr;
                        }
                    }
                }
            }
        }
        $this->assign('factoryStoreList', $this->_factoryStoreList);
    }

    /**获取店长店铺列表
     */
    protected function getStoreList(){
        $model = new \common\model\UserStore();
        $config = [
            'field' => [
                'us.id user_store_id','us.user_id','us.user_name',
                's.id store_id','s.store_type','s.run_type','s.is_default','s.operational_model',
                's.consignee_name','s.consignee_mobile_phone','s.detail_address',
                's.province','s.city','s.area',
                'case s.store_type when 1 then r.logo_img when 2 then b.brand_img END as logo_img',
                'case s.store_type when 1 then r.short_name when 2 then b.name END as store_name',
                'f.id factory_id','f.name factory_name','f.type factory_type',
            ],'join' => [
                ['store s','s.id = us.store_id','left'],
                ['record r','r.id = s.foreign_id','left'],
                ['brand b','b.id = s.foreign_id','left'],
                ['factory f','f.id = us.factory_id','left'],
            ],'where' => [
                ['us.status','=',0],
                ['us.type','=',3],
                ['us.user_id','=',$this->user['id']],
                ['s.status','=',0],
                ['f.status','=',0],
                ['f.type','=',config('custom.type')],
            ],
        ];
        $storeList = $model->getList($config);
        $this->_storeList = $storeList;
    }
}