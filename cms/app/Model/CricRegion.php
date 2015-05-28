<?php

class Model_CricRegion extends Model_Base
{
    const DB_NAME = 'cric_xf';

    const TABLE_NAME = 'Region';

    public static function getRegionByCID($iCityID, $category = 1)
    {
        $sSQL = "SELECT r.ID, r.RegionName
                FROM Region r, City c
                WHERE c.ID = $iCityID and c.CityCode = r.CityCode AND r.State = 1 and r.Category = $category";
        $aData = self::query($sSQL,'all');
        return $aData;
    }

    public static function getRegionByZone($iCityID, $minLon, $minLat, $maxLon, $maxLat, $category = 1)
    {
        $sSQL = "SELECT r.ID, r.RegionName, r.PriceList, r. Price, r.BiddingPrice, r.X, r.Y, r.NewUnitCount
                FROM Region r, City c
                WHERE c.ID = $iCityID and r.Category = $category and c.CityCode = r.CityCode AND r.State = 1 and r.X <= $maxLon and r.X >= $minLon and r.Y <= $maxLat and r.Y >= $minLat";
        $aData = self::query($sSQL,'all');
        return $aData;
    }

    //根据城市code获取城市区域
    public static function getCityRegion($cityCode)
    {
        $where = array(
            'CityCode' => $cityCode
        );
        return self::getAll(array('where'=>$where));
    }
}