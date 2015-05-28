<?php

class Model_CricCityMap extends Model_Base
{
    const DB_NAME = 'cric_xf';

    const TABLE_NAME = 'CityMap';

    /**
    * 获取列表
    */
    public static function getCityMaps()
    {
        $sql = "select CityDomain cityCode,picUrl,cityId from ".self::TABLE_NAME;

        return self::query($sql, 'all');
    }
}