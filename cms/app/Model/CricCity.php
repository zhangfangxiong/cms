<?php

class Model_CricCity extends Model_Base
{
    const DB_NAME = 'cric_xf';

    const TABLE_NAME = 'City';

    /*
     * 获取城市详情
     */
    public static function getCityByID($ID) {
        $where = array(
            'ID' => $ID
        );
        return self::getRow(array('where'=>$where));
    }

    /*
    * 获取城市列表
    */
    public static function getCitys() {
        $sql = "SELECT c.*,l.CITY_SORT FROM City c LEFT JOIN TB_CITY_LIST l ON c.CityCode = l.CITY_CODE WHERE c.State = 1 AND c.Publish = 1 ORDER BY l.CITY_SORT ASC";

        return self::query($sql, 'all');
    }

    /**
     * 根据城市code获取价格区间
     * @ string $cityCode
     */
    public static function getPriceSection($cityCode){
        $where = array(
            'CityCode' => $cityCode
        );
        return self::getRow(array('where'=>$where));
    }
}