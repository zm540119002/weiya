<?php
namespace app\index_admin\controller;

class User extends \common\controller\UserBaseAdmin{
    /**用户-管理
     */
    public function manage(){
        if(!request()->isGet()){
            return config('custom.not_get');
        }
        return $this->fetch();
    }
    
    /**用户-信息
     */
    public function info(){
        $modelUser = new \common\model\User();
        if(request()->isPost()){
            return errorMsg('暂未开通');
        }else{
            $where = [
                'status' => 0,
                'id' => $this->user['id'],
            ];
            $info = $modelUser->where($where)->find();
            $this->assign('info',$info);
            return $this->fetch();
        }
    }

    /**用户-编辑
     */
    public function edit(){
        $modelUser = new \common\model\User();
        if(request()->isPost()){
            return $modelUser->edit($this->user);
        }else{
            $id = input('id',0);
            if($id){
                $where = [
                    'status' => 0,
                    'id' => $id,
                ];
                $info = $modelUser->where($where)->find();
                $this->assign('info',$info);
            }
            return $this->fetch();
        }
    }

    /**用户-列表
     */
    public function getList(){
        if(!request()->isGet()){
            return config('custom.not_get');
        }
        $modelUser = new \common\model\User();
        $where = [
            ['status', '=', 0],
            ['type', '<>', 0],
        ];
        $keyword = input('get.keyword','');
        if($keyword){
            $where[] = ['name', 'like', '%'.trim($keyword).'%'];
        }
        $config = [
            'where'=>$where,
            'field'=>[
                'id','name','nickname','mobile_phone',
            ],'order'=>[
                'id'=>'desc',
            ],
        ];
        $list = $modelUser->pageQuery($config);
        $this->assign('list',$list);
        return $this->fetch('user_list');
    }

    /**用户-删除
     */
    public function del(){
        if(!request()->isPost()){
            return errorMsg(config('custom.not_post'));
        }
        $modelUser = new \common\model\User();
        return $modelUser->del();
    }

    /**用户-赋角色
     */
    public function empower(){
        $modelUserRole = new \common\model\UserRole();
        if(request()->isPost()){
            return $modelUserRole->edit();
        }else{
            $userId = input('id',0);
            $this->assign('userId',$userId);
            $this->assign('userName',input('name',''));
            //用户角色列表
            $response = $modelUserRole->where('user_id','=',$userId)->select();
            $roleIds = array_column($response->toArray(),'role_id');
            $this->assign('roleIds',$roleIds?:[]);
            //系统角色列表
            $menu = new \common\lib\Menu();
            $roleList = $menu->getAllRole();
            $this->assign('roleList',$roleList);
            return $this->fetch();
        }
    }
}