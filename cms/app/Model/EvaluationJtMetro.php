<?php
/**
 * Created by PhpStorm.
 * User: len
 * Date: 2015/3/3
 * Time: 14:10
 */

class Model_EvaluationJtMetro extends Model_Base
{

    const DB_NAME = 'cms';
    const TABLE_NAME = 't_evaluation_jt_metro';
    const CHAPTER = 14; //  当前章节ID

    public static function getEvaluationMetro($p_iEvaluationID)
    {
        $aMetro = self::getRow(
            [
                'where' => [
                    'iStatus' => self::STATUS_VALID,
                    'iEvaluationID' => $p_iEvaluationID
                ]
            ]
        );
        if (!empty($aMetro)) {
            if ($aMetro['iHasMetro'] == 1) {
                $aMetroInfo = Model_EvaluationJtMetroInfo::getAll(
                    [
                        'where' => [
                            'iStatus' => 1,
                            'iJtMetroID' => $aMetro['iAutoID']
                        ]
                    ]
                );
                if ($aMetroInfo) {
                    foreach ($aMetroInfo as $key => $metroInfo) {
                        $metro = Model_Metro::getDetail($metroInfo['iMetroID']);
                        $station = Model_MetroStation::getDetail($metroInfo['iStationID']);
                        $aStationList = Model_Metro::getMetroStation($metroInfo['iMetroID']);
                        $aMetroInfo[$key]['sMetroName'] = $metro['sMetroName'];
                        $aMetroInfo[$key]['sStation'] = $station['sStation'];
                        $aMetroInfo[$key]['aStationList'] = $aStationList;
                        $aMetroInfo[$key]['images'] = Model_EvaluationImage::getImagesByTargetId($metroInfo['iAutoID'],514,$p_iEvaluationID);
                    }
                }
                $aMetro['aMetroInfo'] = $aMetroInfo;
            }

            if (!empty($aMetro['sGoodTag'])) {
                $aMetro['sGoodTag'] = explode(',', $aMetro['sGoodTag']);
            } else {
                $aMetro['sGoodTag'] = [];
            }
            if (!empty($aMetro['sBadTag'])) {
                $aMetro['sBadTag'] = explode(',', $aMetro['sBadTag']);
            } else {
                $aMetro['sBadTag'] = [];
            }
        }
        return $aMetro;
    }

    /**
     * API 接口获取轨交信息（先站点后线路）
     * @param $p_iEvaluationID
     * @return int
     */
    public static function getApiEvaluationMetro($p_iEvaluationID)
    {
        $aMetro = self::getRow(
            [
                'where' => [
                    'iStatus' => self::STATUS_VALID,
                    'iEvaluationID' => $p_iEvaluationID
                ]
            ]
        );
        if (!empty($aMetro)) {
            if ($aMetro['iHasMetro'] == 1) {
                $aMetroInfo = Model_EvaluationJtMetroInfo::getAll(
                    [
                        'where' => [
                            'iStatus' => 1,
                            'iJtMetroID' => $aMetro['iAutoID']
                        ]
                    ]
                );

                if ($aMetroInfo) {
                    foreach ($aMetroInfo as $key => $metroInfo) {
                        $station = Model_MetroStation::getDetail($metroInfo['iStationID']);
                        $aParam = array(
                            'where' => array(
                                'sStation' => $station['sStation'],
                                'iStatus' => 1
                            ),
                        );
                        //根据站名获取地铁线路
                        $aMetroRes = Model_MetroStation::getAll($aParam);
                        if(!empty($aMetroRes)){
                            foreach($aMetroRes as $k=>$v) {
                                $aMetroList[$k] = Model_Metro::getDetail($v['iMetroID']);
                                $aMetroList[$k]['iMorningPeak'] = $metroInfo['iMorningPeak'];
                                $aMetroList[$k]['iEveningPeak'] = $metroInfo['iEveningPeak'];
                            }
                        }
                        $aMetroInfo[$key]['sStation'] = $station['sStation'];
                        $aMetroInfo[$key]['aMetroList'] = $aMetroList;
                        $aMetroInfo[$key]['images'] = Model_EvaluationImage::getImagesByTargetId($metroInfo['iAutoID'],514,$p_iEvaluationID);
                    }
                }
                $aMetro['aMetroInfo'] = $aMetroInfo;
            }

            if (!empty($aMetro['sGoodTag'])) {
                $aMetro['sGoodTag'] = explode(',', $aMetro['sGoodTag']);
            } else {
                $aMetro['sGoodTag'] = [];
            }
            if (!empty($aMetro['sBadTag'])) {
                $aMetro['sBadTag'] = explode(',', $aMetro['sBadTag']);
            } else {
                $aMetro['sBadTag'] = [];
            }
        }
        return $aMetro;
    }

    public static function getMetroInfoID($p_iJtMetroID) {
        $aList = Model_EvaluationJtMetroInfo::getPair(
            [
                'where' => [
                    'iJtMetroID' => $p_iJtMetroID,
                    'iStatus' => 1
                ]
            ],
            'iAutoID',
            'iJtMetroID'
        );
        if ($aList) {
            return array_keys($aList);
        }
        return [];
    }

    public static function delMetroInfoByPkID($p_pkIDs)
    {
        if ($p_pkIDs) {
            foreach ($p_pkIDs as $value) {
                Model_EvaluationJtMetroInfo::delData($value);
            }
        }
        return true;
    }


    public static function updateMetroInfo($p_aDataList)
    {
        $idArr = array();
        if ($p_aDataList) {
            foreach ($p_aDataList as $key => $value) {
                Model_EvaluationJtMetroInfo::updData($value);
                $idArr[$key]  = $value['iAutoID'];
            }
        }
        return $idArr;
    }

    public static function addMetroInfo($p_aDataList, $p_iJtMetroID)
    {
        $idArr = array();
        if ($p_aDataList) {
            foreach ($p_aDataList as $key => $value) {
                $value['iJtMetroID'] = $p_iJtMetroID;
                $idArr[$key] =  Model_EvaluationJtMetroInfo::addData($value);
            }
        }
        return $idArr;
    }

    /**
     * 批量添加轨交出行信息
     */
    public static function addBatchData($data)
    {
        if (!empty($data)) {
            foreach($data as $item) {
                $item = self::getOrm()->filterFields($item);// 过滤数据
                self::addData($item);
            }
        }
    }
    /**
     * 批量修改轨交出行信息
     *
     */
    public static function updateBatchData($data)
    {
        if (!empty($data)) {
            foreach($data as $item) {
                $item = self::getOrm()->filterFields($item);// 过滤数据
                self::updData($item);
            }
        }
    }
}