<?php
namespace common\model;
use GuzzleHttp\Psr7\Request;
use think\Model;
use think\Db;
/**
 * 基础模型器
 */

class Promotion extends Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'promotion';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
	// 别名
	protected $alias = 'p';
	protected $connection = 'db_config_common';
	/**
	 * 新增和修改
	 */
	public function edit($storeId ='',$runType =''){
		$modelGoods  = new \common\model\Goods;
		$data = input('post.');
		$data['run_type'] = $runType;
		$validate = validate('\common\validate\Promotion');
		if(!$result = $validate->check($data)) {
			return errorMsg($validate->getError());
		}
		if(!empty($data['first_img'])){
			$data['first_img'] = moveImgFromTemp(config('upload_dir.factory_promotion'),basename($data['first_img']));
		}
		if(!empty($data['second_img'])){
			$data['second_img'] = moveImgFromTemp(config('upload_dir.factory_promotion'),basename($data['second_img']));
		}
		//
		$selectedGoods = json_decode($data['goods'],true);
		$selectedGoodsIds = [];
		foreach ($selectedGoods as $key=>$value){
			$selectedGoodsIds[] = $value['goods_id'];
			$selectedGoods[$key]['id'] = $value['goods_id'];
			$selectedGoods[$key]['sale_type'] = 1;
		}
		$data['goods_ids'] = implode(',',$selectedGoodsIds);
		$data['store_id'] = $storeId;
		$this ->startTrans();
		if(input('?post.id')){//修改
			$config = [
				'where' => [
					['id','=',$data['id']],
					['store_id','=',$storeId],
				],
				'field' => [
					'first_img','second_img','goods_ids',
				],
			];
			$oldInfo = $this -> getInfo($config);
			$data['update_time'] = time();
			$result = $this -> allowField(true) -> save($data,['id' => $data['id'],'store_id'=>$storeId]);
			if(false == $result){
				$this ->rollback();
				return errorMsg('失败');
			}
			$deleteGoodsIds= array_diff(explode(',',$oldInfo['goods_ids']),$selectedGoodsIds);
			if(!empty($deleteGoodsIds)){
				$where = [
					['id','in',$deleteGoodsIds]
				];
				$result = $modelGoods -> where($where)->setField('sale_type',0);
				if(false === $result){
					$this ->rollback();
					return errorMsg('失败');
				}
			}
		}else{//新增
			$data['create_time'] = time();
			$result = $this -> allowField(true) -> save($data);
			if(false == $result){
				$this ->rollback();
				return errorMsg('失败');
			}
		}
		$result = $modelGoods -> saveAll($selectedGoods);
		if(false === $result){
			$this ->rollback();
			return errorMsg('失败');
		}
		$this ->commit();
		if(input('?post.id')){//修改成功后，删除旧图
			delImgFromPaths($oldInfo['first_img'],$data['first_img']);
			delImgFromPaths($oldInfo['second_img'],$data['second_img']);
		}
		return successMsg("成功");
	}

//	/**
//	 * 删除
//	 */
//	public function del($storeId,$tag = true){
//		$data = input('post.');
//		$where = [
//			['store_id','=',$storeId]
//		];
//		if(is_array($data['id'])){
//			$where[] = ['id','in',$data['id']];
//		}else{
//			$where[] = ['id','=',$data['id']];
//		}
//		if($tag){//标记删除
//			$result = $this->where($where)->setField('status',2);
//		}else{
//			$result = $this->where($where)->delete();
//		}
//		if(false !== $result){
//			return successMsg("已删除");
//		}
//		return errorMsg('失败');
//	}
	
}