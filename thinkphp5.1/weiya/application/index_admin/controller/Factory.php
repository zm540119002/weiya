<?php
namespace app\index_admin\controller;

/**供应商验证控制器基类
 */
class Factory extends Base {

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
        $model = new \app\index_admin\model\Factory;
        $list = $model -> pageQuery();
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
        $model = new \app\index_admin\model\Factory;
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
        $model = new \app\index_admin\model\Factory;
        return $model -> audit();

    }

}