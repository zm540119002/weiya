<?php
namespace app\index\controller;

class OnlineService extends \common\controller\Base{
    private $user = null;
    public function __construct(){
        parent::__construct();
        //判断是否登录
        $this->user = checkLogin();
    }
    //首页
    public function index(){
        if(request()->isAjax()){
            if($this->user){
                $modelChatMessage = new \common\model\ChatMessage();
                $config = [
                    'field' => [
                        'cm.id','cm.from_id','cm.to_id','cm.read','cm.content','cm.create_time',
                        'u.name','u.avatar',
                    ],'join' => [
                        ['common.user u','u.id = cm.from_id','left'],
                    ],'where' =>
                        'u.status = 0 and cm.status = 0 and cm.type = 1 ' .
                        'and ((cm.from_id = ' .$this->user['id'] . ' and cm.to_id = 17) ' .
                        'or ( cm.from_id = 17' . ' and cm.to_id = ' .$this->user['id'] . '))'
                    ,'order' => [
                        'cm.create_time'=>'desc',
                    ],'limit' => config('custom.chat_page_size'),
                ];
                $messages = $modelChatMessage->getList($config);
                $messages = array_reverse($messages);
                foreach ($messages as &$message){
                    if($this->user['id']==$message['from_id']){
                        $message['who'] = 'me';
                    }else{
                        $message['who'] = 'others';
                    }
                }
                $this->assign('list',$messages);
                return view('list_tpl');
            }
        }else{
            if($this->user){
                $modelChatMessage = new \common\model\ChatMessage();
                $config = [
                    'field' => [
                        'count(id) num',
                    ],'where' =>
                        'cm.status = 0 and cm.type = 1 and cm.read = 0 ' .
                        'and (cm.from_id = 17 and cm.to_id = ' . $this->user['id'] . ') '
                    ,
                ];
                $unreadCount = $modelChatMessage->getList($config);
                $this->assign('unreadCount',$unreadCount[0]['num']);
                $this->assign('loginSign','login');
            }
            $this->assign('welcomeSpeech',config('custom.welcome_speech'));
            $this->assign('responseTime',date('Y-m-d H:i',time()));
            return $this->fetch();
        }
    }
}