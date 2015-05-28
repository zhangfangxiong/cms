<?php

class Model_CricDistrict extends Model_Base
{
    const DB_NAME = 'cric_xf';

    const TABLE_NAME = 'District';



    public static function getDistrictByCID($iCityID, $category = 1)
    {
        $sSQL = "SELECT d.ID, d.RegionName, d.DistrictName
                FROM District d, City c
                WHERE c.ID = $iCityID  and c.CityCode = d.CityCode AND d.State = 1 And d.Category = $category group by DistrictName";
        $aData = self::query($sSQL,'all');
        return $aData;
    }

    /*
     * 获取城市某个坐标附近最近板块的均价
     */
    public static function getDistrictPrice($iCityID, $lng, $lat, $category = 1)
    {
        $sSQL = "SELECT d.Price, SQRT(POW((d.X-$lng)*102.83, 2) + POW((d.Y-$lat)*111.71, 2)) distance FROM District d, City c WHERE d.X > 0 AND d.Y > 0 AND c.ID = $iCityID and c.CityCode = d.CityCode and d.Category = $category and SQRT(POW((d.X-$lng)*102.83, 2) + POW((d.Y-$lat)*111.71, 2)) < 20 ORDER BY distance asc, d.ID desc limit 0, 1";
        $aData = self::query($sSQL,'row');

        return $aData;
    }

}