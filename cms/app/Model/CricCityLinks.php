<?php

class Model_CricCityLinks extends Model_Base
{
    const DB_NAME = 'cric_xf';

    const TABLE_NAME = 'CityLinks';

    /**
     * 获取友情链接
     */
    public static function getLink($CityCode){
        $sql = "select Name,Url from CityLinks where CityCode in ('common', '$CityCode') and State > 0 order by sort asc";
        return self::query($sql,'all');
    }
}