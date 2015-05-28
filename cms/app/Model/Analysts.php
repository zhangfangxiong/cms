<?php
/**
 * 分析师业务管理类
 * author: cjj
 */
class Model_Analysts extends Model_Base
{

    const DB_NAME = 'cric_xf';

    const TABLE_NAME = 'Analysts';

    /*
     * 获取当前城市的分析师
     * return array
     */
    public static function getAnalystsList($iCurrCityID, $aParam=[])
    {
        $aCity = Model_City::getDetail($iCurrCityID);
        $aWhere ['City'] = $aCity['sCityName'];

        if($aParam) {
            if(isset($aParam['iStatus'])) 
                $aWhere['State'] = intval($aParam['iStatus']);
            
            if(isset($aParam['isDown']))
                $aWhere['IsDown'] = intval($aParam['isDown']);
        }

        $aParam = array(
            'where' => $aWhere,
        );

        return self::getOrm()->fetchAll($aParam);
    }

    /*
     * 获取分析师名称
     */
    public static function getAnalystNameByID($ID)
    {
        $arr = self::getDetail($ID);
        return $arr['AnalystsName'];
    }

    /*
     * 获取分析师详情
     */
    public static function getAnalystByID($ID)
    {
        $where = array(
            'ID' => $ID,
        );
        return self::getRow(array('where'=>$where));
    }

    /**
     * 获取首页分析师
     */
    public static function getAnalystByCityCode($cityCode)
    {
        $sql = "SELECT ans.AnalystsID,ans.AnalystsName,ans.AnalystLevel,ans.AnalystHead,
                        a.GoodCount,ans.AnalystSort,ans.UnitName,ans.UnitUrl,ans.RegionName,ans.Selected,
                        a.BriefIntroduction,ans.OpinionSummary,ans.ZongPingXQ
                FROM AnalystsShow ans
                LEFT JOIN Analysts a  ON ans.AnalystsID=a.AnalystsID
                WHERE ans.State=1 AND a.State=1  AND a.isDown =1 AND ans.CityCode='$cityCode'
                GROUP BY ans.AnalystsID
                ORDER BY ans.AnalystSort,ans.AnalystsID,selected DESC LIMIT 0,3";

        $analysts = self::query($sql, "all");
        if(!empty($analysts)) {
            foreach ($analysts as $key=>$value) {
                $analystsShow[] = Model_AnalystsShow::getAll(array(
                    'where' =>array('AnalystsID'=>$value['AnalystsID']),
                    'order' => 'AnalystSort,AnalystsID,selected DESC',
                    'limit' =>'0,2'
                ));
            }
        }
        return array('analysts'=>$analysts,'analystsShow'=>$analystsShow);
    }

    /**
     * 获取首页分析师点评(2条)
     */
    public static function getAnalysDianPin($cityName)
    {
            $sql = "SELECT tt.*,u.UnitName,u.ID,u.DistrictName,u.RegionName,u.CityCode
                    FROM (SELECT a.AnalystsName,ao.Opinion,ao.UpdateTime,ao.UnitID FROM Analysts a ,Analysts_Opinion ao  WHERE a.AnalystsID = ao.AnalystsID AND a.City = '$cityName') AS tt
                    LEFT JOIN Unit u ON tt.UnitID = u.UnitID ORDER BY tt.UpdateTime DESC LIMIT 0,2";
        return self::query($sql, 'all');
    }

    /**
     * 房价点评网分析师点赞
     */
    public static function analysUp($AnalystsID)
    {
        $sql = "UPDATE Analysts SET GoodCount=GoodCount+1 WHERE AnalystsID='$AnalystsID'";
        return self::query($sql);
    }
}