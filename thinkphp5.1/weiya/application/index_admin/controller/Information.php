<?php
namespace app\index_admin\controller;

/**供应商验证控制器基类
 */
class Information extends Base {
    
    public function manage(){
        return $this->fetch('manage');
    }

    /**
     * @return array
     * 审核
     */
    public function edit(){
        $model = new \app\index_admin\model\Information();
        if(request()->isPost()){
            if( isset($_POST['main_img']) && $_POST['main_img'] ){
                $detailArr = explode(',',input('post.main_img','','string'));
                $tempArr = array();
                foreach ($detailArr as $item) {
                    if($item){
                        $tempArr[] = moveImgFromTemp(config('upload_dir.weiya_information'),basename($item));
                    }
                }
                $_POST['main_img'] = implode(',',$tempArr);
            }
            $data = $_POST;
            if(isset($_POST['id']) && intval($_POST['id'])){//修改
                $config = [
                    'where' => [
                        'id' => input('post.id',0,'int'),
                        'status' => 0,
                    ],
                ];
                $info = $model->getInfo($config);

                if($info['main_img']){
                    //删除商品详情图
                    $oldImgArr = explode(',',$info['main_img']);
                    $newImgArr = explode(',',$_POST['main_img']);
                    delImgFromPaths($oldImgArr,$newImgArr);
                }
                $where = [
                    'id'=>input('post.id',0,'int')
                ];
                $data['update_time'] = time();
                $result = $model -> allowField(true) -> save($data,$where);
                if(false === $result){
                    return errorMsg('失败');
                }
            }else{//新增
                $data['create_time'] = time();
                $result = $model -> allowField(true) -> save($data);
                if(!$result){
                    $model ->rollback();
                    return errorMsg('失败');
                }

            }
            return successMsg('成功');
        }else{
            //要修改的商品
            if(input('?id') && (int)input('id')){
                $config = [
                    'where' => [
                        'status' => 0,
                        'id'=>input('id',0,'int'),
                    ],
                ];
                $info = $model->getInfo($config);
                $this->assign('info',$info);
            }
            return $this->fetch();
       }
    }

    /**
     *  分页查询
     */
    public function getList(){
        $model = new \app\index_admin\model\Information();
        $where = [];
        $where[] = ['i.status','=',0];
        if(isset($_GET['category_id_1']) && intval($_GET['category_id_1'])){
            $where[] = ['i.category_id_1','=',input('get.category_id_1',0,'int')];
        }
        if(isset($_GET['category_id_2']) && intval($_GET['category_id_2'])){
            $where[] = ['i.category_id_2','=',input('get.category_id_2',0,'int')];
        }
        if(isset($_GET['category_id_3']) && intval($_GET['category_id_3'])){
            $where[] = ['i.category_id_3','=',input('get.category_id_3',0,'int')];
        }
        $keyword = input('get.keyword','','string');
        if($keyword){
            $where[] = ['i.headline','like', '%' . trim($keyword) . '%'];
        }
        $config = [
            'where'=>$where,
            'field'=>[
                'i.id','i.headline','i.content','i.main_img','i.auth_status','i.sort','i.create_time'
            ],
            'order'=>[
                'i.id'=>'desc',
                'i.sort'=>'desc',
            ],
        ];
        $list = $model ->pageQuery($config);
        $this->assign('list',$list);
        if($_GET['pageType'] == 'manage'){
            return view('list_tpl');
        }
    }


    /**
     * @return array|mixed
     * 删除
     */
    public function del(){
        if(!request()->isPost()){
            return config('custom.not_post');
        }
        $model = new \app\index_admin\model\Information();
        $id = input('post.id/d');
        if(input('?post.id') && $id){
            $condition = [
                ['id','=',$id]
            ];
        }
        if(input('?post.ids')){
            $ids = input('post.ids/a');
            $condition = [
                ['id','in',$ids]
            ];
        }
        return $model->del($condition);
    }

    /**
     * 设置状态
     */
    public function setAuthStatus(){
        if(!request()->isPost()){
            return config('custom.not_post');
        }
        $model = new \app\index_admin\model\Information();
        $id = input('post.id/d');
        if(!input('?post.id') && !$id){
            return errorMsg('失败');
        }
        $rse = $model->where(['id'=>input('post.id/d')])->setField(['auth_status'=>input('post.auth_status/d')]);
        if(!$rse){
            return errorMsg('失败');
        }
        return successMsg('成功');
    }

    /**
     * 设置精选
     */
    public function setSelection(){
        if(!request()->isPost()){
            return config('custom.not_post');
        }
        $model = new \app\index_admin\model\Scene();
        $id = input('post.id/d');
        if(!input('?post.id') && !$id){
            return errorMsg('失败');
        }
        $rse = $model->where(['id'=>input('post.id/d')])->setField(['is_selection'=>input('post.is_selection/d')]);
        if(!$rse){
            return errorMsg('失败');
        }
        return successMsg('成功');
    }

    /**
     * 添加项目相关商品
     * @return array|mixed
     * @throws \Exception
     */
    public function addSceneGoods(){
        if(request()->isPost()){
            $model = new \app\index_admin\model\SceneGoods();
            $data = input('post.selectedIds/a');
            $condition = [
                ['scene_id','=',$data[0]['scene_id']]
            ];
            $model->startTrans();
            $rse = $model -> del($condition,$tag=false);

            if(false === $rse){
                $model->rollback();
                return errorMsg('失败');
            }
            $res = $model->allowField(true)->saveAll($data)->toArray();
            if (!count($res)) {
                $model->rollback();
                return errorMsg('失败');
            }
            $model -> commit();
            return successMsg('成功');
            
        }else{
            if(!input('?id') || !input('id/d')){
                $this ->error('参数有误',url('manage'));
            }
            // 所有商品分类
            $model = new \app\index_admin\model\GoodsCategory();
            $config = [
                'where'=>[
                    'status'=>0
                ]
            ];
            $allCategoryList = $model->getList($config);
            $this->assign('allCategoryList',$allCategoryList);

            $id = input('id/d');
            $this->assign('id',$id);
            return $this->fetch();
        }
    }

}