<?php
namespace app\index_admin\controller;

/**供应商验证控制器基类
 */
class Goods extends Base {

    /*
     *审核首页
     */
    public function auditManage(){
        return $this->fetch();
    }

    /**
     *  分页查询
     */
    public function getList(){
        if(!request()->isGet()){
            return errorMsg('请求方式错误');
        }
        $model = new \app\index_admin\model\Goods;
        $filed = [
            'g.id,g.sale_price,g.sale_type,g.shelf_status,g.create_time,g.update_time,g.inventory,
                g.name,g.retail_price,g.trait,g.category_id_1,g.category_id_2,g.category_id_3,
                g.thumb_img,g.goods_video,g.main_img,g.details_img,g.tag,g.parameters,g.sort,g.store_id,
                s.name as store_name,f.id as factory_id,f.name as factory_name'
        ];
        $join = [
            ['store s','g.store_id = s.id','left'],
            ['factory f','f.id = s.factory_id','left'],
        ];
        $list = $model -> pageQuery([],$filed,$join);
        $this->assign('list',$list);
        return $this->fetch('audit_list');
    }

    /**
     * @return array
     * 审核
     */
    public function audit(){
        if(!request()->isPost()){
            return errorMsg('请求方式错误');
        }
        $id = (int)input('post.id');
        if(!$id){
            return errorMsg('参数错误');
        }
        $model = new \app\index_admin\model\Goods;
        return $model -> audit();
    }

}