<?php
namespace app\index_admin\controller;

class GoodsCategory extends Base
{
    /**商品分类-管理
     */
    public function manage(){
        if(request()->isAjax()){
            $modelGoodsCategory = new \app\index_admin\model\GoodsCategory();
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
            $list = $modelGoodsCategory->where($where)->select();
            $this->assign('list',$list->toArray());
            return view('list_tpl');
        }else{
            return $this->fetch();
        }
    }

    /**商品分类-编辑
     */
    public function edit(){
        $modelGoodsCategory = new \app\index_admin\model\GoodsCategory();
        if(request()->isPost()){
            return $modelGoodsCategory->edit();
        }else{
            $id = input('id',0);
            $where = [
                'status' => 0,
            ];
            $allCategoryList = $modelGoodsCategory->where($where)->select();
            $this->assign('allCategoryList',$allCategoryList->toArray());
            if($id){
                $where['id'] = $id;
                $info = $modelGoodsCategory->where($where)->find();
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
        $modelGoodsCategory = new \app\index_admin\model\GoodsCategory();
        $list = $modelGoodsCategory->pageQuery();
        $this->assign('list',$list);
        return $this->fetch('list_tpl');
    }

    /**商品分类-删除
     */
    public function del(){
        if(!request()->isPost()){
            return config('custom.not_post');
        }
        $modelGoodsCategory = new \app\index_admin\model\GoodsCategory();
        return $modelGoodsCategory->del();
    }
}