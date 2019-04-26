<?php
namespace app\index\controller;
class Test extends \common\controller\Base{
    /**测试
     */
    public function test(){
        if(request()->isAjax()){
            $unlockingFooterCart = unlockingFooterCartConfig([0,2,1]);
            return json_encode($unlockingFooterCart);
        }else{
            $unlockingFooterCart = unlockingFooterCartConfigTest([0,2,1]);
            array_push($unlockingFooterCart['menu'][0]['class'],'group_btn30');
            array_push($unlockingFooterCart['menu'][1]['class'],'group_btn30');
            array_push($unlockingFooterCart['menu'][2]['class'],'group_btn30');
            $this->assign('unlockingFooterCart',json_encode($unlockingFooterCart));

            $id = intval(input('id'));
            if(!$id){
                $this->error('此商品已下架');
            }
            $model = new \app\index\model\Goods();
            $config =[
                'where' => [
                    ['g.status', '=', 0],
                    ['g.shelf_status', '=', 3],
                    ['g.id', '=', $id],
                ],
            ];
            $info = $model->getInfo($config);
            if(empty($info)){
                $this->error('此商品已下架');
            }
            $info['main_img'] = explode(',',(string)$info['main_img']);
            $info['detail_img'] = explode(',',(string)$info['detail_img']);
            $info['tag'] = explode(',',(string)$info['tag']);
            $this->assign('info',$info);

            $modelComment = new \app\index\model\Comment();
            $where = [
                ['status','=',0],
                ['goods_id','=',$id],
            ];
            $averageScore = $modelComment -> where($where)->avg('score');
            $averageScore = round($averageScore,2);
            $this ->assign('averageScore',$averageScore);
            $total = $modelComment -> where($where)->count('user_id');
            $this ->assign('total',$total);

            //登录判断是否已收藏
            $user = session('user');
            if(!empty($user)){
                $modelCollection = new \app\index\model\Collection();
                $config = [
                    'where'=>[
                        ['user_id','=',$user['id']],
                        ['goods_id','=',$id],
                        ['status','=',0]
                    ],'field'=>[
                        'id'
                    ]
                ];
                $info = $modelCollection -> getInfo($config);
                if(count($info)){
                    $this->assign('collected', 1);
                }
            }

            return $this->fetch();
        }
    }

    /**测试1
     */
    public function test1(){
        return $this->fetch();
    }

    /**测试2
     */
    public function test2(){
        return $this->fetch();
    }

    /**测试3
     */
    public function test3(){
        return $this->fetch();
    }
}