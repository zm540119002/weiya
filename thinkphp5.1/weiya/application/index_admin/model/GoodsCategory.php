<?php
namespace app\index_admin\model;

class GoodsCategory extends \think\Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'goods_category';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_common';

	//编辑
	public function edit(){
		$postData = input('post.');
		$validateGoodsCategory = new \common\validate\GoodsCategory();
		if(!$validateGoodsCategory->scene('edit')->check($postData)){
			return errorMsg($validateGoodsCategory->getError());
		}
		if($postData['id'] && intval($postData['id'])){
			$postData['update_time'] = time();
			$this->isUpdate(true)->save($postData);
		}else{
			unset($postData['id']);
			$postData['create_time'] = time();
			$this->save($postData);
		}
		if(!$this->getAttr('id')){
			return errorMsg('失败',$this->getError());
		}
		return successMsg('成功！',array('id'=>$this->getAttr('id')));
	}
	
	//分页查询
	public function pageQuery(){
		$where = [
			['status', '=', 0],
			['level', '=', 1],
		];
		$keyword = input('get.keyword','');
		if($keyword){
			$where[] = ['name', 'like', '%'.trim($keyword).'%'];
		}
		$field = array(
			'id','name','level','parent_id_1','parent_id_2','remark','sort','img',
		);
		$order = 'id';
		$pageSize = (isset($_GET['pageSize']) && intval($_GET['pageSize'])) ?
			input('get.pageSize',0,'int') : config('custom.default_page_size');
		return $this->where($where)->field($field)->order($order)->paginate($pageSize);
	}

	//删除
	public function del($tag=true){
		$where = [
			['status', '=', 0],
		];
		$id = input('post.id',0);
		if(!$id){
			return errorMsg('参数错误');
		}
		$where[] = ['id', '=', $id];
		$level = input('post.level',0);
		$whereOr = [];
		if($level==1){
			$whereOr[] = ['parent_id_1', '=', $id];
		}elseif($level==2){
			$whereOr[] = ['parent_id_2', '=', $id];
		}
		if($tag){//标记删除
			$result = $this->where($where)->whereOr($whereOr)->setField('status',2);
		}else{
			$result = $this->where($where)->whereOr($whereOr)->delete();
		}
		if(!$result){
			return errorMsg('失败',$this->getError());
		}
		return successMsg('成功');
	}

	/**查询多条数据
	 */
	public function getList($where=[],$field=['*'],$join=[],$order=[],$limit=''){
		$_where = array(
			'gc.status' => 0,
		);
		$_join = array(
		);
		$where = array_merge($_where, $where);
		$_order = array(
			'gc.id'=>'desc',
		);
		$order = array_merge($_order, $order);
		$list = $this->alias('gc')
			->where($where)
			->field($field)
			->join(array_merge($_join,$join))
			->order($order)
			->limit($limit)
			->select();
		return count($list)!=0?$list->toArray():[];
	}

	/**查找一条数据
	 */
	public function getInfo($where=[],$field=['*'],$join=[]){
		$_where = array(
			'gc.status' => 0,
		);
		$where = array_merge($_where, $where);
		$_join = array(
		);
		$info = $this->alias('gc')
			->field($field)
			->join(array_merge($_join,$join))
			->where($where)
			->find();
		return $info?$info->toArray():[];
	}
}