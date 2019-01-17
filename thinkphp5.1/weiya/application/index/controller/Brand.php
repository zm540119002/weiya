<?php
namespace app\index\controller;

class Brand extends \common\controller\UserBase{
    /**首页
     */
    public function index(){
        $unlockingFooterCart = unlockingFooterCartConfig([15]);
        $this->assign('unlockingFooterCart', $unlockingFooterCart);            
        return $this->fetch();
    }

    public function edit(){
        if(request()->isAjax()){
            $userId = $this->user['id'];
            $model = new  \app\index\model\Brand();
            $data = input('post.');
            if(input('?post.id') && !empty(input('post.id')) ){
                //开启事务
                $model -> startTrans();
                //修改
                $id = input('post.id');
                $condition = [
                    ['status','=',0],
                    ['id','=',$id],
                    ['user_id','=',$userId],
                ];
                $result = $model -> edit($data,$condition);
                if( !$result['status'] ){
                    $model ->rollback();
                    return errorMsg('失败');
                }
                //修改其他地址不为默认值
                if($_POST['is_default'] == 1){
                    $where = [
                        ['status','=',0],
                        ['id',"<>",$id],
                        ['user_id','=',$userId],
                    ];
                    $result = $model->where($where)->setField('is_default',0);
                    if(false === $result){
                        $model ->rollback();
                        return errorMsg('失败');
                    }
                }
                $model->commit();
                $data['id'] = $id;
                $this->assign('info',$data);
                return $this->fetch('info_tpl');
            }else{
                //增加
                $config = [
                    'where'=>[
                        ['status','=',0],
                        ['user_id','=',$userId]
                    ],
                ];
                $list = $model -> getList($config);
                if(empty($list)){
                    $data['is_default'] = 1;
                }
                //开启事务
                $model -> startTrans();
                $data['user_id'] = $userId;
                $result = $model->edit($data);
                if(!$result['status']){
                    return errorMsg('失败');
                }
                $id = $result['id'];
                //修改其他地址不为默认值
                if($_POST['is_default'] == 1){
                    $where = [
                        ['status','=',0],
                        ['id',"<>",$id],
                        ['user_id','=',$userId],
                    ];
                    $result = $model->where($where)->setField('is_default',0);
                    if(false === $result){
                        $model ->rollback();
                        return errorMsg('失败');
                    }
                }
                $model->commit();
                $data['id'] = $id;
                $this -> assign('id',$id);
                $this->assign('info',$data);
                return $this->fetch('info_tpl');
            }

        }else{
            return $this->fetch();
        }

    }
}