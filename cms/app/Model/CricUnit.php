<?php

class Model_CricUnit extends Model_Base
{

    const DB_NAME = 'cric_xf';

    const TABLE_NAME = 'Unit';

    public static function searchAutoComplete($sCityCode, $sKey, $iCityID = 0)
    {
        $aCurrCity = null;
        if ($iCityID > 0) {
            $aCurrCity = Model_City::getDetail($iCityID);
        }
        //$dbh = Util_Common::getDb(self::DB_NAME, 'master');
        if (empty($sCityCode)) {
            $sSQL = "SELECT
                ID,
                UnitName,RegionName,
                DistrictName,
                Address
                FROM Unit
                WHERE (UnitName LIKE '" . addslashes($sKey) . "%' OR Address LIKE '" . addslashes($sKey) . "%' ) AND State = 1
                ORDER BY " .
                (empty($aCurrCity) ? '' : 'IF(CityCode="' . $aCurrCity['sFullPinyin'] . '",1,0) DESC,')
                . " ID LIMIT 10
            ";
        } else {
            $sSQL = "SELECT
                ID,
                UnitName,RegionName,
                DistrictName,
                Address
                FROM Unit
                WHERE CityCode='$sCityCode' AND (UnitName LIKE '" . addslashes($sKey) . "%' OR Address LIKE '" . addslashes($sKey) . "%' ) AND State = 1
                ORDER BY ID LIMIT 10
            ";
        }

        $aData = self::query($sSQL);
//        $sSQL = mb_convert_encoding($sSQL, 'gb2312', 'utf-8');
//        $oStmt = $dbh->query($sSQL);
//        $oStmt->setFetchMode(PDO::FETCH_ASSOC);
//        $aData = $oStmt->fetchAll();
        $aList = array();
        foreach ($aData as &$aRow) {
//            foreach ($aRow as $k => $v) {
//                if (! is_numeric($v)) {
//                    $aRow[$k] = mb_convert_encoding($v, 'utf-8', 'gb2312');
//                }
//            }
            $aList[] = join('|', $aRow);
        }

        return $aList;
    }

    public static function getLoupanNames($sLoupanID)
    {
        if (empty($sLoupanID)) {
            return array();
        }

        //$dbh = Util_Common::getDb(self::DB_NAME, 'master');
        $sSQL = "SELECT ID,UnitName
            FROM Unit
            WHERE ID IN($sLoupanID)
            ORDER BY ID
        ";
        $aData = self::query($sSQL);
        //var_dump($aData);exit;
//        $oStmt = $dbh->query($sSQL);
//        $oStmt->setFetchMode(PDO::FETCH_ASSOC);
//        $aData = $oStmt->fetchAll();
        $aList = array();
        foreach ($aData as $aRow) {
            $aList[$aRow['ID']] = $aRow['UnitName'];
        }

        return $aList;
    }

    public static function getLoupanByID($sLoupanID)
    {
        return self::getRow(array(
            'where' => array(
                'ID' => $sLoupanID,
                'State' => 1
            )
        ));
    }

    public static function getUnitUrl($sCityCode, $sRegion, $sDistrict, $iUnitID)
    {
        $aRegionPinyin = Util_Pinyin::get($sRegion);
        $aDistrictPinyin = Util_Pinyin::get($sDistrict);
        return 'http://' . Util_Common::getConf('fangjiadp',
            'domain') . '/' . $sCityCode . '/' . $aRegionPinyin[0] . '/' . $aDistrictPinyin[0]
        . '/' . $iUnitID;
    }


    public static function getUnitEvaluationUrl($sCityCode, $sRegion, $sDistrict, $iUnitID)
    {
        $aRegionPinyin = Util_Pinyin::get($sRegion);
        $aDistrictPinyin = Util_Pinyin::get($sDistrict);
        return 'http://'.Util_Common::getConf('fangjiadp', 'domain').'/'.$sCityCode.'/'.$aRegionPinyin[0].'/'.$aDistrictPinyin[0]
        .'/'.$iUnitID.'_evaluation';
    }


    public static function getUnitNewestBatchType($sUnitID)
    {
        $sSQL = "SELECT * FROM BatchTypeFull WHERE UnitId = '" . $sUnitID .
            "' AND CricPrice > 0 AND SalePrice > 0 order by BatchRank asc,pushdate desc, rowdate desc, PriceRank asc, marketdate desc limit 5";
        $aBatchType = self::query($sSQL);
        if ($aBatchType) {
            return $aBatchType[0];
        }

        $sSQL = "SELECT * FROM BatchTypeFull WHERE UnitId = '" . $sUnitID .
            "' AND SalePrice > 0 order by BatchRank asc,pushdate desc, rowdate desc, PriceRank asc, marketdate desc limit 5";
        $aBatchType = self::query($sSQL);
        if ($aBatchType) {
            return $aBatchType[0];
        }

        $sSQL = "SELECT * FROM BatchTypeFull WHERE UnitId = '" . $sUnitID .
            "' order by BatchRank asc,pushdate desc, rowdate desc, PriceRank asc, marketdate desc limit 5";
        $aBatchType = self::query($sSQL);
        if ($aBatchType) {
            return $aBatchType[0];
        }

        return [];
    }

    public static function getRecommendedLevelList()
    {
        return [
            0 => '尽快入手',
            1 => '推荐购买',
            2 => '谨慎购买',
            3 => '持币观望',
            'AAA' => '尽快入手',
            'A+' => '推荐购买',
            'BBB+' => '谨慎购买',
            'C' => '持币观望'
        ];
    }

    /*
     * 通过楼盘名 和 城市code 获取楼盘ID add by cjj
     *
     * */
    public static function  getLouPanIDByName($sCityCode, $name)
    {
        $sSQL = "SELECT
                ID,
               UnitName,RegionName,
                DistrictName,
                Address
                FROM Unit
                WHERE CityCode='$sCityCode' AND UnitName = '" . $name . "' AND State = 1
                LIMIT 0,1
            ";
        $aData = self::query($sSQL, 'row');
        return $aData;
    }


    public static function getByUnitID($sUnitID)
    {
        $sSQL = "SELECT * FROM Unit WHERE UnitID='" . $sUnitID . "' and State = 1";
        return self::query($sSQL, 'row');
    }

    /**
     *  新房搜索
     * @param $searchParam 查询条件
     * @param $cityCode  当前城市code
     * @param $start 开始行
     * @param $pageSize
     */
    public static function search($searchParam, $cityCode,$start,$pageSize)
    {
        $filter = '';
        $sortField = '';
        $sql = '';
        /* 暂无此功能
        if (isDiscount > 0)
        {
            filter += string.Format(" AND EXISTS ( SELECT TOP 1 1 FROM dbo.Unit_Sale WHERE UnitID = t.UnitID )");
        }
        */
        //有评测
        if ($searchParam['ev'] > 0) {
            $filter .= " AND EvaluationFlag = 1";
        }
        //有价目表
        if ($searchParam['pr'] > 0) {
            $filter .= " AND RoomTotal > 0";
        }
        switch ($searchParam['st']) {
            //默认智能排序（根据建议，开盘时间）
            case 0:
                $sortField = " ORDER BY levelcode ASC , OpenTime DESC";
                break;
            //价格排序
            case 1:
                $sortField = sprintf(" ORDER BY BidingPrice %s", $searchParam['ord'] > 0 ? "DESC" : "ASC");
                break;
            //综合评分
            case 2:
                $sortField = " ORDER BY TotalScore DESC";
                break;
            //最优户型
            case 3:
                $sortField = " ORDER BY RoomTypeScore DESC";
                break;
            //装修适宜
            case 4:
                $sortField = " ORDER BY DesignScore DESC";
                break;
            //物业无忧
            case 5:
                $sortField = " ORDER BY HouseManageScore DESC";
                break;
            //车位充足
            case 6:
                $sortField = " ORDER BY ParkScore DESC";
                break;
            //人气区位
            case 7:
                $sortField = " ORDER BY LocationScore DESC";
                break;
            //默认排序
            default:
                $sortField = " ORDER BY levelcode ASC , OpenTime DESC";
                break;
        }
        $limit = " limit {$start},{$pageSize}";
        $filed = "a.*,case a.EJScore when 'AAA' THEN 0 WHEN 'A+' THEN 1 WHEN 'BBB+' THEN 2 WHEN 'C' THEN 3 else 4 END  as levelcode";
        $countFiled = 'count(a.ID) as num ';
        if ($searchParam['f'] == 1) {
            /*$sql = @"select * from(SELECT *,case tt.EJScore when 'AAA' THEN 0
WHEN 'A+' THEN 1 WHEN 'BBB+' THEN 2 WHEN 'C' THEN 3 else 4 END  as levelcode FROM (SELECT * FROM Unit WITH(NOLOCK) WHERE State = 1 AND dbo.IS_REGULARMATCH(OpenTime,'\d{4}-\d{2}-\d{2}')=1) as tt WHERE tt.CityCode = '" .$cityCode . "' AND opentime <= convert(varchar(10),getdate(),120)) as tt where 1 = 1 ";*/
            $formatsql = "SELECT {field}
	              FROM Unit  as a
	              WHERE State = 1  and a.CityCode = '" . $cityCode . "' and OpenTime REGEXP '[0-9]{4}-[0-9]{2}-[0-9]{2}' and OpenTime<=CURDATE()";
            //拼接sql
            $sql = $formatsql . $filter . $sortField . $limit;
            $units = self::query(preg_replace("/\{field\}/",$filed,$sql));
            $sql = $formatsql . $filter;
            $unitsCount =  self::query(preg_replace("/\{field\}/",$countFiled,$sql),'one');
        } elseif ($searchParam['f'] >= 2 && $searchParam['f'] <= 11) {
            $type = "热点板块";
            switch ($searchParam['f']) {
                case 3:
                    $type = "轨交房";
                    break;
                case 4:
                    $type = "婚房";
                    break;
                case 5:
                    $type = "李想买房";
                    break;
                case 6:
                    $type = "特价房";
                    break;
                case 7:
                    $type = "优惠折扣";
                    break;
                case 8:
                    $type = "理想价";
                    break;
                case 9:
                    $type = "刚需房";
                    break;
                case 10:
                    $type = "学位房";
                    break;
                case 11:
                    $type = "热销楼盘";
                    break;
            }
            /*
           sql = string.Format("SELECT  t.* FROM ( SELECT CASE EJScore WHEN 'AAA' THEN 0 WHEN 'A+' THEN 1 WHEN 'BBB+' THEN 2 WHEN 'C' THEN 3 ELSE 4 END AS levelcode ,a.* FROM  dbo.Unit a WITH ( NOLOCK )"
           + "JOIN ( SELECT DISTINCT UnitID,Type  FROM dbo.SuperSearch_UnitList WITH ( NOLOCK ) WHERE CityCode= '{0}' AND Type = '{1}') b ON a.UnitID = b.UnitID"
           + " WHERE a.State = 1 AND a.CityCode = '{0}') t WHERE 1=1", city.CityCode, type);*/
            $formatsql = "
                        SELECT {field}
                        FROM  Unit a
                        INNER JOIN ( SELECT DISTINCT UnitID,Type  FROM SuperSearch_UnitList  WHERE CityCode= '" . $cityCode . "' AND Type = '" . $type . "') b
                        ON a.UnitID = b.UnitID
                        WHERE a.State = 1 AND a.CityCode = '" . $cityCode . "'";
            //拼接sql
            $sql = $formatsql . $filter . $sortField . $limit;
            $units = self::query(preg_replace("/\{field\}/",$filed,$sql));
            $sql = $formatsql . $filter;
            $unitsCount =  self::query(preg_replace("/\{field\}/",$countFiled,$sql),'one');
        } else {

            /*
              sql = string.Format("SELECT  t.* FROM ( SELECT CASE EJScore WHEN 'AAA' THEN 0 WHEN 'A+' THEN 1 WHEN 'BBB+' THEN 2 WHEN 'C' THEN 3 ELSE 4 END AS levelcode ,a.* FROM dbo.Unit a WITH ( NOLOCK )"
            + " WHERE a.State = 1 AND a.CityCode = '{0}') t WHERE 1=1", city.CityCode);*/

            $formatsql = " SELECT {field}
                             FROM Unit a
                             WHERE a.State = 1 AND a.CityCode = '" . $cityCode . "'";
            //关键字
            if ($searchParam['k']) {
                $filter .= " AND ( UnitName LIKE '%" . addslashes($searchParam['k']) . "%')";
            }
            //区域
            if ($searchParam['r']) {
                $filter .= " AND ( RegionName = '" . addslashes($searchParam['r']) . "')";
            }
            //板块
            if ($searchParam['d']) {
                $filter .= " AND ( DistrictName = '" . addslashes($searchParam['d']) . "')";
            }
            //售价
            if (!empty($searchParam['p'])) {
                $price = $searchParam['p'];
                if ($price == "-") {
                    $filter .= " AND BidingPrice = 0 ";
                } else {
                    $priceSql = " AND BidingPrice > 0 AND BidingPrice BETWEEN %u AND %u";
                    $newPrice = self::formatPriceStr($price, "万以下");
                    $priceFlag = 0;
                    if (!empty($newPrice)) {
                        $priceFlag = 1;
                        $filter .= sprintf($priceSql, 0, $newPrice * 10000);
                    }
                    $newPrice = self::formatPriceStr($price, "千以下");
                    if (!empty($newPrice) && $priceFlag == 0) {
                        $priceFlag = 1;
                        $filter .= sprintf($priceSql, 0, $newPrice * 1000);
                    }
                    $newPrice = self::formatPriceStr($price, "万以上");
                    if (!empty($newPrice) && $priceFlag == 0) {
                        $priceFlag = 1;
                        $filter .= sprintf($priceSql, $newPrice * 10000, 9999999);
                    }
                    $newPrice = self::formatPriceStr($price, "千以上");
                    if (!empty($newPrice) && $priceFlag == 0) {
                        $priceFlag = 1;
                        $filter .= sprintf($priceSql, $newPrice * 1000, 9999999);
                    }
                    $newPrice = self::formatPriceStrToArry($price, "千");
                    if (!empty($newPrice) && $priceFlag == 0) {
                        $filter .= sprintf($priceSql, $newPrice[0] * 1000, $newPrice[1] * 1000);
                    }
                    $newPrice = self::formatPriceStrToArry($price, "万");
                    if (!empty($newPrice) && $priceFlag == 0) {
                        $filter .= sprintf($priceSql, $newPrice[0] * 1000, $newPrice[1] * 1000);
                    }

                }
            }
            //预算
            if (!empty($searchParam['prev'])) {
                $minTotalPrice = 0;
                $maxTotalPrice = 100000000;
                self::GetMinAndMaxTotalPrice($searchParam['prev'], $minTotalPrice, $maxTotalPrice);
                $filter .= sprintf(" AND (MinTotalPrice BETWEEN %u AND %u  OR MaxTotalPrice BETWEEN %u AND %u)",
                    $minTotalPrice * 10000, $maxTotalPrice * 10000,$minTotalPrice * 10000, $maxTotalPrice * 10000);

            }
            //户型
            if ($searchParam['rt']) {
                $filter .= " AND ( RoomType LIKE '%" . addslashes($searchParam['rt']) . "%')";
            }
            //建议
            if (self::getRecommendedLevel($searchParam['s'])) {
                $filter .= " AND ( EJScore = '" . self::getRecommendedLevel($searchParam['s']) . "')";
            }
            // 拼接sql
            $sql = $formatsql . $filter . $sortField . $limit;
            $units = self::query(preg_replace("/\{field\}/",$filed,$sql));
            $sql = $formatsql . $filter;
            $unitsCount =  self::query(preg_replace("/\{field\}/",$countFiled,$sql),'one');
        }
        $resultCode = 0;
        if ($unitsCount == 0) {
            $formatsql =  "SELECT %s  FROM Unit as a
                    WHERE a.State = 1 AND a.CityCode = '" . $cityCode . "'  AND EJScore= 'AAA' limit 0,5";
            $units = self::query(sprintf($formatsql,$filed));
            $unitsCount =  count($units);
            $resultCode = 1;
        }
        if (!empty($units)) {
            $roomTypeRemaining = self::getRoomRemaining();
            foreach ($units as &$item) {
                $item['SalePrice'] = number_format($item['BidingPrice']);
                $item['CricPrice'] = number_format($item['Price']);
                $item['MainPic'] = Util_Uri::getCricViewURL($item['ZongPingXQ'],130,90,0,0);
                $item['CommentUrl'] = '';
                $item['Url'] = '';
                if (!empty($roomTypeRemaining) && isset($roomTypeRemaining[$item['HomeID']])) {
                    $item['RoomRemaining'] = $roomTypeRemaining[$item['HomeID']];
                } else {
                    $item['RoomRemaining'] = 0 ;
                }

            }
        }
        return array('units'=>$units,'count' => $unitsCount,'resultCode' => $resultCode);
    }

    public static function getRecommendedLevel($key)
    {
        $level =  array(
            '尽快入手' => 'AAA',
            '推荐购买' => 'A+',
            '谨慎购买' => 'BBB+',
            '持币观望' => 'C'
        );
        return isset($level[$key]) ? $level[$key]:'';
    }

    protected static function getRoomRemaining()
    {
        $roomTypeRemaining = self::query("SELECT HomeID,SUM(HomeRemainingNum) AS RoomRemaining FROM RoomTypeRemaining WHERE STATE = 1 GROUP BY HomeID");
        $newRoomTypeRemaining = array();
        if (!empty($roomTypeRemaining)) {
            foreach($roomTypeRemaining as $item) {
                $newRoomTypeRemaining[$item['HomeID']] = $item['RoomRemaining'];
            }
        }
        return $newRoomTypeRemaining;
    }

    protected static function formatPriceStr($str, $findStr)
    {
        if (!(strpos($str, $findStr) === false)) {
            $price = preg_replace('/' . $findStr . '/', "", $str);
            return $price;
        }
        return '';
    }

    protected static function formatPriceStrToArry($str, $findStr)
    {
        if (!(strpos($str, '-') === false)) {
            if (!(strpos($str, $findStr) === false)) {
                $price = preg_replace('/' . $findStr . '/', "", $str);
                return explode( '-',$price);
            }
        }
        return null;
    }

    private static function GetMinAndMaxTotalPrice($totalPrice, &$minTotalPrice, &$maxTotalPrice)
    {

        $reg = '/[^\\d-]*/';
        $s = preg_replace($reg, '', $totalPrice);
        if (!(strpos($totalPrice, "以下") === false)) {
            $minTotalPrice = 0;
            if (!empty($s)) {
                $maxTotalPrice = $s;
            } else {
                $maxTotalPrice = 10000;
            }
        } elseif (!(strpos($totalPrice, "以上") === false)) {
            $maxTotalPrice = 1000000;
            if (!empty($s)) {
                $minTotalPrice = $s;
            } else {
                $minTotalPrice = 10000;
            }
        } else {
            $arr = explode('-', $s);
            if (!empty($arr)) {
                $minTotalPrice = $arr[0];
                $maxTotalPrice = $arr[1];
            } else {
                $minTotalPrice = 0;
                $maxTotalPrice = 100000000;
            }
        }
    }

    /**
     * 获取预开盘推荐
     */
    public static function getYuKaiPan($cityName)
    {
        $mSQL = "SELECT * FROM (SELECT * FROM UnitZhuanti0114 a
                                  WHERE   State = 1
                                  AND CityCaption = '$cityName'
                ) aa LEFT JOIN
                (SELECT ListID,COUNT(1) AS ListCnt FROM UnitZhuanti0114_Detail WHERE state =1
                GROUP BY ListID) bb
                ON aa.ItemID=bb.ListID ORDER BY ListCnt desc";
            $mData = self::query($mSQL, 'all');

            $sSQL = "SELECT  b.ItemID ,
                        b.ListID ,
                        a.CityCaption ,
                        a.ListType ,
                        a.CustomType ,
                        a.Title ,
                        b.Rank ,
                        b.RType ,
                        b.RTypeArea_Min ,
                        b.RTypeArea_Max ,
                        b.RTypeCode ,
                        b.developer ,
                        b.Defect ,
                        IsRecommendSelf ,
                        CostPerformance ,
                        CompositeScore ,
                        SaleScore ,
                        Score,
                        c.UnitName ,
                        c.ID ,
                        c.RegionName ,
                        c.DistrictName ,
                        c.ZongPingXQ ,
                        c.State ,
                        b.NetRank,
                        c.Price ,
                        c.BidingPrice
                FROM    ( SELECT   *
                           FROM     UnitZhuanti0114
                           WHERE    State = 1 AND CityCaption = '$cityName'
                         ) a
                INNER JOIN UnitZhuanti0114_Detail b ON a.ItemID = b.ListID AND b.State = 1
                LEFT JOIN (SELECT UnitName,ID,regionName,DistrictName,ZongPingXQ,State,Price,BidingPrice,HomeID FROM Unit) c ON c.HomeID = b.HomeID
                ORDER BY b.Rank ASC";
            $aData = self::query($sSQL, 'all');
        return array('mData'=>$mData,'aData'=>$aData);
    }

    /**
     *获取上调楼盘信息,获取当前月的数据，当前月的数据为空取上月的
     * $cityCode 城市code
     * $ListType 1代表评级上调 2代表评级下调
     */
    public static function getPingJiList($cityCode,$ListType)
    {
        $sql = "SELECT TIMESTAMPDIFF(month,'2013-1-1', L.CreateDate) as DiffDate, U.*,L.CreateDate
                FROM Unit U , UnitTopList L
                WHERE U.ID = L.UnitID
                AND L.State = 1
                AND L.ListType = $ListType
                AND L.CityCode = '$cityCode'
                ORDER BY DiffDate  DESC, UnitRank
                limit 5";
                $aData = self::query($sql, 'all');
        $arr = array();
        if(!empty($aData)){
            $newDate = date('Y-m',strtotime($aData[0]['CreateDate']));  //格式化日期
            foreach($aData as $key=>$value) {
                $format = date('Y-m',strtotime($value['CreateDate']));
                if($format == $newDate){
                    array_push($arr,$value);
                }
            }
        }
        return $arr;
    }

    /**
     * 获取滞销或者热销榜
     */
    public static function getZhiXiaoOrReXiao($cityName)
    {
        $sql = "SELECT  c.ID ,c.UnitID ,c.UnitName ,c.City ,c.ListType ,c.CityCode,c.Region,c.District ,c.IndexID ,
                        c.CricPrice ,c.ListDate,c.Rank ,c.CricUnitID,c.RoomCount,
                        u.ZongPingXQ,u.EJScore,u.BidingPrice,u.Price as CircPrice,
                        u.Address,c.TradingCount,c.PushCount
                FROM CityIndex_List c, Unit u
                WHERE c.IndexID = u.ID
                AND c.City = '$cityName'
                AND (c.ListType = 2 OR c.ListType = 5 OR c.ListType = 6 OR c.ListType = 7)
                order by c.ListDate desc,c.Rank asc";
        return  self::query($sql, 'all');
    }

    /**
     * 房价点评网首页获取评测报告
     */
    public static function getEvaluation($CityCode)
    {
        $sql = "SELECT a.ID,a.UnitID,b.UnitName, ZongPingXQ, b.CityCode,RegionName,DistrictName,BidingPrice,TotalScore,AdvantageLabel,RiskLabel,b.Rank
                FROM Unit a
                JOIN CityIndex_List b
                ON a.HomeID = b.UnitID
                AND b.ListType = 4
                AND a.State = 1
                AND b.CityCode = '$CityCode'
                ORDER BY b.Rank ASC LIMIT 0,2";
        return  self::query($sql, 'all');
    }

    /**
     * 房价点评网首页评级推荐
     * $CityCode
     * $type 1代表最佳户型 2代表车位充足 3代表社区无忧
     */
    public static function getPingJi($CityCode,$type)
    {
        if ($type == 1) {
            $order = 'RoomTypeScore DESC';
        } else if ($type == 2) {
            $order = 'ParkScore DESC';
        } else if($type == 3) {
            $order = 'LocationScore DESC';
        }
        $where = array('where'=>array('CityCode'=>$CityCode,'State'=>1),'order'=>$order,'limit'=>'0,20');
//        $sql = "SELECT  ID,UnitID,UnitName,ZongPingXQ, CityCode,RegionName,DistrictName,BidingPrice,TotalScore
//                                  FROM Unit WHERE State = 1 AND CityCode = '$CityCode'  ORDER BY $order LIMIT 0, 20";
//        return  self::query($sql, 'all');
        return Model_CricUnit::getAll($where);
    }


    /**
     * 获取所有小区的首字母
     */
    public static function getFirstLetters($CityCode)
    {
        $sql = "SELECT DISTINCT FirstLetter FROM UnitSearch WHERE FirstLetter <> '' AND CityCode = '$CityCode' ORDER BY FirstLetter";
        return  self::query($sql, 'all');
    }

    /**
     * 根据loupan表中的ID获取unit
     */
    public static function getIDByUnitId($sMapUnitID)
    {
        //$loupan = Model_Loupan::getDetail($id);
        if(isset($sMapUnitID) && !empty($sMapUnitID)){
            $unitInfo = self::getDetail($sMapUnitID);
            if(!empty($unitInfo)){
                return $unitInfo;
            }else{
                return array();
            }
        }else {
            return array();
        }
    }

    /**
     *获取相关ID
     * （type1代表根据unit ID获取字段UnitID，type2代表根据unit ID获取loupan表中的自增ID）
     */
    public static function getUnitIdById($UnitID,$type)
    {
        $unitInfo = self::getRow(['where'=>['ID'=>$UnitID , 'State'=>1]]);
        if(!empty($unitInfo) && $type==1){
            return $unitInfo;
        }else if(isset($unitInfo['UnitID']) && !empty($unitInfo['UnitID']) && $type==2){
            $loupan = Model_Loupan::getRow(['where'=>['sMapUnitID'=>$unitInfo['UnitID'] , 'iStatus'=>1]]);
            return $loupan;
        }else {
            return array();
        }
    }

     /* 当前城市楼盘
     * @param  [type] $sCityCode [description]
     */
    public static function getCurrentCityLoupanList($sCityCode)
    {
        $where = array('where'=>array('CityCode'=>$sCityCode,'State'=>1));
        return Model_CricUnit::getAll($where);
    }

    /**
     * 楼盘Ids
     * @param  [type] $aUnitIds [description]
     */
    public static function getUnitByIDs($aUnitIds)
    {
        $where = array('where'=>array('UnitID in'=>$aUnitIds,'State'=>1));
        return Model_CricUnit::getAll($where);
    }

    /**
     * 首字母取楼盘
     * @param  [type] $iLetter [description]
     * @return [type]          [description]
     */
    public static function getListByLetter($iLetter) {
        $where = array('where'=>array('FirstLetter' => $iLetter, 'State' => 1));
        return Model_CricUnit::getAll($where);
    }

    // 获取周边楼盘
    public static function getSurroundingUnit($sUnitId,$cityCode,$regionName)
    {
        $where = array(
            'where' => array(
                'CityCode' => $cityCode,
                'RegionName'=> $regionName,
                'UnitID <>' => $sUnitId,
            )
        );
        $result =  self::getAll($where);
        $data = array();
        if (!empty($result)) {
            foreach ($result as $key => $item) {
                $data[$key]['RegionName'] = $item['RegionName'];
                $data[$key]['UnitName'] = $item['UnitName'];
                $data[$key]['SalePrice'] = empty($item['BidingPrice']) ? '-' : number_format($item['BidingPrice']);
                $data[$key]['District'] = $item['District'];
                $data[$key]['ID'] = $item['ID'];
                $data[$key]['Url'] = $item['Url'];
                $data[$key]['PicUrl'] = Model_EvaluationImage::getImageUrl(1,$item['ZongPingXQ'],80,60);
            }
        }
        return $data;
    }
    // 推荐楼盘
    public static  function getRecommendedUnit($cityCode,$homeId)
    {
        $num = 5;
        $sql = "SELECT tt.ListUnitID,
                        tt.remark,
                        ww.CityCode,
                        ww.DistrictName,
                        ww.RegionName,
                        ww.ID,ww.UnitName,
                        ww.EvaluationFlag,
                        ww.ZongPinAnchor,
                        ww.UnitId,
                        ww.ZongPingXQ,
                        ww.Price,
                        ww.BidingPrice,
                        ww.OpinionSummary
											FROM (SELECT UnitID,Remark,ListUnitID FROM Unit_Interest_List WHERE UnitID = '%s' and State=1) tt
											LEFT JOIN (SELECT * FROM Unit WHERE EvaluationFlag = 1 AND State=1) ww ON tt.listunitid=ww.homeid
											WHERE ww.ID <> ''";
        $sql = sprintf($sql,$homeId);
        $result = self::query($sql);
        if (sizeof($result)<$num) {
            $top = $num - sizeof($result);
            $sql = "SELECT *  FROM Unit WHERE CityCode = '".$cityCode."' AND State = 1 limit 0, 50";
            $us = self::query($sql);
            if (!empty($us)) {
                $rand = rand(0,sizeof($us)-5);
                $tempArr = array_slice($us,$rand,$top);
                $result = array_merge($result,$tempArr);
            }
        }

        $data = array();
        if (!empty($result)) {
            foreach ($result as $key => $item) {
                $data[$key]['RegionName'] = $item['RegionName'];
                $data[$key]['UnitName'] = $item['UnitName'];
                $data[$key]['SalePrice'] = empty($item['BidingPrice']) ? '-' : number_format($item['BidingPrice']);
                $data[$key]['District'] = $item['DistrictName'];
                $data[$key]['ID'] = $item['ID'];
                $data[$key]['Url'] = '/'.$cityCode.'/Newhouse/detail?unitid='.$item['ID'];
                $data[$key]['PicUrl'] = Model_EvaluationImage::getImageUrl(1,$item['ZongPingXQ'],195,145);
            }
        }
        return $data;
    }
    /**
     * 根据老表中楼盘ID获取新房表中的楼盘ID
     * @param $sID
     * @return int
     */
    public static function getNewIDByUnitID($sID)
    {
        $aLouInfo = self::getLoupanByID($sID);
        if (empty($aLouInfo)) {
            return 0;//没有对应UnitID的楼盘
        }
        $aNewLouInfo = Model_Loupan::getLouByUnitID($aLouInfo['UnitID']);//新表中楼盘数据
        if (empty($aNewLouInfo)) {
            return -1;//新表中没有同步楼盘数据
        }
        return $aNewLouInfo['iAutoID'];
    }

}