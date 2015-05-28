<?php
/**
 * Created by PhpStorm.
 * User: xiaoyao
 * Date: 2015/2/3
 * Time: 10:49
 */

class Model_CricUnitRecommended extends Model_Base
{

    const DB_NAME = 'cric_xf';
    const TABLE_NAME = 'UnitRecommended';
    /**
     * @param $sType in ['尽快入手', '推荐购买', '谨慎购买', '持币观望']
     * @param $sCityCode
     * @return array
     * 获取城市分析师评楼对应的数据
     */
    public static function analystRecommendUnit($sType, $sCityCode)
    {
        $aWhere = ['ListType' => $sType, 'CityCode' => $sCityCode, 'CricPrice >' => 0];
        $iCnt = self::getCnt(['where' => $aWhere]);
        if ($iCnt > 0) {
            $iRand = mt_rand(0,$iCnt-1);
            $aList = self::getAll(
                [
                    'where' => $aWhere,
                    'limit' => $iRand.',1',
                    'order' => 'ListDate DESC'
                ]
            );

            if ($aList) {

                $aRecommendUnit = $aList[0];
                $aRecommendUnit['sUrl'] = Model_CricUnit::getUnitUrl(
                    $sCityCode,
                    $aRecommendUnit['Region'],
                    $aRecommendUnit['District'],
                    $aRecommendUnit['UnitIndexID']
                );
                return $aRecommendUnit;
            }
            return [];
        }
        return [];
    }

    public static function getAnalystRecommendedUnit($mType, $sCityCode)
    {
        $aList = [];
        if ($sCityCode) {
            if (is_array($mType)) {
                foreach ($mType as $value) {
                    $aRet = Model_CricUnitRecommended::analystRecommendUnit($value, $sCityCode);
                    if ($aRet) {
                        array_push($aList, $aRet);
                    }
                }
            } else {
                $aRet = Model_CricUnitRecommended::analystRecommendUnit($mType, $sCityCode);
                if ($aRet) {
                    array_push($aList, $aRet);
                }
            }
        }
        return $aList;
    }

    /*
     * 主站新房的分析师评楼 各种推荐等级随机取一个
     */
    public static function unitRecommendedList($sCityCode)
    {
        $aList = self::getAll(
            array(
                'where' => array(
                        'CityCode' => $sCityCode,
                    ),
                'order' => 'ListDate DESC'
            )
        );
        $unitRecommended['jkrs'] = array(); // 尽快入手
        $unitRecommended['tjgm'] = array(); // 推荐购买
        $unitRecommended['jsgm'] = array(); // 谨慎购买
        $unitRecommended['cbgw'] = array(); // 持币观望
        if (!empty($aList)) {
            foreach($aList as $key => $item) {
                if ($item['ListType'] == '尽快入手') {
                    $unitRecommended['jkrs'][] = $item;
                }
                if ($item['ListType'] == '推荐购买') {
                    $unitRecommended['tjgm'][] = $item;
                }
                if ($item['ListType'] == '谨慎购买') {
                    $unitRecommended['jsgm'][] = $item;
                }
                if ($item['ListType'] == '持币观望') {
                    $unitRecommended['cbgw'][] = $item;
                }
            }
        }
        function disposeArr($arr) {
            if (empty($arr)) {
                return array();
            }
            $firstItem = reset($arr);
            $newArr  = array();
            foreach ($arr as $item) {
                $year= date('Y',strtotime($item['ListDate']));
                $month= date('m',strtotime($item['ListDate']));
                if ($year==date('Y',strtotime($firstItem['ListDate'])) && $month==date('m',strtotime($firstItem['ListDate']))) {
                    $newArr[] = $item;
                }
            }
            return $newArr;
        }
        foreach($unitRecommended as $k => $item) {
            $unitRecommended[$k] = disposeArr($unitRecommended[$k]);
            if (!empty($unitRecommended[$k])) {
                $count = sizeof($unitRecommended[$k]);
                $iRand = mt_rand(0,$count-1);
                $unitRecommended[$k]  = $unitRecommended[$k][$iRand];
            }
        }
        return $unitRecommended;
    }

}