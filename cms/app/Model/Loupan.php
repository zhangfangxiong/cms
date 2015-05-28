<?php

class Model_Loupan extends Model_Base
{
    const DB_NAME = 'xinfang';

    const TABLE_NAME = 't_loupan';

    /*
     * 获取城市当月新开楼盘列表
     */
    public static function getLoupanByCID($iCityID, $iRows = 0, $iPage = 0)
    {
        $where = array(
            'iCityID' => $iCityID,
            'sMapUnitID <>' => '',
            'iStatus' => 1
        );

        $currentTime = time();
        $currentTime = date('Y-m', $currentTime);
        $currentTime = explode('-', $currentTime);
        $year = $currentTime[0];
        $month = $currentTime[1];
        $startTime = $year. '-'. $month. '-1 00:00:00';
        $lastDay = self::getMonthLastDay($month, $year);
        $endTime = $year. '-'. $month. '-'. $lastDay. ' 23:59:59';

        $where['sOpenTime >='] = $startTime;
        $where['sOpenTime <='] = $endTime;

        $order = "`sOpenTime` ASC";

        if(!empty($iRows) && !empty($iPage)) {
            return self::getList($where, $iPage, $order, $iRows, '', '', false);
        }else {
            return self::getAll(['where'=> $where]);
        }
    }

    /*
     * 获取区域当月新开楼盘列表
     */
    public static function getLoupanByRID($iRegionID, $iRows = 0, $iPage = 0)
    {
        $where = array(
//            'iRegionID' => $iRegionID,
            'sMapUnitID <>' => '',
            'iStatus' => 1
        );

        $region = Model_CricRegion::getDetail($iRegionID);
        if(!empty($region)){
            $where['sRegionName'] = $region['RegionName'];
        }

        $currentTime = time();
        $currentTime = date('Y-m', $currentTime);
        $currentTime = explode('-', $currentTime);
        $year = $currentTime[0];
        $month = $currentTime[1];
        $startTime = $year. '-'. $month. '-1 00:00:00';
        $lastDay = self::getMonthLastDay($month, $year);
        $endTime = $year. '-'. $month. '-'. $lastDay. ' 23:59:59';

        $where['sOpenTime >='] = $startTime;
        $where['sOpenTime <='] = $endTime;

        $order = "`sOpenTime` ASC";

        if(!empty($iRows) && !empty($iPage)) {
            return self::getList($where, $iPage, $order, $iRows, '', '', false);
        }else {
            return self::getAll(['where'=> $where]);
        }
    }

    public static function getMonthLastDay($month, $year) {
        switch ($month) {
            case 4 :
            case 6 :
            case 9 :
            case 11 :
                $days = 30;
                break;
            case 2 :
                if ($year % 4 == 0) {
                    if ($year % 100 == 0) {
                        $days = $year % 400 == 0 ? 29 : 28;
                    } else {
                        $days = 29;
                    }
                } else {
                    $days = 28;
                }
                break;

            default :
                $days = 31;
                break;
        }
        return $days;
    }

    /*
    * 获取城市中某个区域的楼盘
    *
    * */
    public static function  getLouPanByZone($iCityID, $minLon, $minLat, $maxLon, $maxLat)
    {
        $where = array(
            'where' => array(
                'iCityID' => $iCityID,
                'sMapUnitID <>' => '',
                'iStatus' => 1,
                'iLng <=' => $maxLon,
                'iLng >=' => $minLon,
                'iLat <=' => $maxLat,
                'iLat >=' => $minLat
            )
        );

        return self::getAll($where);
    }

    /**
 * 把xinfang数据库t_loupan表的id转换成cric_xf数据库Loupan表中的id
 */
    public static function BuildingIDConvert($buildingID)
    {
        $detail = self::getDetail($buildingID);
        return $detail['sMapUnitID'];
    }

    /**
     * 获取楼盘信息
     */
    public static function getListByIds($buildingIds)
    {
        $buildingIds = implode(',', $buildingIds);
        $where = array(
            'where' => array(
                'iAutoID IN' => $buildingIds,
                'iStatus' => 1,
            )
        );

        return self::getAll($where);
    }


    /**
     * @param $sUnitID
     * 根据UnitID获取cric_xf 表unit ID
     */
    public static function getUnitCricID($sUnitID)
    {
        $aUnit = Model_CricUnit::getByUnitID($sUnitID);
        return $aUnit['ID'];
    }

    public static function getZongPinPic($sPic, $iHeight, $iWidth)
    {
        if (!empty($sPic)) {
            return Util_Uri::getCricViewURL($sPic, $iHeight, $iWidth);
        } else {
            return Util_Uri::getDefaultImg();
        }
    }

    public static function getHomeID($buildingID)
    {
        $detail = self::getDetail($buildingID);

        $homeID = 0;
        if(!empty($detail)) {
            $homeID = $detail['sHomeID'];
        }

        return $homeID;
    }

    /**
     * 点评网首页最新开盘
     */
    public static function getNewOpen($cityCode)
    {
        $date = date('Y-m', time()) . '-01';
        $sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE iStatus = 1 AND sCityCode ='$cityCode'
                AND  sOpenTime REGEXP '[0-9]{4}-[0-9]{2}-[0-9]{2}'  AND sOpenTime >= '$date'
                ORDER BY sOpenTime LIMIT 50";
        return self::query($sql, "all");
    }
    public static function getRecommend($score)
    {
        $recommend = Yaf_G::getConf('recommend',null,'mapi');

        $result = '';
        if(!empty($score) && isset($recommend[$score])){
            $result = $recommend[$score];
        }

        return $result;
    }

    public static function getRecommendText($score)
    {
        $recommend = Yaf_G::getConf('recommend_text',null,'mapi');

        $result = '';
        if(!empty($score) && isset($recommend[$score])){
            $result = $recommend[$score];
        }

        return $result;
    }

    public static function getRecommendColor($score)
    {
        $recommend = Yaf_G::getConf('recommend_color',null,'mapi');

        $result = '';
        if(!empty($score) && isset($recommend[$score])){
            $result = $recommend[$score];
        }

        return $result;
    }

    //根据旧表中unitID获取新表中楼盘ID
    public static function getLouByUnitID($sMapUnitID)
    {
        return self::getRow(array(
            'where' => array(
                'sMapUnitID' => $sMapUnitID,
                'State' => 1
            )
        ));
    }
}