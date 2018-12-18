<?php
namespace app\index_admin\controller;

/**供应商验证控制器基类
 */
class Brand extends Base {

    /*
     *审核首页
     */
    public function auditManage(){
        return $this->fetch();
    }
    public function info(){

    }

    /**
     *  分页查询
     */
    public function getList(){
        if(!request()->isGet()){
            return errorMsg('请求方式错误');
        }
        $model = new \app\index_admin\model\Brand;
        $filed = [
            'b.id,b.name,b.brand_img,b.certificate,b.authorization,b.create_time,b.update_time,b.auth_status,
             f.name as factory_name,gc.name as goods_category_name'
        ];
        $join = [
            ['factory f','f.id = b.factory_id','left'],
            ['goods_category gc','gc.id = b.category_id_1','left'],
        ];
        $list = $model -> pageQuery([],$filed,$join);
        $this->assign('list',$list);
        return $this->fetch('audit_list');
    }
    /**
     *  单条数据信息
     */
    public function getInfo(){
        if(!request()->isGet()){
            return errorMsg('请求方式错误');
        }
        $id = (int)input('get.id');
        if(!$id){
            return errorMsg('参数错误');
        }
        $model = new \app\index_admin\model\Brand;
        $where = [
            ['id','=',$id]
        ];
        $info = $model -> getInfo($where);
        $this->assign('info',$info);
        return $this->fetch('audit_info');
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
        $model = new \app\index_admin\model\Brand;
        return $model -> audit();
    }
}