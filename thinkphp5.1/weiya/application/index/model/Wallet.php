<?php
namespace app\index\model;

class Wallet extends \common\model\Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'wallet';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_weiya';
	//表的别名
	protected $alias = 'w';

	/**登录-账号检查
	 */
	public function loginCheck($uid){
		$where = [
			['user_id','=',$uid],
		];
		$field = [
			'status',
		];
		$wallet = $this->where($where)->field($field)->find();
		return $wallet;
	}

	/**登录
	 */
	public function login($data){
		//		$validateUser = new \common\validate\User();
//		if(!$validateUser->scene('resetPassword')->check($data)){
//			return errorMsg($validateUser->getError());
//		}
		$data['password'] = trim($data['password']);
		if($data['password']){//账号密码登录
			$wallet = $this->loginCheck($data['user_id']);
			if(!$wallet){
				return errorMsg('账号不存在！');
			}elseif($wallet['status']==1){
				return errorMsg('账号异常，请申诉！');
			}
			return $this->_login($data);
		}else{
			return errorMsg('密码错误！');
		}
	}

	/**注册-账号检查
	 */
	public function registerCheck($mobilePhone){
		$where = [
			['mobile_phone','=',$mobilePhone],
			['status','<>',2],
		];
		$field = [
			'id','name','nickname',
		];
		$wallet = $this->where($where)->field($field)->find();
		if(!$wallet){
			return false;
		}
		return $wallet;
	}

	/**注册
	 */
	public function register($data){
		$data['mobile_phone'] = trim($data['mobile_phone']);
		$data['password'] = trim($data['password']);
		$data['captcha'] = trim($data['captcha']);
		if(!$this->_checkCaptcha($data['mobile_phone'],$data['captcha'])){
			return errorMsg('验证码错误，请重新获取验证码！');
		}
		$res = $this->registerCheck($data['mobile_phone']);
		if($res){
			return errorMsg('该手机号码已被注册，请更换手机号码，谢谢！');
		}
		$validateUser = new \common\validate\User();
		if(!$validateUser->scene('register')->check($data)) {
			return errorMsg($validateUser->getError());
		}
		if(!$this->_register($data)){
			return errorMsg('注册失败');
		}
		return $this->_login($data);
	}

	/**重置密码
	 */
	public function resetPassword($data){
		$validate = new \common\validate\Wallet();
		if(!$validate->scene('resetPassword')->check($data)){
			return errorMsg($validate->getError());
		}
		if($data['user_id'] && $data['captcha']){
			if(!$this->_checkCaptcha($data['mobile_phone'],$data['captcha'])){
				return errorMsg('验证码错误，请重新获取验证码！');
			}
			$wallet = $this->loginCheck($data['user_id']);
			if(!$wallet){
				if(!$this->_register($data)){
					return errorMsg('注册失败');
				}
				return $this->_login($data);
			}elseif($wallet['status']==1){
				return errorMsg('账号异常，请申诉！');
			}
			$saveData['salt'] = create_random_str(10,0);//盐值
			$saveData['password'] = md5($saveData['salt'] . $data['password']);//加密
			$where = array(
				'status' => 0,
				'user_id' => $data['user_id'],
			);
			$response = $this->where($where)->update($saveData,$where);
			if(!$response){
				return errorMsg('重置失败！');
			}
			return $this->_login($data);
		}
		return errorMsg('资料缺失！');
	}

	/**登录
	 */
	private function _login($data){
		$wallet = $this->_get($data);
		if(!$wallet){
			return errorMsg('密码错误,请重新输入！');
		}
        unset($data['password']);
		return successMsg(json_encode($data));
	}

	/**注册
	 */
	private function _register($data){
		$salt = create_random_str(10,0);
		$data['salt'] = $salt;//盐值;
		$data['password'] = md5($salt . $data['password']);//加密;
		$data['create_time'] = time();
		$this->save($data);
		if(!$this->getAttr('id')){
			return false;
		}
		return true;
	}

	/**更新-最后登录时间
	 */
	private function _setLastLoginTimeById($walletId){
		$where = array(
			'id' => $walletId,
		);
		$this->where($where)->setField('last_login_time', time());
	}

	/**获取登录信息
	 */
	private function _get($data){
		if(!$data['user_id']) {
			return false;
		}
		$where = array(
			'status' => 0,
			'user_id' => $data['user_id'],
		);
		$wallet = $this->where($where)->find();
		if(!count($wallet)) {
			return false;
		}
		if($data['password'] && !slow_equals($wallet['password'],md5($wallet['salt'].$data['password']))){
			return false;
		}
		return $wallet->toArray();
	}
	
	/**检查验证码
	 */
	private function _checkCaptcha($mobilePhone,$captcha){
		return session('captcha_' . $mobilePhone) == $captcha ;
	}
}