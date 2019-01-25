<?php
namespace app\index_admin\controller;

class CustomClient extends Base{
    /**售前
     */
    public function beforeSale(){
        if(request()->isAjax()){
            $modelChatMessage = new \common\model\ChatMessage();
            $config = [
                'field' => [
                    'cm.from_id','cm.to_id',
                    'u.name','u.avatar',
                ],'join' => [
                    ['common.user u','u.id = cm.from_id','left'],
                ],'where' => [
                    ['u.status','=',0],
                    ['cm.status','=',0],
                    ['cm.type','=',1],
                    ['cm.to_id','=',$this->user['id']],
                ],'group' => 'cm.from_id'
                ,
            ];
            $fromUserList = $modelChatMessage->getList($config);
            foreach ($fromUserList as &$fromUser){
                $config = [
                    'field' => [
                        'cm.id','cm.from_id','cm.to_id','cm.read','cm.content','cm.create_time',
                        'u.name','u.avatar',
                    ],'join' => [
                        ['common.user u','u.id = cm.from_id','left'],
                    ],'where' =>
                        'u.status = 0 and cm.status = 0 and cm.type = 1 ' .
                        'and ((cm.from_id = ' . $fromUser['from_id'] . ' and cm.to_id = ' . $this->user['id'] .') ' .
                        'or ( cm.from_id = ' . $this->user['id'] . ' and cm.to_id = ' . $fromUser['from_id'] . '))'
                    ,'order' => [
                        'cm.create_time'=>'desc',
                    ],'limit' => config('custom.chat_page_size'),
                ];
                $fromUser['messages'] = array_reverse($modelChatMessage->getList($config));
            }
            foreach ($fromUserList as &$fromUser){
                $fromUser['unreadCount'] = 0;
                foreach ($fromUser['messages'] as &$message){
                    if($fromUser['from_id']==$message['from_id']){
                        if($message['read']==0){
                            $fromUser['unreadCount'] ++;
                        }
                        $message['who'] = 'others';
                    }else{
                        $message['who'] = 'me';
                    }
                }
            }
            $this->assign('list',$fromUserList);
            return view('tpl');
        }else{
            $this->assign('loginSign','login');
            return $this->fetch();
        }
    }
    /**售后
     */
    public function afterSale(){
        if(request()->isAjax()){
            return successMsg('成功');
        }else{
            return $this->fetch();
        }
    }
}