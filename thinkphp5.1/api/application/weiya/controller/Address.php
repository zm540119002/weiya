<?php
namespace app\weiya\controller;
class Address extends \common\controller\UserBaseApi{
    //增加修改地址页面
    public function edit(){
        if(!request()->isPost()){
            return buildFailed('请求方式不对');
        }
        $model = new \common\model\Address();
        $userId = $this->user['id'];
        $data = input('post.');
        $data = $data['data'];
        print_r($data);exit;
        if($data['id'] ){
            //开启事务
            $model -> startTrans();
            //修改
            $addressId = $data['id'];
            $condition = [
                'status' => 0 ,
                'id' => $addressId ,
                'user_id' => $userId ,
            ];
            $id = $model -> edit($data,$condition);
            if( !$id ){
                $model ->rollback();
                return buildFailed();
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
                    return buildFailed();
                }
            }
            $model->commit();
            return buildSuccess($data);
        }else{
            //增加
            $config = [
                'where'=>[
                    ['status','=',0],
                    ['user_id','=',$userId]
                ],
            ];
            $addressCount = $model -> where($config['where'])->count('id');
            if(!$addressCount){
                $data['is_default'] = 1;
            }
            //开启事务
            $model -> startTrans();
            $data['user_id'] = $userId;
            print_r($data);exit;
            $id = $model->edit($data);
            if(!$id){
                return buildFailed();
            }
            //修改其他地址不为默认值
            if($data['is_default'] == 1){
                $where = [
                    ['status','=',0],
                    ['id',"<>",$id],
                    ['user_id','=',$userId],
                ];
                $result = $model->where($where)->setField('is_default',0);
                if(false === $result){
                    $model ->rollback();
                    return buildFailed();
                }
            }
            $model->commit();
            $data['id'] = $id;
            print_r($data);exit;
            return buildSuccess($data);
        }


    }

    //获取
    public function getList()
    {
        if(!request()->isGet()){
            return errorMsg(config('custom.not_ajax'));
        }
        $model = new \common\model\Address();
        $config = [
            'where'=>[
                ['status','=',0],
                ['user_id','=',16]
            ],'field' => [
                'id','consignee','detail_address','tel_phone','mobile','is_default','status','province','city','area'
            ]
        ];
        $list = $model -> getList($config);
        return json_encode($list);

    }
    //获取
    public function getInfo()
    {
        if(!request()->isGet()){
            return errorMsg(config('custom.not_ajax'));
        }
        $model = new \common\model\Address();
        $id = input('get.id',0,'int');
        $config = [
            'where'=>[
                ['status','=',0],
                ['user_id','=',16],
                ['id','=',$id],
            ],'field' => [
                'id','consignee','detail_address','tel_phone','mobile','is_default','status','province','city','area'
            ]
        ];
        $info = $model -> getInfo($config);
        return json_encode($info);

    }
    //删除地址
    public function del(){
        if(!request()->isPost()){
            return errorMsg(config('custom.not_ajax'));
        }
        $id = input('post.id',0,'int');
        $model = new \common\model\Address();
        $condition = [
            ['id','=',$id],
        ];
        $result = $model -> del($condition);

        if($result['status']){
            return '删除成功';
        }else{
            return '删除失败';
        }

    }

}