<?php
namespace app\index_admin\controller;

/**供应商验证控制器基类
 */
class Store extends Base {

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
        $model = new \app\index_admin\model\Store;
        $filed = [
            's.id,s.name,s.foreign_id,s.store_type,s.run_type,s.auth_status,s.create_time,s.update_time,
             s.logo_img,f.name as factory_name'
        ];
        $join = [
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
        $model = new \app\index_admin\model\Store;
        return $model -> audit();
    }

}