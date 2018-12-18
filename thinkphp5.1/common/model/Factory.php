<?php
namespace common\model;
use think\Model;
use think\Db;
/**
 * 基础模型器
 */

class Factory extends Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'factory';
	// 设置主键
	protected $pk = 'id';
	// 别名
	protected $alias = 'f';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_common';

	/**编辑
	 */
	public function edit($uid='',$type = ''){
		$data = input('post.');
		$data['user_id'] = $uid;
		$data['type'] = $type;//类型
		$validate = new \common\validate\Factory();
		if(!$result = $validate->check($data)) {
			return errorMsg($validate->getError());
		}
		$data['business_license'] = moveImgFromTemp(config('upload_dir.factory_auto'),basename($data['business_license']));
		$data['auth_letter'] = moveImgFromTemp(config('upload_dir.factory_auto'),basename($data['auth_letter']));

		if(input('?post.factory_id')){
			//查找当前的factory_id的入驻信息
			$config = [
				'where' => [
					['id', '=', $data['factory_id']],
				],
				'field' => [
					'business_license','auth_letter',
				],
			];
			$oldFactoryInfo = $this -> getInfo($config);
			$data['update_time'] = time();
			$result = $this->allowField(true)->save($data,['id' => $data['factory_id']]);
			if(false !== $result){
				delImgFromPaths($oldFactoryInfo['business_license'],$data['business_license']);
				delImgFromPaths($oldFactoryInfo['auth_letter'],$data['auth_letter']);
				return successMsg("成功");
			}else{
				return errorMsg('失败');
			}
		}else{
			$data['create_time'] = time();
			$this -> startTrans();
			$result = $this->allowField(true)->save($data);
			if(!$result){
				$this ->rollback();
				return errorMsg('失败');
			}
			$factoryUserModel =  new \common\model\UserFactory;
			$data2['user_id'] = $uid;
			$data2['factory_id'] = $this->getAttr('id');
			$data2['factory_type'] = $type;
			$result = $factoryUserModel -> allowField(true) -> save($data2);
			if(!$result){
				$this ->rollback();
				return errorMsg('失败');
			}
			$this ->commit();
			return successMsg('提交申请成功');
		}
	}

}