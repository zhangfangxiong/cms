<?php
/**
 * Created by PhpStorm.
 * User: yaobiqing
 * Date: 15/4/8
 * Time: 下午3:10
 */


class Model_CricBatchTypeFull extends Model_Base
{

    const TABLE_NAME = 'BatchTypeFull';
    const DB_NAME = 'cric_xf';


    private static function _getMonthPrice($unitID, $start, $next, $homeType) {
        $sSql = "SELECT count(*) as count, sum(SalePrice) as salePrice, sum(CricPrice) as cricPrice  FROM ".self::TABLE_NAME." WHERE UnitId = '".$unitID."' AND MarketDate >= '".$start."' AND MarketDate < '".$next."' AND INSTR(HomeType, '".$homeType."') AND salePrice > 0 and cricPrice > 0";// ORDER BY PushDate DESC,rowDate DESC";
        //echo $sSql, PHP_EOL;exit;
        $aList = self::query($sSql, 'row');
        //var_dump($aList);
        $iCount = intval($aList['count']);
        if ($iCount > 0) {
            $avgSalePrice = round(intval($aList['salePrice']) / $iCount, 0);
            $avgGuidePirce = round(intval($aList['cricPrice']) / $iCount, 0);
            return ['avgSalePrice' => $avgSalePrice, 'avgGuidePirce' => $avgGuidePirce];
        } else {
            return false;
        }
    }
    /**
     * @param $p_sUnitID
     * 获取楼盘的价格趋势
     */
    public static function getPriceTrendData($p_sUnitID, $p_sHomeType)
    {
        $aDate = getdate();
        $year = $aDate['year'];
        $mon = $aDate['mon'];

        $tmpGuidePrice = 0;
        $tmpPrice = 0;
        $priceList = [];
        for ($i = 11; $i >= 0; $i--) {
            $tmpYear = $year;
            $startMonth = $mon - $i;
            if ($startMonth <= 0) {
                $startMonth = $startMonth + 12;
                $tmpYear = $year - 1;
            }
            $nextMonth = $startMonth + 1;

            $start = date('Y-m-d', strtotime($tmpYear.'-'.$startMonth.'-01'));
            $next = date('Y-m-d', strtotime($tmpYear.'-'.$nextMonth.'-01'));;
            $aInfo = self::_getMonthPrice($p_sUnitID, $start, $next, $p_sHomeType);


            if ($aInfo !== false) {
                $tmpPrice = $aInfo['avgSalePrice'];
                $tmpGuidePrice = $aInfo['avgGuidePirce'];
            }
            $priceList['price'][$start] = $tmpPrice;
            $priceList['guidePrice'][$start] = $tmpGuidePrice;
        }
        //var_dump($priceList);exit;
        if ($tmpPrice === 0) {
            return false;
        }
        return $priceList;

    }

    public static function getBatchTypeFull($sUnitID)
    {
        $sql = "select * FROM ".self::TABLE_NAME." where UnitId='".$sUnitID."' order by MarketDate desc";
        return self::query($sql);
    }

    // 最新批次信息,返回的是最新批次的一组批次数据。
    public static function getNewestBatchTypes($sUnitID)
    {
        $sql = "SELECT * FROM ".self::TABLE_NAME."  WHERE UnitId = '".$sUnitID."' order by BatchRank asc,pushdate desc,rowdate desc,PriceRank asc,marketdate desc";
        $result = self::query($sql);
        if (empty($result)) {
            return null;
        }
        $tempArr  = array();
        foreach ($result as $item) {
            if ($item['CricPrice'] > 0 && $item['SalePrice'] > 0) {
                $tempArr[] = $item;
            }
        }
        $batchId = '';
        $sql = "SELECT * FROM BatchTypeFull WHERE BatchId = '%s'  order by BatchRank asc,pushdate desc,rowdate desc,PriceRank asc,marketdate desc";
        if (!empty($tempArr)) {
            $batchId = $tempArr[0]['BatchId'];
            return self::query(sprintf($sql,$batchId));
        }
        $tempArr  = array();
        foreach ($result as $item) {
            if ($item['CricPrice'] > 0) {
                $tempArr[] = $item;
            }
        }
        if (!empty($tempArr)) {
            $batchId = $tempArr[0]['BatchId'];
            return self::query(sprintf($sql,$batchId));
        }
        return null;
    }

    public static function getBatchTypeFullData($sUnitID,$where)
    {
        //unitid = '$sUnitID' ORDER BY PushDate DESC,rowDate DESC
        $sql = "SELECT ID,UnitId,CricPrice,SalePrice, BatchId,BatchTypeCode,PushDate,RoomCount,FitmentDesign,MarketDate,ManualLevelCode,HomeType
                FROM ".self::TABLE_NAME." WHERE $where";
        return self::query($sql);
    }
}