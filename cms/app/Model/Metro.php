<?php
/**
 * Created by PhpStorm.
 * User: xiaoyao
 * Date: 2015/1/22
 * Time: 10:42
 */

class Model_Metro extends Model_Base
{
    const DB_NAME = 'cms';
    const TABLE_NAME = 't_metro';

    /**
     * @param $p_iMetroID
     * 获取地铁线路下的地铁站信息
     */
    public static function getMetroStation($p_iMetroID)
    {
        $aList = [];
        $aList = Model_MetroStation::getAll(
            [
                'where' => ['iStatus' => 1, 'iMetroID' => $p_iMetroID],
                'order' => 'iOrder ASC'
            ]
        );
        return $aList;

    }

    public static function unableMetro($p_iMetroID, $p_aModifyUser)
    {
        try {
            $ret = self::updData(
                [
                    'iMetroID' => $p_iMetroID,
                    'iStatus' => self::STATUS_INVALID,
                    'sOperationName' => $p_aModifyUser['sUserName'],
                    'iOperationID' => $p_aModifyUser['iUserID']
                ]
            );
            if ($ret) {
                $aList = Model_MetroStation::getPair(
                    [
                        'where' => ['iStatus' => 1, 'iMetroID' => $p_iMetroID],
                        'order' => 'iOrder ASC'
                    ],'iAutoID', 'sStation'
                );
                if ($aList) {
                    foreach ($aList as $key => $value) {
                        Model_MetroStation::updData(['iAutoID' => $key, 'iStatus' => self::STATUS_INVALID]);
                    }
                }
                return true;
            }
            return false;

        } catch (Exception $e) {
            return false;
        }
    }


    public static function enableMetro($p_iMetroID, $p_aModifyUser)
    {
        try {
            $ret = self::updData(
                [
                    'iMetroID' => $p_iMetroID,
                    'iStatus' => self::STATUS_VALID,
                    'sOperationName' => $p_aModifyUser['sUserName'],
                    'iOperationID' => $p_aModifyUser['iUserID']
                ]
            );
            if ($ret) {

                $aList = Model_MetroStation::getPair(
                    [
                        'where' => ['iStatus' => 0, 'iMetroID' => $p_iMetroID],
                        'order' => 'iOrder ASC'
                    ],'iAutoID', 'sStation'
                );
                if ($aList) {
                    foreach ($aList as $key => $value) {
                        Model_MetroStation::updData(['iAutoID' => $key, 'iStatus' => self::STATUS_VALID]);
                    }
                }
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function metroDirection($p_iMetroID)
    {
        return Model_MetroDirection::getAll(
            [
                'where' => [
                    'iMetroID' => $p_iMetroID,
                    'iStatus' => self::STATUS_VALID
                ]
            ]
        );
    }

    public static function stationDirectionRuntime($p_iStationID, $p_iDirectionID)
    {
        return Model_MetroStationRuntime::getRow(
            [
                'where' => [
                    'iStatus' => self::STATUS_VALID,
                    'iStationID' => $p_iStationID,
                    'iDirectionID' => $p_iDirectionID
                ]
            ]
        );
    }


}