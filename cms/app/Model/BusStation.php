<?php

/**
 * Created by PhpStorm.
 * User: xiaoyao
 * Date: 2015/1/26
 * Time: 9:28
 */
class Model_BusStation extends Model_Base
{
    const DB_NAME = 'cms';
    const TABLE_NAME = 't_bus_station';

    /*
    * 通过公交线路ID获取站点
    */
    public static function getStationsByBid($Id)
    {
        if (empty($Id)) {
            return null;
        }
        $aParam = array(
            'where' => array(
                'iBusID' => $Id,
                'iStatus' => 1
            ),
            'order' => 'iCreateTime DESC',
        );
        return self::getAll($aParam);
    }
    /*
    * 通过公交线路名称获取站点
    */
    public static function getStationsByName($name,$iCityID)
    {
        if (empty($name)) {
            return null;
        }
        $sql = "select a.*,b.iType from t_bus_station as a inner join t_bus as b on a.iBusID = b.iBusID where a.sStation like '".addslashes($name)."%' and b.iCityID=$iCityID  and a.iStatus=1 and b.iStatus=1";

        return self::query($sql);
    }

}