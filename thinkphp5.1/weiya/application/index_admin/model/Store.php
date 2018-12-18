<?php
namespace app\index_admin\model;
use think\Model;
use think\Db;
/**
 * 基础模型器
 */

class Store extends Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'store';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_factory';
	/**
	 * 审核厂商
	 */
	public function audit(){
		$postData = input('post.');
		$res = $this->isUpdate(true)->save($postData);
		if($res===false){
			return errorMsg('更新失败',$this->getError());
		}else{
			return successMsg('成功！',$postData);
		}
	}
	
	/**
	 * @param array $where
	 * @param array $field
	 * @param array $order
	 * @param array $join
	 * @param string $limit
	 * @return array|\PDOStatement|string|\think\Collection
	 * 查询多条数据
	 */
	public function getList($where=[],$field=['*'],$join=[],$order=[],$limit=''){
		$_where = array(
			's.status' => 0,
		);
		$_join = array(
		);
		$where = array_merge($_where, $where);
		$_order = array(
			's.id'=>'desc',
		);
		$order = array_merge($_order, $order);
		$list = $this->alias('s')
			->where($where)
			->field($field)
			->join(array_merge($_join,$join))
			->order($order)
			->limit($limit)
			->select();
	    return count($list)!=0?$list->toArray():[];
	}

	/**
	 * @param array $where
	 * @param array $field
	 * @param array $join
	 * @return array|null|\PDOStatement|string|Model
	 * 查找一条数据
	 */
	public function getInfo($where=[],$field=['*'],$join=[]){
		$_where = array(
			's.status' => 0,
		);
		$where = array_merge($_where, $where);
		$_join = array(
		);
		$info = $this->alias('s')
			->field($field)
			->join(array_merge($_join,$join))
			->where($where)
			->find();
		return $info?$info->toArray():[];
	}

	/**
	 * 分页查询 商品
	 * @param array $_where
	 * @param array $_field
	 * @param array $_join
	 * @param string $_order
	 * @return \think\Paginator
	 */
	public function pageQuery($_where=[],$_field=['*'],$_join=[],$_order=[]){
		$where = [
			['s.status', '=', 0],
		];
		$keyword = input('get.keyword','');
		if($keyword){
			$where[] = ['f.name|s.name', 'like', '%'.trim($keyword).'%'];
		}
		if(input('?get.auth_status')){
			$authStatus = input('get.auth_status','int');
			$where[] = ['s.auth_status', '=',$authStatus];
		}
		$order = [
			's.auth_status'=>'asc',
			's.id'=>'desc'
		];
		$join = [];
		$where = array_merge($_where, $where);
		$order = array_merge($_order,$order);
		$join  = array_merge($_join,$join);
		$pageSize = (isset($_GET['pageSize']) && intval($_GET['pageSize'])) ?
			input('get.pageSize',0,'int') : config('custom.default_page_size');
		return $list = $this->alias('s')->join($join)->where($where)->field($_field)->order($order)->paginate($pageSize);
	}
}