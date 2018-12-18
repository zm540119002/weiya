<?php
namespace common\model;

/**基础模型器
 */
class Store extends Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'store';
	// 设置主键
	protected $pk = 'id';
	// 别名
	protected $alias = 's';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_common';

	/**编辑
	 */
	public function edit($factoryId,$userId=0){
		$data = input('post.');
		$data['factory_id'] = $factoryId;
		$validate = validate('\common\validate\Store');
		if(input('?post.id')){
			if(!$result = $validate->scene('edit')->check($data)) {
				return errorMsg($validate->getError());
			}
			$data['update_time'] = time();
			$result = $this->allowField(true)->save($data,['id' => $data['store_id'],'factory_id'=>$factoryId]);
			if(false !== $result){
				return successMsg("成功");
			}
			return errorMsg('失败',$this->getError());
		}else{
			if(!$result = $validate->scene('add')->check($data)) {
				return errorMsg($validate->getError());
			}
			$this->startTrans();
			$data['create_time'] = time();
			$result = $this->allowField(true)->save($data);
			if(!$result){
				$this->rollback();//事务回滚
				return errorMsg('失败');
			}
			$modelUserStore = new \common\model\UserStore();
			$storeId = $this->getAttr('id');
			$postData['type'] = 1;//拥有者
			$postData['factory_id'] = $factoryId;
			$postData['user_id'] = $userId;
			$postData['store_id'] = $storeId;
			$result = $modelUserStore->save($postData);
			if(!$result){
				$this->rollback();//事务回滚
				return errorMsg('失败',$this->getError());
			}
			$this->commit();//事务提交
			return successMsg('提交申请成功');
		}
	}
	
	//设置默认店铺
	public function setDefaultStore($factoryId=''){
		if(request()->isAjax()){
			$id = (int)input('post.id');
			if(!$id){
				return errorMsg('参数错误');
			}
			$this->startTrans();
			$data = array('is_default' => 1);
			$result = $this->allowField(true)->save($data,['id' => $id,'factory_id'=>$factoryId]);
			if(false === $result){
				$this->rollback();
				return errorMsg('修改默认失败');
			}
			$where = [
				['id','<>',$id],
				['factory_id','=',$factoryId],
			];
			$result = $this ->where($where)->setField('is_default',0);
			if(false === $result){
				$this->rollback();
				return errorMsg('修改失败');
			}
			$this->commit();
			return successMsg("已选择");
		}
	}

	/**检查店铺是否属于此厂商
	 */
	public function checkStoreExist($id,$factoryId){
		$where = [
			['id','=',$id],
			['factory_id','=',$factoryId],
		];
		$count = $this->where($where)->count();
		if($count){
			return true;
		}else{
			return false;
		}
	}

	//设置店长
	public function setManager($factoryId){
		$postData = input('post.');
		$postData['name'] = trim($postData['name']);
		$postData['mobile_phone'] = trim($postData['mobile_phone']);
		$storeId = (int)$postData['id'];
		if(!$storeId){
			return errorMsg('缺少店铺ID');
		}
		if($postData['mobile_phone']){//手机号存在
			//验证用户是否存在
			$userId = $this->checkUserExistByMobilePhone($postData['mobile_phone']);
			$this->startTrans();//事务开启
			if(!$userId){//不存在
				$saveData = [
					'type' => 1,
					'name' => $postData['name'],
					'mobile_phone' => $postData['mobile_phone'],
					'create_time' => time(),
				];
				$modelUser = new \common\model\User();
				$res = $modelUser->isUpdate(false)->save($saveData);
				if($res===false){
					$modelUser->rollback();//事务回滚
					return errorMsg('失败',$modelUser->getError());
				}
				$userId = $modelUser->getAttr('id');
			}
			//验证店铺店长是否存在
			$userStoreId = $this->checkManagerExist($factoryId,$storeId);
			$modelUserStore = new \common\model\UserStore();
			if($userStoreId){//修改
				$where = [
					['type', '=', 3],
					['id', '=', $userStoreId],
					['factory_id', '=', $factoryId],
					['store_id', '=', $storeId],
				];
				$saveData = [
					'user_id' => $userId,
					'user_name' => $postData['name'],
				];
				$res = $modelUserStore->isUpdate(true)->save($saveData,$where);
				if($res===false){
					$this->rollback();//事务回滚
					return errorMsg('失败',$this->getError());
				}
			}else{//新增
				$saveData = [
					'type' => 3,
					'user_id' => $userId,
					'factory_id' => $factoryId,
					'store_id' => $storeId,
					'user_name' => $postData['name'],
				];
				$res = $modelUserStore->isUpdate(false)->save($saveData);
				if($res===false){
					$this->rollback();//事务回滚
					return errorMsg('失败',$this->getError());
				}
			}
			$this->commit();//事务提交
			$postData['id'] = $userId;
		}else{//手机号不存在
			$modelUserStore = new \common\model\UserStore();
			$where = [
				['type', '=', 3],
				['factory_id', '=', $factoryId],
				['store_id', '=', $storeId],
			];
			$res = $modelUserStore->del($where);
			if($res['status']==0){
				return errorMsg('失败',$this->getError());
			}
		}
		return successMsg('成功！',$postData);
	}

	/**验证店铺店长是否存在
	 */
	private function checkManagerExist($factoryId,$storeId){
		$modelUserStore = new \common\model\UserStore();
		$where = [
			['store_id','=',$storeId],
			['factory_id','=',$factoryId],
			['status','<>',2],
			['type','=',3],
		];
		return $modelUserStore->where($where)->value('id');
	}
}