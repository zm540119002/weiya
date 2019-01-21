<?php
namespace common\model;
use think\facade\Session;
class UserCenter extends Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'user';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_common';

	/**登录-账号检查
	 */
	public function loginCheck($mobilePhone){
		$where = [
			['mobile_phone','=',$mobilePhone],
		];
		$field = [
			'status',
		];
		$user = $this->where($where)->field($field)->find();
		return $user;
	}

	/**登录
	 */
	public function login($data){
		$data['mobile_phone'] = trim($data['mobile_phone']);
		$data['password'] = trim($data['password']);
		$validateUser = new \common\validate\User();
		if($data['mobile_phone'] && $data['password']){//账号密码登录
			if(!$validateUser->scene('login')->check($data)) {
				return errorMsg($validateUser->getError());
			}
			$user = $this->loginCheck($data['mobile_phone']);
			if(!$user){
				return errorMsg('账号不存在！');
			}elseif($user['status']==1){
				return errorMsg('账号异常，请申诉！');
			}
			return $this->_login($data);
		}else{
			return errorMsg('登录信息不完善！');
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
		$user = $this->where($where)->field($field)->find();
		if(!$user){
			return false;
		}
		return $user;
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
		$validateUser = new \common\validate\User();
		if(!$validateUser->scene('resetPassword')->check($data)){
			return errorMsg($validateUser->getError());
		}
		if($data['mobile_phone'] && $data['captcha']){
			if(!$this->_checkCaptcha($data['mobile_phone'],$data['captcha'])){
				return errorMsg('验证码错误，请重新获取验证码！');
			}
			$user = $this->loginCheck($data['mobile_phone']);
			if(!$user){
				if(!$this->_register($data)){
					return errorMsg('注册失败');
				}
				return $this->_login($data);
			}elseif($user['status']==1){
				return errorMsg('账号异常，请申诉！');
			}
			$saveData['salt'] = create_random_str(10,0);//盐值
			$saveData['password'] = md5($saveData['salt'] . $data['password']);//加密
			$where = array(
				'status' => 0,
				'mobile_phone' => $data['mobile_phone'],
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
		$user = $this->_get($data);
		if(!$user){
			return errorMsg('密码错误,请重新输入！');
		}
		//更新最后登录时间
		$this->_setLastLoginTimeById($user['id']);
		return successMsg($this->_setSession($user));
	}

	/**注册
	 */
	private function _register($data){
		$salt = create_random_str(10,0);
		$data['salt'] = $salt;//盐值;
		$data['password'] = md5($salt . $data['password']);//加密;
		$data['name'] = '游客';
		$data['create_time'] = time();
		$this->save($data);
		if(!$this->getAttr('id')){
			return false;
		}
		return true;
	}

	/**更新-最后登录时间
	 */
	private function _setLastLoginTimeById($userId){
		$where = array(
			'id' => $userId,
		);
		$this->where($where)->setField('last_login_time', time());
	}

	/**获取登录信息
	 */
	private function _get($data){
		if(!$data['mobile_phone']) {
			return false;
		}
		$where = array(
			'status' => 0,
			'mobile_phone' => $data['mobile_phone'],
		);
		$field = array(
			'id','name','nickname','mobile_phone','status','type','password','avatar',
			'sex','salt','birthday','last_login_time',
		);
		$user = $this->field($field)->where($where)->find();
		if(!count($user)) {
			return false;
		}
		if($data['password'] && !slow_equals($user['password'],md5($user['salt'].$data['password']))){
			return false;
		}
		return $user->toArray();
	}

	/**设置登录session
	 */
	private function _setSession($user){
		$user = array_merge($user,array('rand' => create_random_str(10, 0),));
		print_r(Session::prefix(''));exit;
		session('user', $user,Session::prefix(''));
		session('user_sign', data_auth_sign($user),Session::prefix(''));
		//返回发起页或平台首页
		$backUrl = session('backUrl','',Session::prefix(''))?:session('returnUrl','',Session::prefix(''));
		$pattern  =  '/index.php\/([A-Z][a-z]*)\//' ;
		preg_match ($pattern,$backUrl,$matches);
		return $backUrl?(is_ssl()?'https://':'http://').$backUrl:url('Index/index');
	}
	
	/**检查验证码
	 */
	private function _checkCaptcha($mobilePhone,$captcha){
		return session('captcha_' . $mobilePhone) == $captcha ;
	}
}