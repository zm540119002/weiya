<?php
namespace app\index\controller;

class Brand extends \common\controller\UserBase{
    /**首页
     */
    public function index(){
        $model = new  \app\index\model\Brand();
        $config = [
            'where'=>[
                ['status','=',0],
                ['user_id','=',$this->user['id']]
            ],
        ];
        $list = $model -> getList($config);
        $this->assign('list',$list);
        $unlockingFooterCart = unlockingFooterCartConfig([15]);
        $this->assign('unlockingFooterCart', $unlockingFooterCart);
        return $this->fetch();
    }

    public function edit(){
        if(request()->isAjax()){
            $pageTye = input('post.page_type');
            $this->assign('operate_btn',$pageTye);
            $userId = $this->user['id'];
            $data = input('post.');
            $model = new  \app\index\model\Brand();
            if( isset($_POST['logo']) && $_POST['logo'] ){
                $data['logo'] = moveImgFromTemp(config('upload_dir.weiya_goods'),basename($_POST['logo']));
            }
            if( isset($_POST['certificate']) && $_POST['certificate'] ){
                $data['certificate'] = moveImgFromTemp(config('upload_dir.weiya_goods'),basename($_POST['certificate']));
            }
            if( isset($_POST['authorization']) && $_POST['authorization'] ){
                $data['authorization'] = moveImgFromTemp(config('upload_dir.weiya_goods'),basename($_POST['authorization']));
            }
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
                $config = [
                    'where' => [
                        ['status','=',0],
                        ['id','=',$id],
                        ['user_id','=',$userId],
                    ],
                ];
                $info = $model->getInfo($config);
                if($info['logo']){
                    //删除旧主图
                    delImgFromPaths($info['logo'],$data['logo']);
                }
                if($info['certificate']){
                    //删除旧主图
                    delImgFromPaths($info['certificate'],$data['certificate']);
                }
                if($info['authorization']){
                    //删除商品旧主图
                    delImgFromPaths($info['authorization'],$data['authorization']);
                }
                $model->commit();
                $this->assign('info',$info);
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

                $data['user_id'] = $userId;
                $data['create_time'] = time();
                $result = $model->edit($data);
                if(!$result['status']){
                    return errorMsg('失败');
                }
                $id = $result['id'];
                $data['id'] = $id;
                $this -> assign('id',$id);
                $this->assign('info',$data);
                return $this->fetch('info_tpl');
            }

        }else{
            return $this->fetch();
        }
    }

    /**
     * @return array设置默认值
     */
   public function setDefault(){
       if(!request()->isAjax()){
           return errorMsg('请求方式错误');
       }
       $id = input('post.id');
       $userId = $this->user['id'];
       $data = input('post.');
       $condition = [
           ['status','=',0],
           ['id','=',$id],
           ['user_id','=',$userId],
       ];
       $model = new  \app\index\model\Brand();
       $model -> startTrans();
       $result = $model -> edit($data,$condition);
       if( !$result['status'] ){
           $model ->rollback();
           return errorMsg('失败');
       }
       //修改其他不为默认值
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
       return successMsg('成功');

   }

    //获取列表
    public function getList(){
        $model = new  \app\index\model\Brand();
        $config = [
            'where'=>[
                ['status','=',0],
                ['user_id','=',$this->user['id']]
            ],
        ];
        $list = $model -> getList($config);
        $this->assign('list',$list);
        return $this->fetch('list_tpl');
    }

    //删除
    public function del(){
        if(!request()->isAjax()){
            return errorMsg(config('custom.not_ajax'));
        }
        $ids = input('post.ids/a');
        if(empty($ids)){
            return errorMsg(config('请求参数错误'));
        }
        $model = new \app\index\model\Brand();
        $condition = [
            ['user_id','=',$this->user['id']],
            ['id','in',$ids],
        ];
        $result = $model -> del($condition);
        if($result['status']){
            return successMsg('删除成功');
        }else{
            return errorMsg('删除失败');
        }
    }

}