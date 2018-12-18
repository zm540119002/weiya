<?php
namespace common\model;

class Goods extends Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'goods';
	// 设置主键
	protected $pk = 'id';
	// 别名
	protected $alias = 'g';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_common';

	/**编辑 新增和修改
	 */
	public function edit($storeId ='',$runType = ''){
		$data = input('post.');
		$data['run_type'] = $runType;
		if($this->_isExistGoodsName($data,$storeId)) {
			return errorMsg('本店已存在此商品名，请更改别的商品名');
		}
		$validate = new \common\validate\Goods();
		 if(!$result = $validate->check($data)) {
		 	return errorMsg($validate->getError());
		 }
		if(!empty($data['thumb_img'])){
			$data['thumb_img'] = moveImgFromTemp(config('upload_dir.factory_goods'),basename($data['thumb_img']));
		}
		if(!empty($data['main_img'])){
			$tempMainImg = explode(",",$data['main_img']);
			array_pop($tempMainImg);
			$mainImg =[];
			foreach ($tempMainImg as $item) {
				if($item){
					$mainImg[] = moveImgFromTemp(config('upload_dir.factory_goods'),basename($item));
				}
			}
			$data['main_img'] = implode(",", $mainImg).',';
		}
		if(!empty($data['goods_video'])){
			$data['goods_video'] = moveImgFromTemp(config('upload_dir.factory_goods'),basename($data['goods_video']));
		}
		if(!empty($data['details_img'])){
			$tempArray = explode(",",$data['details_img']);
			array_pop($tempArray);
			$detailsImg = [];
			foreach ($tempArray as $item) {
				if($item){
					$detailsImg[] = moveImgFromTemp(config('upload_dir.factory_goods'),basename($item));
				}
			}
			$data['details_img'] = implode(",", $detailsImg).',';
		}
		if(input('?post.id')){//修改
			$config = [
				'where' => [
					['id','=',$data['id']],
				],
				'field' => [
					'thumb_img','main_img','details_img','goods_video'
				],
			];
			$oldGoodsInfo = $this -> getInfo($config);
			if(empty($oldGoodsInfo)){
				return errorMsg('没有数据');
			}
			$data['update_time'] = time();
			$result = $this->allowField(true)->save($data, ['id' => $data['id'],'store_id'=>$storeId]);
		}else{
			$data['create_time'] = time();
			$data['store_id'] = $storeId;
			$result = $this -> allowField(true) -> save($data);
			if(!$result){
				return errorMsg('失败');
			}

		}
		if(false !== $result){
			if(input('?post.id')){//删除旧图片
				delImgFromPaths($oldGoodsInfo['thumb_img'],$data['thumb_img']);
				//删除就图片
				$oldMainImg = explode(",",$oldGoodsInfo['main_img']);
				array_pop($oldMainImg);
				$newMainImg = explode(",",$data['main_img']);
				array_pop($newMainImg);
				delImgFromPaths($oldMainImg,$newMainImg);

				$oldDetailsImg = explode(",",$oldGoodsInfo['details_img']);
				array_pop($oldDetailsImg);
				$newDetailsImg = explode(",",$data['details_img']);
				array_pop($newDetailsImg);
				delImgFromPaths($oldDetailsImg,$newDetailsImg);

				delImgFromPaths($oldGoodsInfo['goods_video'],$data['goods_video']);
			}
			return successMsg("成功");
		}else{
			return errorMsg('失败');
		}
	}

	//检查本店的商品是否同名,
	private function _isExistGoodsName($data,$storeId){
		$name = $data['name'];
		$where = [
			['store_id','=',$storeId],
			['name','=',$name],
		];
		if(isset($data['id']) && (int)$data['id']){//
			$id = $data['id'];
			$where[] =  ['id','<>',$id];
		}
		return $this->where($where)->count() ? true : false;
	}

	//设置库存
	public function setInventory($storeId=''){
		$data = input('post.');
		if(empty($data['id'] || !(int)$data['id'])){
			return errorMsg("参数错误");
		}
		$where = [
			['id','=',(int)$data['id']],
			['store_id','=',$storeId],
		];
		$result = $this->where($where)->setInc('inventory',(int)$data['num'] );
		if(false !== $result){
			return successMsg("成功");
		}else{
			return errorMsg("失败");
		}
	}
}