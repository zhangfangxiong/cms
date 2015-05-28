<?php

class Model_CricUnitSearch extends Model_Base
{

    const DB_NAME = 'cric_xf';

    const TABLE_NAME = 'UnitSearch';


    const REG_TSZF = '/[^a-zA-Z0-9\x{4e00}-\x{9fa5}]/u'; //非字母数字 中文

    const REG_ZW = '/[\x{4e00}-\x{9fa5}]+/u'; // 多个汉字

    const NUM = 25; // 返回记录数
    /*
     * @param $cityCode 城市代码
     * @param $keywords 搜索关键字
     * @param $num 返回记录数
     * @param $category 区分新房 二手房
     * 搜索建议
     */
    public static  function SuggestUnits($cityCode,$keywords,$category)
    {
        $includeBatch = 1;
        $limit = " limit 0, ".self::NUM;
        $sql = " SELECT temp.*,u.TotalScore,u.ZongPingXQ,u.EvaluationFlag FROM (
                  SELECT s.*,0 as BatchId,'' as BatchTypeCode,0 as ManualLevelCode  FROM UnitSearch as s
                  where CityCode = '".$cityCode."'and State > 0 and X> 0 and (s.Keywords like '%".$keywords."%' OR s.OfficalName like '%".$keywords."%' OR OfficalAddr like '%".$keywords."%') ".$limit."
                ) AS temp LEFT JOIN  Unit as u  on u.UnitID = s.UnitID and u.CityCode=s.CityCode
                order by temp.Category DESC,temp.RoomTotal DESC,temp BatchId ASC ";
        $result = self::query($sql);
        return $result;
        /*
        if ($includeBatch) {
            $unitIDArr = array();
            if (!empty($result)) {
                foreach($result as $k => $item) {
                    $unitIDArr[] = $item['Unit'];
                }
            }
            //$sql = "select ID,UnitID, BatchTypeCode,ManualLevelCode,count(ID) as num from BatchType group by unitid HAVING num=1  where ";
        }*/
    }

    public static function Suggest($cityCode,$keywords,$category)
    {
        $result = self::getSuggest($cityCode,$keywords,$category);

        foreach($result as $k => $item) {

        }
    }


    public static  function getSuggest($cityCode,$keywords,$category)
    {
        $keywords = preg_replace(self::REG_TSZF,'',$keywords);
        if (empty($keywords)) {
            return null;
        }

        $result = self::SuggestUnits($cityCode,$keywords,$category);
        return $result;
    }

}