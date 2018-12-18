<?php
namespace app\index_admin\controller;

class ProjectCategory extends Base
{
    /**商品分类-管理
     */
    public function manage(){
        if(request()->isAjax()){
            $model = new \app\index_admin\model\ProjectCategory();
            $where = [
                'status' => 0,
            ];
            $level = input('level',0);
            if($level){
                $where['level'] = $level + 1;
            }
            $id = input('id',0);
            if($id){
                if($level==1){
                    $where['parent_id_1'] = $id;
                }
                if($level==2){
                    $where['parent_id_2'] = $id;
                }
            }
            $list = $model->where($where)->select();
            $this->assign('list',$list->toArray());
            return view('list_tpl');
        }else{
            return $this->fetch();
        }
    }

    /**商品分类-编辑
     */
    public function edit(){
        $model = new \app\index_admin\model\ProjectCategory();
        if(request()->isPost()){
            return $model->edit();
        }else{
            $id = input('id',0);
            $where = [
                'status' => 0,
            ];
            $allCategoryList = $model->where($where)->select();
            $this->assign('allCategoryList',$allCategoryList->toArray());
            if($id){
                $where['id'] = $id;
                $info = $model->where($where)->find();
                $this->assign('info',$info);
                $this->assign('operate',input('operate',''));
            }
            return $this->fetch();
        }
    }

    /**商品-列表
     */
    public function getList(){
        if(!request()->isGet()){
            return config('custom.not_get');
        }
        $model = new \app\index_admin\model\ProjectCategory();
        $where = [
			['status', '=', 0],
			['level', '=', 1],
		];
		$keyword = input('get.keyword','');
		if($keyword){
			$where[] = ['name', 'like', '%'.trim($keyword).'%'];
		}
		$field = [
            'id','name','level','parent_id_1','parent_id_2','remark','sort','img',
        ];
        $config = [
            'where' => $where,
            'field' => $field,
        ];
        $list = $model->pageQuery($config);
        $this->assign('list',$list);
        return $this->fetch('list_tpl');
    }

    /**商品分类-删除
     */
    public function del(){
        if(!request()->isPost()){
            return config('custom.not_post');
        }
        $model = new \app\index_admin\model\ProjectCategory();
        $id = input('post.id',0,'int');
        $level = input('post.level',0,'int');
        if($level == 1){
            $config = [
                'where' => [
                    'status'=>0,
                    'parent_id_1'=>$id
                ],'field'=>[
                    'id'
                ]
            ];
            $ids = $model ->getList($config);
            $ids = array_column($ids,'id');
            $ids[] = $id;
            $condition = [
                ['id','in',$ids]
            ];
        }elseif($level == 2){
            $config = [
                'where' => [
                    'status'=>0,
                    'parent_id_2'=>$id
                ],'field'=>[
                    'id'
                ]
            ];
            $ids = $model ->getList($config);
            $ids = array_column($ids,'id');
            $ids[] = $id;
            $condition = [
                ['id','in',$ids]
            ];
        }elseif($level == 3){
            $condition = [
               ['id', '=',$id]
            ];
        }else{
            return errorMsg('失败');
        }
        return $model->del($condition);
    }
}