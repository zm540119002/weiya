<?php
namespace app\index\controller;

class Index extends \common\controller\Base{
    /**首页
     */
    public function index(){
        //获取商品的分类
        $modelGoodsCategory = new \app\index\model\GoodsCategory();
        $config =[
            'where' => [
                ['status', '=', 0],
                ['level','=',1]
            ], 'order'=>[
                'sort'=>'desc',
                'id'=>'desc'
            ],  'limit'=>'7'

        ];
        $categoryList  = $modelGoodsCategory->getList($config);
        print_r($categoryList);exit;
        $this ->assign('categoryList',$categoryList);
        //获取精选的6个 场景
        $modelScene = new \app\index\model\Scene();
        $config =[
            'where' => [
                ['status', '=', 0],
                ['shelf_status','=',3]
            ], 'order'=>[
                'is_selection'=>'desc',
                'sort'=>'desc',
                'id'=>'desc'
            ],  'limit'=>'6'

        ];
        $sceneList  = $modelScene->getList($config);
        $this ->assign('sceneList',$sceneList);

        //获取精选的10个项目
        $modelProject = new \app\index\model\Project();
        $config =[
            'where' => [
                ['status', '=', 0],
                ['shelf_status','=',3]
            ], 'order'=>[
                'is_selection'=>'desc',
                'sort'=>'desc',
                'id'=>'desc'
            ],  'limit'=>'6'
        ];
        $projectList  = $modelProject->getList($config);
        $this ->assign('projectList',$projectList);

        //获取精选的6个项目
        $modelGoods = new \app\index\model\Goods();
        $config =[
            'where' => [
                ['status', '=', 0],
                ['is_selection', '=', 1],
                ['shelf_status','=',3]
            ], 'order'=>[
                'sort'=>'desc',
                'id'=>'desc'
            ],  'limit'=>'6'

        ];
        $goodsList  = $modelGoods->getList($config);
        $this ->assign('goodsList',$goodsList);
        return $this->fetch();
    }
}