<?php
/**
 * Created by PhpStorm.
 * User: cjj
 * Date: 2015/05/04
 * 地图想关操作
 */

class Model_CricMapData
{


    // 缩放级别1(最大,一般城市使用)
    const  ZOOM_LEVEL_1 = 12;
    // 缩放级别2(次之,一般区域使用)
    const  ZOOM_LEVEL_2 = 15;
    // 缩放级别3(再次之,一般用于显示小区信息)
    const  ZOOM_LEVEL_3 = 16;

    /*
     *
     * @param $keywords 关键字
     * @param $category 新房 or 二手房
     */
    public static  function Create($keywords,$category)
    {
        $keywords = trim($keywords);
        if (strpos($keywords, "$") === 0) {
            return null;
        }
        $keywords = strtolower($keywords);
        if (strrchr($keywords, ".css") == '.css') {
            return null;
        }
        if (strrchr($keywords, ".png") == '.png') {
            return null;
        }
        if (strrchr($keywords, ".jpg") == '.jpg') {
            return null;
        }
        if (strrchr($keywords, ".js") == '.js') {
            return null;
        }
        if (strrchr($keywords, ".gif") == '.gif') {
            return null;
        }
        // 搜索行为记录 暂时没写
        $result = self::CreateByKey($keywords, $category);
        return $result;
    }

    public static function CreateByKey()
    {

        //MapData result = MapSearcher.Suggest(city.CityCode, key,category);
    }

}