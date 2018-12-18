<?php
namespace common\cache;

class Store{
    private static $_cache_key = 'cache_store_';

    /**从缓存中获取入驻厂商店铺列表
     */
    public static function get($factorId){
        $storeList = cache(self::$_cache_key.$factorId);
        if(!$storeList){
            $model = new \common\model\Store();
            $config = [
                'where' => [
                    ['s.factory_id','=',$factorId]
                ],'join' => [
                    ['record r','r.id = s.foreign_id','left'],
                    ['brand b','b.id = s.foreign_id','left'],
                ],'field' => [
                    's.id','s.store_type','s.run_type','s.is_default',
                    'case s.store_type when 1 then r.logo_img when 2 then b.brand_img END as logo_img',
                    'case s.store_type when 1 then r.short_name when 2 then b.name END as name',
                ],
            ];
            $storeList = $model->getList($config);
            cache(self::$_cache_key.$factorId, $storeList,config('custom.factory_cache_time'));
        }
        return $storeList;
    }

    /**删除缓存入驻厂商店铺列表
     */
    public static function remove($factorId){
        cache(self::$_cache_key.$factorId, null);
    }
}