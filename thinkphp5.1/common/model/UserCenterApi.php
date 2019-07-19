<?php
namespace common\model;
class UserCenterApi extends Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'user';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_common';

	/**登录
	 */
	public function login($data){
		$data['mobile_phone'] = trim($data['mobile_phone']);
		$data['password'] = trim($data['password']);
		$validateUser = new \common\validate\User();
		if(!$validateUser->scene('login')->check($data)) {
			return buildFailed($validateUser->getError());
		}
		if($data['mobile_phone'] && $data['password']){//账号密码登录
			$user = $this->_accountStatusCheck($data['mobile_phone']);
			if(empty($user)){
				return buildFailed('账号不存在');
			}elseif ($user['status'] ==1){
				return buildFailed('账号已禁用');
			}elseif ($user['status'] ==2){
				return buildFailed('账号已删除');
			}
			return $this->_login($data['mobile_phone'],$data['password']);
		}
	}

	/**登录
	 */
	private function _login($mobilePhone,$password){
		$where = array(
			'status' => 0,
			'mobile_phone' => $mobilePhone,
		);
		$field = array(
			'id','name','nickname','mobile_phone','status','type','password','avatar',
			'sex','salt','birthday','last_login_time','role_id',
		);
		$user = $this->field($field)->where($where)->find();
		$user = $user->toArray();
		if($password && !slow_equals($user['password'],md5($user['salt'].$password))){
			return buildFailed('密码错误,请重新输入！');
		}
		//更新最后登录时间
		$this->_setLastLoginTimeById($user['id']);
		return buildSuccess($this->_setCache($user));
	}

	/**注册
	 */
	public function register($data){
		$data['mobile_phone'] = trim($data['mobile_phone']);
		$data['password'] = trim($data['password']);
		$saveData['salt'] = create_random_str(10,0);//盐值;
		$saveData['mobile_phone'] = $data['mobile_phone'];
		$saveData['password'] = md5($saveData['salt'] . $data['password']);
		$saveData['captcha'] = trim($data['captcha']);
		if(!$this->_checkCaptcha($saveData['mobile_phone'],$saveData['captcha'])){
			return buildFailed('验证码错误，请重新获取验证码！');
		}
		$user = $this->_registerCheck($saveData['mobile_phone']);
		$validateUser = new \common\validate\User();
		if(empty($user)){//未注册，则注册账号
			if(!$validateUser->scene('register')->check($data)) {
				return buildFailed($validateUser->getError());
			}
			if(!$this->_register($saveData)){
				return buildFailed('注册失败');
			}
		}elseif ($user['status'] ==1){
			return buildFailed('账号已禁用');
		}elseif ($user['status'] ==2){
			return buildFailed('账号已删除');
		}else{//已注册，正常，则修改密码
			if(!$validateUser->scene('resetPassword')->check($data)){
				return buildFailed($validateUser->getError());
			}
			if(!$this->_resetPassword($saveData['mobile_phone'],$saveData)){
				return buildFailed('重置密码失败');
			}
		}
		return $this->_login($saveData['mobile_phone'],$data['password']);
	}

	/**注册
	 */
	private function _register($saveData){
		$saveData['create_time'] = time();
		$res = $this->isUpdate(false)->save($saveData);
		if(false === $res){
			return false;
		}
		return true;
	}

	/**注册-账号检查
	 */
	private function _registerCheck($mobilePhone){
		$where = [
			['mobile_phone','=',$mobilePhone],
		];
		$field = [
			'id','name','nickname','status',
		];
		$user = $this->where($where)->field($field)->find();
		if(!$user){
			return false;
		}
		return $user;
	}

	/**重置密码
	 */
	private function _resetPassword($mobilePhone,$data){
		$where = array(
			'mobile_phone' => $mobilePhone,
		);
		$saveData = [
			'salt' => $data['salt'],
			'password' => $data['password'],
			'update_time' => time(),
		];
		$res = $this->isUpdate(true)->save($saveData,$where);
		if(false === $res){
			return false;
		}
		return true;
	}

	/**账号状态检查
	 */
	private function _accountStatusCheck($mobilePhone){
		$where = [
			['mobile_phone','=',$mobilePhone],
		];
		$field = [
			'status',
		];
		$user = $this->where($where)->field($field)->find();
		return $user;
	}

	/**更新-最后登录时间
	 */
	private function _setLastLoginTimeById($userId){
		$where = array(
			'id' => $userId,
		);
		$this->where($where)->setField('last_login_time', time());
	}

	/**设置登录session
	 */
	public function _setCache($user){
	    unset($user['password']);
	    unset($user['salt']);
        $userToken = getToken($user);
        $user['backUrl']  = session('backUrl');
        cache('Login:' . $userToken, json_encode($user));
        return [
            'token' => $userToken,
            'user' => $user,
        ];
	}

    /**设置登录session
     */
    public function _setSession($user){
        unset($user['password']);
        unset($user['salt']);
        $userToken = getToken($user);
        $user = array_merge($user,array('rand' => create_random_str(10, 0),));
        session($userToken, $user);
        session('user_sign', data_auth_sign($user));
        return [
            'token' => $userToken,
            'user' => $user,
        ];
        return session('backUrl');
        //返回发起页或平台首页
        $backUrl = session('backUrl');
        $pattern  =  '/index.php\/([A-Z][a-z]*)\//' ;
        preg_match ($pattern,$backUrl,$matches);
        return $backUrl?(is_ssl()?'https://':'http://').$backUrl:url('Index/index');
    }

	/**检查验证码
	 */
	private function _checkCaptcha($mobilePhone,$captcha){
		return cache('captcha_' . $mobilePhone) == $captcha ;
	}
}