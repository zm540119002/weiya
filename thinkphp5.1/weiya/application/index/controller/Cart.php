<?php
namespace app\index\controller;

class Cart extends \common\controller\UserBase{
    /**首页
     */
    public function index(){
        if(request()->isAjax()){
        }else{

            $unlockingFooterCart = unlockingFooterCartConfig([0,2,1]);
            $this->assign('unlockingFooterCart', $unlockingFooterCart);
            return $this->fetch();
        }
    }

    public function addCart(){
        if(!request()->isPost()){
            return errorMsg('请求方式错误');
        }
        $data = input('post.goodsList/a');
        if(empty($data)){
            return errorMsg('没有数据');
        }
        $userId = $this->user['id'];
        $arr = [
            'user_id' => $userId,
        ];
        array_walk($data, function (&$value, $key, $arr) {
            $value = array_merge($value, $arr);
        }, $arr);
        $model = new \app\index\model\Cart();
        $res = $model->allowField(true)->saveAll($data)->toArray();
        if (!count($res)) {
            return errorMsg('失败');
        }
        return successMsg('成功');

    }

    /**
     * 分页查询
     */
    public function getList(){
        if(!request()->isGet()){
            return errorMsg('请求方式错误');
        }
        $model = new \app\index\model\Cart();
        $config=[
            'where'=>[
            ],
            'field'=>[
                'g.id,g.sale_price,g.sale_type,g.shelf_status,g.create_time,g.update_time,g.inventory,
                g.name,g.retail_price,g.trait,g.category_id_1,g.category_id_2,g.category_id_3,
                g.thumb_img,g.goods_video,g.main_img,g.details_img,g.tag,g.parameters,g.sort,g.trait'
            ],
            'order'=>[
                'sort'=>'desc',
                'line_num'=>'asc',
                'id'=>'desc'
            ],
        ];
        if(input('?get.storeId') && (int)input('?get.storeId')){
            $config['where'][] = ['g.store_id', '=', input('get.storeId')];
        }
        $keyword = input('get.keyword','');
        if($keyword) {
            $config['where'][] = ['name', 'like', '%' . trim($keyword) . '%'];
        }
        $list = $model -> pageQuery($config);
        $this->assign('list',$list);
        if(isset($_GET['pageType'])){
            if($_GET['pageType'] == 'store' ){//店铺产品列表
                return $this->fetch('list_tpl');
            }
        }
    }

    /**详情页
     */
    public function detail(){
        if(request()->isAjax()){
        }else{
            $goodsId = intval(input('goods_id'));
            if(!$goodsId){
                $this->error('此商品已下架');
            }
            $model = new \app\index\model\Cart();
            $config =[
                'where' => [
                    ['g.status', '=', 0],
                    ['g.shelf_status', '=', 3],
                    ['g.id', '=', $goodsId],
                ],
            ];
            $info = $model->getInfo($config);
            if(empty($info)){
                $this->error('此商品已下架');
            }
            $info['main_img'] = explode(',',(string)$info['main_img']);
            $info['detail_img'] = explode(',',(string)$info['detail_img']);
            $this->assign('info',$info);

            $unlockingFooterCart = unlockingFooterCartConfig([0,2,1]);
            $this->assign('unlockingFooterCart', $unlockingFooterCart);
            return $this->fetch();
        }
    }

}