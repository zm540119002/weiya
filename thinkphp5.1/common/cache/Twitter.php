<?php
namespace common\cache;

class Twitter{
    private static $_cache_key = 'cache_twitter_';
    /**从缓存中获取信息
     */
    public static function get($userId){
        $twitterList = cache(self::$_cache_key.$userId);
        if(!$twitterList){
            $modelTwitter = new \app\twitter\model\Twitter();
            $where = [
                ['uf.status','=',0],
                ['uf.user_id','=',$userId],
            ];
            $field = [
                'uf.is_default','f.id','f.name',
            ];
            $join = [
                ['twitter f','uf.twitter_id = f.id','left'],
            ];
            $twitterList = $modelTwitter->alias('uf')->join($join)->where($where)->field($field)->select();
            $twitterList = $twitterList->toArray();
        }
        cache(self::$_cache_key.$userId, $twitterList,config('custom.twitter_cache_time'));
        return $twitterList;
    }

    /**删除缓存信息
     */
    public static function remove($userId){
        cache(self::$_cache_key.$userId, null);
    }
}