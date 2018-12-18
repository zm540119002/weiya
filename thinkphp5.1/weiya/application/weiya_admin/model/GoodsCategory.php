<?php
namespace app\admin\model;

class GoodsCategory extends\common\model\Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'goods_category';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_weiya';
	// 别名
	protected $alias = 'gc';

	//编辑
	public function edit(){
		$postData = input('post.');
		$validateGoodsCategory = new \common\validate\GoodsCategory();
		if(!$validateGoodsCategory->scene('edit')->check($postData)){
			return errorMsg($validateGoodsCategory->getError());
		}
		if( isset($postData['img']) && $postData['img'] ){
			$postData['img'] = moveImgFromTemp(config('upload_dir.weiya_goods_gategory'),basename($postData['img']));
		}
		if($postData['id'] && intval($postData['id'])){
			$config = [
				'where' => [
					'id' => $postData['id'],
					'status' => 0,
				],
			];
			$info = $this->getInfo($config);
			//删除商品主图
			if($info['img']){
				delImgFromPaths($info['img'],$postData['img']);
			}
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
	
}