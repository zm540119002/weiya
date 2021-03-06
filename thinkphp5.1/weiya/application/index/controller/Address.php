<?php
namespace app\index\controller;
class Address extends \common\controller\UserBase {
    //增加修改地址页面
    public function edit(){
        $model = new \common\model\Address();
        $userId = $this->user['id'];
        if(request()->isPost()){
            $data = input('post.');
            if(input('?post.address_id') && !empty(input('post.address_id')) ){
                //开启事务
                $model -> startTrans();
                //修改
                $addressId = input('post.address_id');
                $condition = [
                    ['status','=',0],
                    ['id','=',$addressId],
                    ['user_id','=',$userId],
                ];
                $result = $model->allowField(true)->save($data,$condition);
                if( false === $result ){
                    $model ->rollback();
                    return $this->errorMsg('失败');
                }
                //修改其他地址不为默认值
                if($_POST['is_default'] == 1){
                    $where = [
                        ['status','=',0],
                        ['id',"<>",$addressId],
                        ['user_id','=',$userId],
                    ];
                    $result = $model->where($where)->setField('is_default',0);
                    if(false === $result){
                        $model ->rollback();
                        return $this->errorMsg('失败');
                    }
                }
                $model->commit();
                $data['id'] = $addressId;
                $this->assign('data', $data);
                return view('address/info');
            }else{
                //增加
                $config = [
                    'where'=>[
                        ['status','=',0],
                        ['user_id','=',$userId]
                    ],
                ];
                $addressList = $model -> getList($config);
                if(empty($addressList)){
                    $data['is_default'] = 1;
                }
                //开启事务
                $model -> startTrans();
                $data['user_id'] = $userId;
                $result = $model->allowField(true)->save($data);
                if(!$result){
                    return $this->errorMsg('失败');
                }
                $addressId = $model->getAttr('id');
                //修改其他地址不为默认值
                if($_POST['is_default'] == 1){
                    $where = [
                        ['status','=',0],
                        ['id',"<>",$addressId],
                        ['user_id','=',$userId],
                    ];
                    $result = $model->where($where)->setField('is_default',0);
                    if(false === $result){
                        $model ->rollback();
                        return $this->errorMsg('失败');
                    }
                }
                $model->commit();
                $data['id'] = $addressId;
                $this -> assign('addressId',$addressId);
                $this->assign('data', $data);
                return view('address/info');
            }
        }

        $footerCartConfig = [6];
        if(input('?address_id') && !empty(input('address_id'))){
            $id = input('address_id');
            $config = [
                'where' => [
                    ['status','=',0],
                    ['id','=',$id],
                    ['user_id','=',$userId],
                ], 'field'=>[
                    'id','user_id', 'consignee', 'detail_address','mobile','is_default',
                    'tel_phone','province', 'city', 'area','status',
                ],
            ];
            $address = $model -> getInfo($config);
            $this->assign('address',$address);
            $footerCartConfig = [7];
        }
        $unlockingFooterCart = unlockingFooterCartConfig($footerCartConfig);
        $this->assign('unlockingFooterCart', $unlockingFooterCart);
        return $this -> fetch();

    }

    //地址列表
    public function manage(){
        $model = new \common\model\Address();
        $config = [
            'where'=>[
                ['status','=',0],
                ['user_id','=',$this->user['id']]
            ], 'field'=>[
                'id','user_id', 'consignee', 'detail_address','mobile','is_default',
                'tel_phone','province', 'city', 'area','status',
            ],
        ];
        $addressList = $model -> getList($config);
        $this->assign('addressList',$addressList);
        $unlockingFooterCart = unlockingFooterCartConfig([8]);
        $this->assign('unlockingFooterCart', $unlockingFooterCart);
        return $this -> fetch();
    }

    //删除地址
    public function del(){
        if(!request()->isAjax()){
            return $this->errorMsg(config('custom.not_ajax'));
        }
        $id = input('post.address_id',0,'int');
        $model = new \common\model\Address();
        $condition = [
            ['user_id','=',$this->user['id']],
            ['id','=',$id],
        ];
        $result = $model -> del($condition);
        if($result['status']){
            return successMsg('删除成功');
        }else{
            return $this->errorMsg('删除失败');
        }
    }

    /**
     * 获取地址列表  弹窗
     */
    public function _popGetList(){

        $model= new \common\model\Address();

        $condition = [
            'where' => [
                ['a.user_id','=',$this->user['id']],
            ],'field'=>[
            'id','user_id', 'consignee', 'detail_address','mobile','is_default',
            'tel_phone','province', 'city', 'area','status',
            ]
        ];
        $data = $model->getAddressDataList($condition);

        $this->assign('addressList',$data);

        return $this->fetch('pop_list');
    }

}