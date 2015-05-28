<?php

/**
 * Created by PhpStorm.
 * User: chasel
 * Date:  2015/3/20
 * Time:  15:40
 */
class Controller_Mapi_Evaluation extends Controller_Mapi_Base
{

    /**
     * 获取评测列表
     */
    public function listAction()
    {
        $cityID = intval($this->getParam('cityID'));
        $page = intval($this->getParam('iPage'));
        $rows = intval($this->getParam('iRows'));

        $result = array(
            'code' =>  0,
            'msg' => 'ok',
        );

        if(!empty($cityID) && !empty($page) && !empty($rows)) {
            $result['data'] = array(
                'iTotalNum' => 0,
                'iPage' => $page,
                'iRows' => $rows,
                'list' => array()
            );

            $sql = $this->genSearch();
            $aLoupan = Model_Loupan::query($sql);

            $buildingIDs = array();
            $buildings = array();
            if(!empty($aLoupan)) {
                foreach($aLoupan as $lp) {
                    $buildingID = $lp['sMapUnitID'];
                    $unit = Model_CricUnit::getByUnitID($buildingID);
                    if(!empty($unit)) {
                        $unitID = $unit['ID'];
                        $buildingIDs[] = $unit['ID'];
                        $buildings[$unitID] = $lp;
                    }
                }
            }

            if(!empty($buildingIDs)) {
                $where = array(
                    'sFinished !=' => '',
                    'iStatus' => 1,
                    'iCityID' => $cityID,
                    'iUnitID IN' => $buildingIDs
                );

                $evaluations = Model_Evaluation::getList($where, $page, 'iCreateTime DESC', $rows, '', '', false);
                $result['data']['iTotalNum'] = $evaluations['iTotal'];

                if(!empty($evaluations['aList'])) {
                    foreach($evaluations['aList'] as $eva) {
                        $iUnitID = $eva['iUnitID'];
                        $aChapter = Model_Evaluation::getEvaluationInfo($iUnitID, $this->isApp);
                        $analyst = Model_Analysts::getDetail(intval($eva['iAnalysisID']));

                        $sPrice = empty($buildings[$iUnitID]['iBidingPrice']) ? '待定' : number_format($buildings[$iUnitID]['iBidingPrice']);
                        $sUnit = empty($buildings[$iUnitID]['iBidingPrice']) ? '' : '元/平';

                        $result['data']['list'][] = array(
                            'iBuildingID' => $buildings[$iUnitID]['iAutoID'],    // 楼盘ID
                            'iEvaluateID' => $eva['iEvaluationID'],
                            'sImgUrl' => empty($buildings[$iUnitID]['sZongPinPic']) ? Util_Uri::getDefaultImg() : Util_Uri::getCricViewURL($buildings[$iUnitID]['sZongPinPic'], 600, 338),
                            'sAnylistAvatarImgUrl' => empty($analyst) ? Util_Uri::getDefaultImg(2) : Util_Uri::getCricViewURL($analyst['AnalystHead'], 120, 120, 0, 0, 1),
                            'sName' => $buildings[$iUnitID]['sName'],
                            'sPrice' => $sPrice,
                            'sUnit' => $sUnit,
                            'sRegion' => $buildings[$iUnitID]['sRegionName']. '－'. $buildings[$iUnitID]['sZoneName'],
                            'sRate' => $buildings[$iUnitID]['iTotalScore'],
                            'sCompletePageUrl' => $aChapter['sCompletePageUrl'],
                            'chapter' => $aChapter['chapter']
                        );
                    }
                }
            }
        }else {
            $result['code'] = 1;
            $result['msg'] = '参数不全';
        }

        return $this->showMsg($result, true);
    }

    public function genSearch() {
        $cityID = intval($this->getParam('cityID'));
        $iPage = intval($this->getParam('iPage'));
        $iRows = intval($this->getParam('iRows'));

        $regionID = $this->getParam('regionID');
        $priceID = intval($this->getParam('priceID'));
        $layoutID = intval($this->getParam('layoutID'));

        $sql = 'select * from t_loupan where iCityID ='. $cityID. ' and iStatus = 1 and iTotalScore > 0';
        if (!empty($regionID)) {
            if(is_numeric($regionID)) {
                $regionID = intval($regionID);
                $region = Model_CricRegion::getDetail($regionID);
                if(!empty($region)){
                    $sql .= " and sRegionName = '". $region['RegionName']. "'";
                }
            }else {
                $sql .= " and sZoneName = '". $regionID. "'";
            }
        }

        if(!empty($priceID)) {
            switch($priceID) {
                case 1:
                    $sql .= ' and iBidingPrice < 10000';
                    break;
                case 2:
                    $sql .= ' and iBidingPrice >= 10000 and iBidingPrice < 15000';
                    break;
                case 3:
                    $sql .= ' and iBidingPrice >= 15000 and iBidingPrice < 20000';
                    break;
                case 4:
                    $sql .= ' and iBidingPrice >= 20000 and iBidingPrice < 25000';
                    break;
                case 5:
                    $sql .= ' and iBidingPrice >= 25000 and iBidingPrice < 30000';
                    break;
                case 6:
                    $sql .= ' and iBidingPrice >= 30000 and iBidingPrice < 50000';
                    break;
                case 7:
                    $sql .= ' and iBidingPrice >= 50000';
                    break;
                default:
                    break;
            }

        }

        if(!empty($layoutID)) {
            $layout = Yaf_G::getConf('condition_layout', null, 'mapi');
            $layout = $layout[$layoutID];

            $sql .= " and FIND_IN_SET('$layout',sRoomType)";
        }

        return $sql;
    }

}