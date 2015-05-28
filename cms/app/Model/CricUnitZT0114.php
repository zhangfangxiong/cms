<?php
/**
 * Created by PhpStorm.
 * User: yaobiqing
 * Date: 15/4/9
 * Time: 下午1:18
 */

class Model_CricUnitZT0114 extends Model_Base
{
    const TABLE_NAME = 'UnitZhuanti0114';
    const DB_NAME = 'cric_xf';

    /**
     * @param $sCityName
     * 根据城市获取榜单名称
     */
    public static function getListTypeByCityName($sCityName, $sTime, $sDateType)
    {
        $iTime = strtotime($sTime);

        $sSQL = "SELECT * FROM (SELECT * FROM ".self::TABLE_NAME." ".
                " WHERE State = 1".
                " AND CityCaption = '".$sCityName."') aa LEFT JOIN".
                " (SELECT ListID, COUNT(1) AS ListCnt FROM ".Model_CricUnitZT0114Detail::TABLE_NAME." WHERE state =1".
                " GROUP BY ListID) bb".
                " ON aa.ItemID=bb.ListID";

        $sFSql = $sSQL." WHERE
                 aa.ListType = 'TOP榜' AND date_format(aa.ListDateTime, '%Y-%m-%d')='".date('Y-m-d', $iTime)."'
                 AND aa.DateType = '".$sDateType."' AND aa.CustomType = 1 AND bb.ListCnt >= 5
                 ORDER BY ListCnt desc, RankPosition ASC Limit 1";

        $ListType = self::query($sFSql);
        $iCount = count($ListType);
        if ($iCount < 6) {
            $sLimit = " LIMIT 0, ".(6 - $iCount);
            $sSSql = $sSQL." WHERE
                ((aa.ListType = 'TOP榜' AND aa.CustomType = 2) OR aa.ListType = '最佳户型榜')AND
                date_format(aa.ListDateTime, '%Y-%m-%d')='".date('Y-m-d', $iTime)."'
                 AND aa.DateType = '".$sDateType."'  AND bb.ListCnt >= 5
                 ORDER BY ListCnt desc, RankPosition ASC".$sLimit;
            $extListType = self::query($sSSql);
        }
        if ($extListType) {
            foreach($extListType as $value) {
                array_push($ListType, $value);
            }
        }
        return $ListType;
    }

    public static function replaceListTitle($ListType, $regArr, $replaceArr)
    {
        $result = [];
        if (!empty($ListType)) {
            foreach($ListType as $key => $value) {
                if ($value['ListType'] == "TOP榜" && $value['CustomType'] == 1) {
                    $sName = '楼盘TOP榜';
                } else {
                    $sName = str_replace($regArr, $replaceArr, strtoupper($value['Title']));
                }
                $result[] = [
                    'sCode' => $value['ItemID'],
                    'sName' => $sName
                ];
            }
        }
        return $result;
    }


    public static function getCurrYearListType($sCity, $bReplace = true)
    {
        $iTime = time();
        $dateType = '季度';
        //$dateType = '年度';

        //$iTime = strtotime('2014-01-01');
        $sDate = date('Y-01-01', $iTime);
        $typeList = Model_CricUnitZT0114::getListTypeByCityName($sCity, $sDate, $dateType);
        $year = date('Y年', $iTime);
        $year2 = date('Y', $iTime);

        $regArr = [
            $year, $year2, '第一季度', '一季度', '楼盘', '性价比', 'TOP3', 'TOP5', 'TOP10', '测评榜'
        ];

        $replaceArr = [
            '', '', '', '', '', '', '', '', '', '榜单'
        ];

//        $regArr = [
//            '楼盘', '性价比', '测评榜'
//        ];
//
//        $replaceArr = [
//            '', '', '榜单'
//        ];
        if ($bReplace) {
            return self::replaceListTitle($typeList, $regArr, $replaceArr);
        } else {
            return $typeList;
        }
    }

    public static function getZhuantiDetail($sHomeID, $sListID)
    {
        $sSQL = "SELECT * FROM ".Model_CricUnitZT0114Detail::TABLE_NAME." WHERE HomeID='".$sHomeID."'".
            " AND State = 1 AND ListID='".$sListID."'";
        return self::query($sSQL, "row");
    }

    public static function getUnitListType($sHomeID)
    {
        $aUnit = Model_CricUnit::getRow([
                'where' => [
                    'HomeID' => $sHomeID
                ]
            ]);
        $aListType = [];
        if (!empty($aUnit)) {
            $aCity = Model_City::getCityByFullPinyin($aUnit['CityCode']);
            if ($aCity) {
                $listType = self::getCurrYearListType($aCity['sCityName'], true);
                if ($listType) {
                    foreach($listType as $key => $value) {
                        $row = self::getZhuantiDetail($sHomeID, $value['sCode']);
                        if ($row) {
                            $aListType[] = ['sTagName' => $value['sName']];
                        }
                    }
                }
            }
        }
        return $aListType;
    }

    /**
     * @param $sListType
     * 获取list Type 对应的unitID
     */
    public static function getListTypeUnit($sItemID)
    {
        return Model_CricUnitZT0114Detail::getAll(
            [
                'where' => [
                    'ListID' => $sItemID,
                    'State' => 1
                ]
            ]
        );
    }

    /**
     * 获取预开盘推荐
     */
    public static  function getYuKaiPan($data)
    {
        if(!empty($data)){
            $ListType = $data['ListType'];
            $CityCaption = $data['cityName'];
            $sql = "SELECT * FROM ".self::TABLE_NAME." WHERE ListType = '$ListType' AND CustomType = ".$data['CustomType']."
                    AND CityCaption = '$CityCaption' ORDER BY ListDateTime ASC";
            $info = self::query($sql, "row");
            //获取楼盘详细信息
            $yuList = self::getUnitInfo($info);
            return $yuList;
        }else{
            return null;
        }
    }

    /**
     * 获取新开盘
     */
    public static function getXinKaiPan($data){
        if(!empty($data)){
            $ListType = $data['ListType'];
            $CityCaption = $data['cityName'];
            $sql = "SELECT * FROM ".self::TABLE_NAME." WHERE ListType = '$ListType' AND CustomType = ".$data['CustomType']."
                    AND CityCaption = '$CityCaption' AND DATE_FORMAT(ListDateTime,'%Y-%m-%d') = '2015-03-01'";
            $info = self::query($sql, "row");
            if(empty($info)){
                $asql = "SELECT * FROM ".self::TABLE_NAME." WHERE ListType = '$ListType' AND CustomType = ".$data['CustomType']."
                    AND CityCaption = '$CityCaption' AND DATE_FORMAT(ListDateTime,'%Y-%m-%d') = '2015-02-01'";
                $info = self::query($asql, "row");
            }
            //获取楼盘详细信息
            $xinList = self::getUnitInfo($info);
            return $xinList;
        }else{
            return null;
        }
    }

    /**
     * 获取楼盘详细信息
     */
    public static function getUnitInfo($info)
    {
        if(!empty($info)){
            $yuList = Model_CricUnitZT0114Detail::getAll(
                [
                    'where' => [
                        'ListID' => $info['ItemID']
                    ],
                    'order' => 'rank asc',
                    'limit' => '0,3'
                ]
            );

            foreach($yuList as $uKey => $unit) {
                $unitInfo = Model_Loupan::getRow(
                    [
                        'where' => [
                            'sHomeID' => $unit['HomeID']
                        ]
                    ]
                );
                if(!empty($unitInfo)) {
                    $yuList[$uKey] = array_merge($unit, $unitInfo);
                }
            }
            return $yuList;
        }else{
            return null;
        }

    }
}