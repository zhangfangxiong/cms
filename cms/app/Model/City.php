<?php

class Model_City extends Model_Base
{

    const DB_NAME = 'permission';

    const TABLE_NAME = 't_city';

    /**
     * 跟据城市id取得城市
     * @param int $iCid
     * @return array
     */
    public static function getCityByID($iCid)
    {
        return self::getDetail($iCid);
    }

    /**
     * 跟据城市名称取得城市
     * @param unknown $sCityName
     * @return Ambigous <number, multitype:, mixed>
     */
    public static function getCityByName($sCityName)
    {
        return self::getRow(array(
            'where' => array(
                'sCityName' => $sCityName
            )
        ));
    }

    /**
     * 跟据城市简拼取得城市
     * @param unknown $sPinyin
     * @return Ambigous <number, multitype:, mixed>
     */
    public static function getCityByPinyin($sPinyin)
    {
        return self::getRow(array(
            'where' => array(
                'sPinyin' => $sPinyin
            )
        ));
    }


    /**
     * 跟据城市全拼取得城市
     * @param unknown $sPinyin
     * @return Ambigous <number, multitype:, mixed>
     */
    public static function getCityByFullPinyin($sFullPinyin)
    {
        return self::getRow(array(
            'where' => array(
                'sFullPinyin' => $sFullPinyin
            )
        ));
    }
    /**
     * 取得所有启用城市的ID => Name数组
     * @return Ambigous <number, multitype:multitype:, multitype:unknown >
     */
    public static function getPairCitys()
    {
        return self::getPair(array('where' => array('iShow' => 1, 'iStatus' => 1)), 'iCityID', 'sCityName');
    }

    /**
     * 取得指定城市的ID => Name数组
     * @return Ambigous <number, multitype:multitype:, multitype:unknown >
     */
    public static function getPairCitysByUser($aIDs)
    {
        if (!empty($aIDs)) {
            return self::getPair(array('where' => array('iShow' => 1, 'iStatus' => 1, 'iCityID IN' => $aIDs)), 'iCityID', 'sCityName');
        }
        return array();
    }

    /**
     * @param $sName
     * @return array
     * 根据城市名和拼音检索城市
     */
    public static function searchAutoComplete($sName)
    {
        $sSQL = "SELECT
                *
                FROM ".self::TABLE_NAME."
                WHERE sCityName LIKE '" . addslashes($sName) . "%' OR sPinyin LIKE '%" . addslashes($sName) . "%' OR sFullPinyin LIKE '%" . $sName . "%'
                LIMIT 10
            ";
        return self::query($sSQL);
    }
}