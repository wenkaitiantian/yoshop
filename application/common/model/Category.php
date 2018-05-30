<?php

namespace app\common\model;

use think\Cache;

/**
 * 商品分类模型
 * Class Category
 * @package app\common\model
 */
class Category extends BaseModel
{
    protected $name = 'category';

    /**
     * 所有分类
     * @return mixed
     * @throws \think\exception\DbException
     */
    public static function getALL()
    {
        if (!Cache::get('category_' . self::$wxapp_id)) {
            $all = ($data = static::all()) ?  collection($data)->toArray() : [];
            $tree = [];
            foreach ($all as $first) {
                if ($first['parent_id'] !== 0) continue;
                $twoTree = [];
                foreach ($all as $two) {
                    if ($two['parent_id'] !== $first['category_id']) continue;
                    $threeTree = [];
                    foreach ($all as $three)
                        $three['parent_id'] === $two['category_id']
                        && $threeTree[$three['category_id']] = $three;
                    !empty($threeTree) && $two['child'] = $threeTree;
                    $twoTree[$two['category_id']] = $two;
                }
                !empty($twoTree) && $first['child'] = $twoTree;
                $tree[$first['category_id']] = $first;
            }
            Cache::set('category_' . self::$wxapp_id, compact('all', 'tree'));
        }
        return Cache::get('category_' . self::$wxapp_id);
    }


    /**
     * 获取所有分类(树状结构)
     * @return mixed
     * @throws \think\exception\DbException
     */
    public static function getCacheTree()
    {
        return self::getALL()['tree'];
    }

    /**
     * 获取所有分类
     * @return mixed
     * @throws \think\exception\DbException
     */
    public static function getCacheAll()
    {
        return self::getALL()['all'];
    }

}
