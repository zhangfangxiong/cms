<?php
/**
 * Created by PhpStorm.
 * User: yaobiqing
 * Date: 15/4/1
 * Time: 下午3:15
 */


class Controller_Cmd_Datatrans extends Controller_Cmd_Base
{


    public function unittransAction()
    {
        set_time_limit(0);
        //ini_set('memory_limit',0);
        ini_set('memory_limit', '3000M');
        $iPagesize = 1000;
        $sSQL = "SELECT COUNT(*) as amount FROM Unit ORDER BY ID DESC";
        $row = Model_CricUnit::query($sSQL, 'row');
        $iCount = intval($row['amount']);
        echo 'Start Trans Unit', PHP_EOL;
        if ($iCount > 0) {
            echo $iCount.' Need Tranlated', PHP_EOL;

            $iPage = ceil($iCount / $iPagesize);
            echo $iPage;
            for ($i = 0; $i < $iPage; $i++) {
                echo $i, PHP_EOL;
                $start = $i * $iPagesize;
                $sSQL = "SELECT * FROM Unit ORDER BY ID DESC Limit ".$start.",".$iPagesize;
                $unitList = Model_CricUnit::query($sSQL);
                echo time(),PHP_EOL;
                $this->_transUnit($unitList);
                echo time(),PHP_EOL;
                unset($unitList);
                $finishedRate = ceil(($i/$iPage)*100);
                echo 'Finished '.$finishedRate.'%', PHP_EOL;
            }

        }
        echo 'Finish Trans Unit', PHP_EOL;

    }


    private function _transUnit($unitList)
    {
        if ($unitList) {
            $i = 0;
            foreach($unitList as $key => $value) {
                echo $i, PHP_EOL;
                $loupanID = $this->_transLoupan($value);

                $this->_transLoupanDetail($value, $loupanID);

                $this->_transLoupanPic($value, $loupanID);
                $i++;
            }
        }

    }
    private function _transLoupan($unitInfo)
    {
        //var_dump($unitInfo);exit;
        $aCity = Model_City::getCityByFullPinyin($unitInfo['CityCode']);
        $loupan['iCityID'] = !empty($aCity) ? $aCity['iCityID'] : 0;
        $loupan['iPriceTrend'] = $this->_getPriceTrend($unitInfo);
        $loupan['sHomeID'] = $unitInfo['HomeID'];
        $loupan['iOnSellHouseNum'] = $this->_getRoomTotal($unitInfo);
        $loupan['sName'] = $unitInfo['UnitName'];
        $loupan['sCityCode'] = $unitInfo['CityCode'];
        $loupan['sRegionName'] = $unitInfo['RegionName'];
        $loupan['sZoneName'] = $unitInfo['DistrictName'];
        $loupan['sAddress'] = $unitInfo['Address'];
        $loupan['iBidingPrice'] = $unitInfo['BidingPrice'];
        $loupan['iCricPrice'] = $unitInfo['Price'];
        $loupan['iMinTotalPrice'] = $unitInfo['MinTotalPrice'];
        $loupan['iMaxTotalPrice'] = $unitInfo['MaxTotalPrice'];
        $loupan['iTotalScore'] = $unitInfo['TotalScore'];
        $loupan['iRoomTypeScore'] = $unitInfo['RoomTypeScore'];
        $loupan['iDesignScore'] = $unitInfo['DesignScore'];
        $loupan['iHouseManageScore'] = $unitInfo['HouseManageScore'];
        $loupan['iParkScore'] = $unitInfo['ParkScore'];
        $loupan['iLocationScore'] = $unitInfo['LocationScore'];
        $loupan['iCPRatioScore'] = $unitInfo['CPRatioScore'];
        $loupan['iDiscount'] = 0;
        $loupan['sEJScore'] = $unitInfo['EJScore'];
        $loupan['iEJRank'] = $unitInfo['EJRank'];
        $loupan['sRoomType'] = $unitInfo['RoomType'];
        $loupan['iRoomTotal'] = $unitInfo['RoomTotal'];
        $loupan['sOpenTime'] = $unitInfo['OpenTime'];
        $loupan['sDeliverTime'] = $unitInfo['DeliverTime'];
        $loupan['iLng'] = $unitInfo['X'];
        $loupan['iLat'] = $unitInfo['Y'];
        $loupan['sZongPinPic'] = !empty($unitInfo['ZongPingXQ']) ? $unitInfo['ZongPingXQ'] : $unitInfo['ZongPinPic'];
        $loupan['sZongPinPic'] = empty($loupan['sZongPinPic']) ? $unitInfo['ZongPinAnchor'] : $loupan['sZongPinPic'];
        $loupan['sMapUnitID'] = $unitInfo['UnitID'];
        $loupan['iCreateTime'] = time();
        $loupan['iUpdateTime'] = time();
        $loupanID = 0;
        //print_r($loupan);exit;
        $row = Model_Loupan::getRow([
               'where' => [
                   'sMapUnitID' => $unitInfo['UnitID']
               ]
            ]);
        //var_dump($row);
        if (!empty($row)) {
            //echo 133333333333333;
            $loupanID = $loupan['iAutoID'] = $row['iAutoID'];
            unset($loupan['iCreateTime']);
            if (strpos($row['sZongPinPic'], '.') !== false) {
                unset($loupan['sZongPinPic']);
            }
            //print_r($loupan);
            Model_Loupan::updData($loupan);
        } else {
            //echo 3222222222222;
            $loupanID = Model_Loupan::addData($loupan);
        }
        unset($loupan);

        return $loupanID;
    }

    private function _transLoupanDetail($unitInfo, $loupanID)
    {
        $loupan_detail['iLoupanID'] = $loupanID;
        $loupan_detail['sCircleLocation'] = $unitInfo['CircleLocation'];
        if (substr($unitInfo['HomeType'], -1) == ';'){
            $loupan_detail['sHomeType'] = str_replace(';', ',', rtrim($unitInfo['HomeType'], ';'));
        }
        $loupan_detail['sManageCorp'] = $unitInfo['ManagerCorp'];
        $loupan_detail['sManageFee'] = strval($unitInfo['ManagerFee']) + '元/平方米·月';
        $loupan_detail['sDeveloper'] = $unitInfo['Developer'];
        $loupan_detail['iBuildArea'] = $unitInfo['BuildArea'];
        $loupan_detail['sBuildType'] = $unitInfo['Conformation'];
        $loupan_detail['iPlanCount'] = $unitInfo['PlanCount'];
        if (substr($unitInfo['Propertys'],-1) == ';') {
            $loupan_detail['sProperty'] = rtrim($unitInfo['Propertys'], ';');
        }else{
            $loupan_detail['sProperty'] = $unitInfo['Propertys'];
        }
        $loupan_detail['sFitmentDesign'] = $unitInfo['FitmentDesign'];
        $loupan_detail['iFitmentPrice'] = $unitInfo['FitmentPrice'];
        $loupan_detail['sGreenRate'] = !empty($unitInfo['GreenRate']) ? strval($unitInfo['GreenRate']) + '%' : '';
        $loupan_detail['iPlotRatio'] = $unitInfo['Plotratio'];
        $loupan_detail['iParkCount'] = $unitInfo['ParkCount'];
        $loupan_detail['sParking'] = $unitInfo['Parking'];
        $loupan_detail['sSaleAddress'] = $unitInfo['SaleAddress'];
        $loupan_detail['sSalePhone'] = $unitInfo['SalePhone'];
        $loupan_detail['sAdvantageLabel'] = $unitInfo['AdvantageLabel'];
        $loupan_detail['sRiskLabel'] = $unitInfo['RiskLabel'];
        $loupan_detail['sHouseRemark'] = $unitInfo['HouseRemark'];
        $loupan_detail['sOpenNotice'] = $unitInfo['OpenNotice'];
        $loupan_detail['sCoreSellingPoint'] = $unitInfo['CoreSellingPoint'];
        $loupan_detail['sBuyRisk'] = $unitInfo['BuyerRisk'];
        $loupan_detail['iResFlag'] = $unitInfo['ResFlag'];
        $loupan_detail['sUrl'] = '';
        $loupan_detail['iCMSReportFlag'] = $unitInfo['CMSReportFlag'];
        $loupan_detail['iCreateTime'] = time();
        $loupan_detail['iUpdateTime'] = time();
        //var_dump($loupan_detail);
        $row = Model_LoupanDetail::getDetail($loupanID);
        if (!empty($row)) {
            unset($loupan_detail['iCreateTime']);
            Model_LoupanDetail::updData($loupan_detail);
        } else {
            Model_LoupanDetail::addData($loupan_detail);
        }
        unset($loupan_detail);

        return $loupanID;
    }

    private function _transLoupanPic($unitInfo, $loupanID)
    {
        $cricUnitPicOrm = Util_Common::getOrm('cric_xf', 'Unit_Picture', false);
        $sSQL = "SELECT * FROM Unit_Picture WHERE UnitID='".$unitInfo['UnitID']."' AND PictureType IN ('sjt', 'zpt', 'xgt', 'ybft', 'wlm')";
        //echo $sSQL;exit;
        $imgType = [
            'sjt' => 1,
            'xgt' => 2,
            'zpt' => 3,
            'wlm' => 5,
            'ybft' => 4
        ];
        $imglist = $cricUnitPicOrm->query($sSQL);
        //var_dump($imglist);die(22);
        if (!empty($imglist)) {
            foreach($imglist as $aImg) {
                $data = [];
                $data['iLoupanID'] = $loupanID;
                $data['sPictureCode'] = $aImg['PictureCode'];
                //var_dump($aImg);
                $data['iPictureType'] = $imgType[$aImg['PictureType']];
                $data['iStatus'] = 1;

                $data['iWidth'] = $aImg['Width'];
                $data['iSortNum'] = $aImg['SortNum'];
                $data['iHeight'] = $aImg['Height'];
                $data['sRemark'] = $aImg['Remark'];
                $data['iUpdateTime'] = $data['iCreateTime'] = time();

                $row = Model_LoupanPic::getRow([
                        'where' => [
                            'iLoupanID' => $loupanID,
                            'sPictureCode' => $aImg['PictureCode'],
                            'iPictureType' => $imgType[$aImg['PictureType']],
                        ]
                    ]);
                //var_dump($data);
                if (!empty($row)) {
                    $data['iAutoID'] = $row['iAutoID'];
                    unset($data['iCreateTime']);
                    if (strpos($row['sPictureCode'], '.') !== false) {
                        unset($data['sPictureCode']);
                    }
                    Model_LoupanPic::updData($data);
                } else {
                    Model_LoupanPic::addData($data);
                }
            }
        }
        return $loupanID;
    }

    private function _getPriceTrend($aLoupan)
    {

        $iPriceTrend = 2;
        //return $iPriceTrend;
        $aPriceList = Model_CricBatchTypeFull::getPriceTrendData($aLoupan['UnitID'], '普通住宅');
        if ($aPriceList !== false) {
            $tmpPrice = array_values($aPriceList['price']);
            $count = count($tmpPrice);
            if (($tmpPrice[$count-1] - $tmpPrice[$count-2]) > 0) {
                $iPriceTrend = 1;
            } else if (($tmpPrice[$count-1] - $tmpPrice[$count-2]) < 0) {
                $iPriceTrend = 0;
            }
        }
        return $iPriceTrend;
    }


    private function _getRoomTotal($aLoupan)
    {
        $iRoomTotal = 0;
        if (!empty($aLoupan) && !empty($aLoupan['HomeID'])) {
            $iRoomTotal = Model_CricRoomTypeRemaining::getRemainingRoomOfUnit($aLoupan['HomeID']);
        }
        return $iRoomTotal;
    }

}