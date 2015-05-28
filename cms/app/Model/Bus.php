<?php

/**
 * Created by PhpStorm.
 * User: xiaoyao
 * Date: 2015/1/26
 * Time: 9:27
 */
class Model_Bus extends Model_Base
{

    const DB_NAME = 'cms';
    const TABLE_NAME = 't_bus';

    public static function getBusList($p_aParams, $p_iPage, $p_sOrder = 'iBusID DESC')
    {
        $aWhere = [];
        if(isset($p_aParams['iStatus']) && $p_aParams['iStatus'] != -1){
            $aWhere['iStatus'] = $p_aParams['iStatus'];
        }
        if (isset($p_aParams['iCityID']) && !empty($p_aParams['iCityID'])) {
            $aWhere['iCityID'] = $p_aParams['iCityID'];
        }

        if (isset($p_aParams['iType']) && $p_aParams['iType'] != -1) {
            $aWhere['iType'] = intval($p_aParams['iType']);
        }

        if (isset($p_aParams['sBusName']) && !empty($p_aParams['sBusName'])) {
            $aWhere['sBusName LIKE'] = $p_aParams['sBusName'] . '%';
        }
        return self::getList($aWhere, $p_iPage, $p_sOrder);

    }

    public static function searchAutoComplete($key, $cityId)
    {
        $aWhere = array(
            'iStatus' => 1,
            'iCityID' => $cityId,
            'sBusName LIKE' => $key. '%'
        );
        return self::getAll(['where' => $aWhere]);
    }

    /**
     * 通过站点名称获取线路
     * @author cjj
     */
    public static function getStationsByName($name,$cityID)
    {
       if (empty($name)) {
           return null;
       }
        $result = Model_BusStation::getAll(array(
            'where' => array(
                'iStatus' => 1,
                'sStation LIKE '=> $name.'%',
            )
        ));
        if (empty($result)) {
            return null;
        }
        $busIdArr = array();
        foreach ($result as $item) {
            $busIdArr[] = $item['iBusID'];
        }
        return Model_Bus::getAll(array(
            'where' => array(
                'iStatus' => 1,
                'iBusID in' => implode(',',$busIdArr),
                'iCityID' => $cityID,
            )
        ));
    }

}