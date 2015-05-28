<?php

/**
 * Created by PhpStorm.
 * User: chasel
 * Date:  2015/3/20
 * Time:  15:40
 */
class Controller_Mapi_Map extends Controller_Mapi_Base
{

    /**
     * 地图搜房
     */
    public function searchAction()
    {
        $result = array();
        $cityID = intval($this->getParam('cityID'));
        $zoomLevel = intval($this->getParam('zoomLevel'));
        $minLat = floatval($this->getParam('minLat'));
        $minLon = floatval($this->getParam('minLon'));
        $maxLat = floatval($this->getParam('maxLat'));
        $maxLon = floatval($this->getParam('maxLon'));

        $result = array(
            'code' =>0,
            'msg' =>'ok',
            'data' =>array()
        );

        if( !empty($cityID) && !empty($zoomLevel) && !empty($minLat) && !empty($minLon) && !empty($maxLat) && !empty($maxLon) ) {
            if(3 == $zoomLevel) {
                $color = Yaf_G::getConf('recommend_color',null,'mapi');

                $result['data']['buildList'] = array();
                $aBuilding = Model_Loupan::getLouPanByZone($cityID, $minLon, $minLat, $maxLon, $maxLat);
                if(!empty($aBuilding)) {
                    foreach($aBuilding as $building) {
                        $colorIndex = $building['sEJScore'];

                        $sPrice = empty($building['iBidingPrice']) ? '待定' : $building['iBidingPrice'];
                        $sUnit = empty($building['iBidingPrice']) ? '' : '元/平';

                        $result['data']['buildList'][] = array(
                            'iCodeID' => $building['iAutoID'],
                            'sName' => $building['sName'],
                            'sPrice' => $sPrice,
                            'iPriceTrend' => $building['iPriceTrend'],
                            'sPriceUnit' => $sUnit,
                            'sLat' => $building['iLat'],   // 纬度
                            'sLon' => $building['iLng'],    // 经度
                            'sNum' => $building['iOnSellHouseNum']
                        );
                    }
                }
            }else {
                $aRegin = Model_CricRegion::getRegionByZone($cityID, $minLon, $minLat, $maxLon, $maxLat);
                $result['data']['regionList'] = array();
                if(!empty($aRegin)) {
                    foreach($aRegin as $region) {
                        $priceTrend = $region['PriceList'];
                        $priceTrend = explode(',', $priceTrend);
                        $len = sizeof(($priceTrend));

                        $last = $len -1;
                        $vSec = $last - 1;
                        $trend = $priceTrend[$last] - $priceTrend[$vSec];
                        if($trend > 0) {
                            $trend = 1;
                        }else if($trend < 0) {
                            $trend = 0;
                        }else {
                            $trend = 2;
                        }

                        $buildings = Model_Loupan::getLoupanByRID($region['ID']);

                        $sPrice = empty($region['BiddingPrice']) ? '待定' : $region['BiddingPrice'];
                        $sUnit = empty($region['BiddingPrice']) ? '' : '元/平';

                        $result['data']['regionList'][] = array(
                            'iCodeID' => $cityID,            // 城市ID
                            'sName' => $region['RegionName'],       // region名称
                            'sPrice' => $sPrice,
                            'iPriceTrend' => $trend,      // 价格趋势 0 =>跌，1 =>涨，2 =>平
                            'sPriceUnit' => $sUnit,
                            'sLat' => $region['Y'],   // 纬度
                            'sLon' => $region['X'],    // 经度
                            'sNum' => sizeof($buildings)           // 新开楼盘数
                        );
                    }
                }
            }
        }else {
            $result = array('code' =>1, 'msg' =>'参数不全', 'data' =>array());
        }

        return $this->showMsg($result, true);
    }


    /**
     * 地图搜房
     */
    public function websearchAction()
    {
        $cityID = intval($this->getParam('cityID'));
        $zoomLevel = intval($this->getParam('zoomLevel'));
        $keywords = $this->getParam('keywords');
        $minLat = floatval($this->getParam('minLat'));
        $minLon = floatval($this->getParam('minLon'));
        $maxLat = floatval($this->getParam('maxLat'));
        $maxLon = floatval($this->getParam('maxLon'));

        $result = array(
            'code' =>0,
            'msg' =>'ok',
            'data' =>array()
        );

        if( !empty($cityID) ) {
            $city = Model_CricCity::getRow(['where'=>['ID' => $cityID, 'State' => 1]]);
            if(!empty($city)) {
                $result['CityCode'] = $city['CityCode'];
                $result['Keyword'] = '';
                $result['ZoomLevel'] = $zoomLevel;
                $result['MinZoomLevel'] = 12;
                $result['MaxZoomLevel'] = 17;
                $result['X8'] = $city['X'];
                $result['Y8'] = $city['Y'];

                $result['Overlays'] = array();
                $aBuilding = array();

                $where = array(
                    'Category' => 1,
                    'State' => 1,
                    'CityCode' => $city['CityCode']
                );

                if(!empty($minLat) && !empty($minLon) && !empty($maxLat) && !empty($maxLon)){
                    $where['X <='] = $maxLon;
                    $where['X >='] = $minLon;
                    $where['Y <='] = $maxLat;
                    $where['Y >='] = $minLat;
                }

                if(!empty($keywords)) {
                    $where['sWhere'] = "LOCATE('".$keywords. "',Keywords)";
                }

                $aBuilding = Model_CricUnitSearch::getAll(['where'=>$where]);

                if(!empty($aBuilding)) {
                    foreach($aBuilding as $building) {
                        $eva = Model_Evaluation::getRow(['where'=>array(
                            'iUnitID' => intval($building['ID']),
                            'iStatus' => 1,
                            'iPublish' => 1,
                            'sFinished <>' => ''
                        )]);

                        $detail = Model_Loupan::getRow(['where'=> array(
                            'sMapUnitID' => $building['UnitID'],
                            'iStatus' => 1
                        )]);

                        $hasEva = 0;
//                        $evaUrl = '';
                        if(!empty($eva)){
                            $hasEva = 1;
//                            $finished = $eva['sFinished'];
//                            $finished = explode(',', $finished);
//
//                            $evaUrl = Util_Uri::getEvaluationDetailUrl($eva['iEvaluationID'], $finished[0], $city['CityCode']);
                        }

                        $result['Overlays'][] = array(
                             "PK" => intval($building['ID']),
                             "BlockTotal" => intval($building['BlockTotal']),
                             "EJScore" => $building['EJScore'],
                             "OfficalAddr" => $building['OfficalAddr'],
                             "UnitID" => $building['UnitID'],
                             "Region" => $building['Region'],
                             "District" => $building['District'],
                             "AddrList" => $building['AddrList'],
                             "Category" => 1,
                             "Price" => intval($building['Price']),
                             "BiddingPrice" => intval($building['BiddingPrice']),
                             "RoomTotal" => intval($building['RoomTotal']),
                             "EvaluationFlag" => $hasEva,
                             "Tip" => $building['OfficalName'],
                             "PictureUrl" => Util_Uri::getCricViewURL($building['MainPic'], 120, 90),
                             "TotalScore" => empty($detail) ? 0 : floatval($detail['iTotalScore']),
//                             "Url" => "",//楼盘详情
//                             "EvaluationUrl" => $evaUrl,
//                             "UnitCommentUrl" => $evaUrl,
//                             "DiscountUrl" => "",
//                             "PriceTableUrl" => "",
//                             "PriceInfoUrl" => "",
                             "X8" => $building['X'],
                             "Y8" => $building['Y'],
                        );
                    }
                }
            }else {
                $result = array('code' =>1, 'msg' =>'该城市不存在', 'data' =>array());
            }
        }else {
            $result = array('code' =>1, 'msg' =>'参数不全', 'data' =>array());
        }

        return $this->showMsg($result, true);
    }

}