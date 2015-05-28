<?php

class Model_CricAnalystsOpinion extends Model_Base
{
    const DB_NAME = 'cric_xf';

    const TABLE_NAME = 'Analysts_Opinion';

    /*
     * 获取楼盘相关的分析师点评
     */
    public static function getAnalystsOpinionByBID($iBuildingID)
    {
        $sSQL = "SELECT a.*,b.GoodCount, b.ID aid, b.AnalystLevel, b.AnalystHead as bAnalystHead FROM Analysts_Opinion a join Analysts b on a.AnalystsID = b.AnalystsID WHERE a.UnitId = '".addslashes($iBuildingID). "' ORDER BY SortNum DESC,UpdateTime DESC";

        return self::query($sSQL,'all');
    }

    /*
     * 获取某一分析师的点评列表
     */
    public static function getOpinionListByID($iAnalystID, $rows, $page)
    {
        $analyst = Model_Analysts::getAnalystByID($iAnalystID);

        if(!empty($analyst)) {
            $iAnalystID = $analyst['AnalystsID'];

            $where = array(
                'AnalystsID' => $iAnalystID
            );

            $options = self::getList($where, $page, 'UpdateTime desc', $rows, '', '', false);

            if(!empty($options)) {
                $len = sizeof($options['aList']);
                for($i = 0; $i < $len; $i ++) {
                    $buildingId = $options['aList'][$i]['UnitID'];
                    $building = Model_Loupan::getRow(array('where'=>array('sMapUnitID' => $buildingId, 'iStatus' => 1)));

                    if(!empty($building)) {
                        $options['aList'][$i]['iBuildingID'] = $building['iAutoID'];
                        $options['aList'][$i]['sBuildingTitle'] = $building['sName'];
                        $options['aList'][$i]['sScore'] = $building['iTotalScore'];

                        $pics = Model_LoupanPic::getList(array('iLoupanID'=>$building['iAutoID']), 1, '', 3, '', '', false);
                        $options['aList'][$i]['images'] = array();
                        if(!empty($pics['aList'])) {
                            foreach($pics['aList'] as $pic) {
                                $options['aList'][$i]['images'][] = Util_Uri::getCricViewURL($pic['sPictureCode'], 640, 420, 19, 4);
                            }
                        }else {
                            $staticUrl = Yaf_G::getConf('static','domain');
                            $options['aList'][$i]['images'][] = Util_Uri::getDefaultImg();
                        }
                    }else {
//                        $options['aList'][$i]['iBuildingID'] = '';
//                        $options['aList'][$i]['sBuildingTitle'] = '';
//                        $options['aList'][$i]['sScore'] = '';
//                        $options['aList'][$i]['images'] = array();
                        unset($options['aList'][$i]);
                    }
                }

                return $options;
            }
        }

        return false;
    }
}