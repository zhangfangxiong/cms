<?php

/**
 * Created by PhpStorm.
 * User: chasel
 * Date:  2015/3/20
 * Time:  15:40
 */
class Controller_Mapi_Newbuilding extends Controller_Mapi_Base
{
    public function actionBefore ()
    {

    }
    /**
     * 获取楼盘列表筛选条件
     */
    public function filterAction()
    {
        $result = array(
            'code' => 0,
            'msg' => 'ok'
        );
        $cityID = intval($this->getParam('cityID'));

        if(!empty($cityID)) {
            $result['data'] = array();
            $result['data']['region'] = array(
                'sName' => "区域",
                'list' => array(
                    array(
                        'iCodeID' => '',
                        'sName' => '区域不限',
                        'children' => array()
                    )
                )
            );
            $aDistrict = Model_CricDistrict::getDistrictByCID($cityID);
            $aRegion = Model_CricRegion::getRegionByCID($cityID);

            foreach($aRegion as $region) {
                $rname = $region['RegionName'];
                $rtod = array(
                    'iCodeID' => $region['ID'],
//                    'iCodeID' => $rname,
                    'sName' => $rname,
                    'children' => array(
                        array(
                            'iCodeID' => $region['ID'],
//                            'iCodeID' => $rname,
                            'sName' => '全部',
                            'children' => array()
                        )
                    )
                );

                foreach($aDistrict as $district) {
                    if(in_array($rname, $district)) {
                        $rtod['children'][] = array(
//                            'iCodeID' => $district['ID'],
                            'iCodeID' => $district['DistrictName'],
                            'sName' => $district['DistrictName']
                        );
                    }
                }

                $result['data']['region']['list'][] = $rtod;
            }

            $price = Yaf_G::getConf('condition_price',null,'mapi');
            $layout = Yaf_G::getConf('condition_layout',null,'mapi');

            $result['data']['price'] = array(
                'sName' => '价格',
                'list' => array(
                    array(
                        'iCodeID' => 0,
                        'sName' => '全部'
                    )
                )
            );
            foreach($price as $key => $value) {
                $result['data']['price']['list'][] = array(
                    'iCodeID' => $key,
                    'sName' => $value
                );
            }

            $result['data']['layout'] = array(
                'sName' => '户型',
                'list' => array(
                    array(
                        'iCodeID' => 0,
                        'sName' => '全部'
                    )
                )
            );
            foreach($layout as $key => $value) {
                $result['data']['layout']['list'][] = array(
                    'iCodeID' => $key,
                    'sName' => $value
                );
            }
        } else{
            $result['code'] = 1;
            $result['msg'] = '城市ID不能为空';
        }

        return $this->showMsg($result, true);
    }

    /**
     * 获取楼盘列表特色筛选条件
     */
    public function filterfeaturesAction()
    {
        $result = array(
            'code' => 0,
            'msg' => 'ok'
        );
        $cityID = intval($this->getParam('cityID'));

        if(!empty($cityID)) {

            $aCity = Model_City::getCityByID($cityID);
            $listType = Model_CricUnitZT0114::getCurrYearListType($aCity['sCityName']);
            $features = [];
            $features = $listType;

            $result['data']['features'] = $features;
        } else{
            $result['code'] = 1;
            $result['msg'] = '城市ID不能为空';
        }

        return $this->showMsg($result, true);
    }

    /**
     * 获取新开盘楼盘列表
     */
    public function monthlistAction()
    {
        $cityID = intval($this->getParam('cityID'));
        $iPage = intval($this->getParam('iPage'));
        $iRows = intval($this->getParam('iRows'));

        $result = array(
            'code' => 0,
            'msg' => 'ok'
        );

        if(empty($cityID) || empty($iPage) || empty($iRows)) {
            $result = array(
                'code' => 1,
                'msg' => '参数不全'
            );
        }else {
            $color = Yaf_G::getConf('recommend_color',null,'mapi');
            $recommend = Yaf_G::getConf('recommend',null,'mapi');

            $aLoupan = Model_Loupan::getLoupanByCID($cityID, $iRows, $iPage);

            $result['data'] = array(
                'iTotalNum' => $aLoupan['iTotal'],
                'iPage' => $iPage,
                'iRows' => $iRows,
                'list' => array()
            );

            if(!empty($aLoupan['aList'])) {
                $staticUrl = Yaf_G::getConf('static','domain');

                foreach($aLoupan['aList'] as $lp) {
                    $sFlagText = $lp['sEJScore'];

                    $detail = Model_LoupanDetail::getDetail($lp['iAutoID']);
                    $advantage = null;
                    if(!empty($detail)) {
                        $advantage = $detail['sAdvantageLabel'];
                    }

                    $sPrice = empty($lp['iBidingPrice']) ? '待定' : number_format($lp['iBidingPrice']);
                    $sUnit = empty($lp['iBidingPrice']) ? '' : '元/平';

                    $result['data']['list'][] = array(
                        'iBuildingID' => $lp['iAutoID'],
                        'sLat' => $lp['iLat'],
                        'sLon' => $lp['iLng'],
                        'sImgUrl' => empty($lp['sZongPinPic'])? Util_Uri::getDefaultImg() : Util_Uri::getCricViewURL($lp['sZongPinPic'], 213, 160),
                        'sName' => $lp['sName'],
                        'sPrice' => $sPrice,
                        'iPriceTrend' => $lp['iPriceTrend'],
                        'sOnSellHouseNum' => $lp['iOnSellHouseNum'],
                        'sUnit' => $sUnit,
                        'sRegion' => $lp['sRegionName']. '-'. $lp['sZoneName'],
                        'sRate' => $lp['iTotalScore'],
                        'iFlagID' => Model_Loupan::getRecommend($sFlagText),
//                        'sFlagColor' => empty($sFlagText) ? '': $color[$sFlagText],
                        'sListName' => '',      // 暂时没有数据，以后会再补上（cara说）
                        'sAdvantages' => $advantage
                    );
                }
            }
        }

        return $this->showMsg($result, true);
    }

    /**
     * 获取新房楼盘列表(搜索)
     */
    public function listAction()
    {
        $cityID = intval($this->getParam('cityID'));
        $iPage = intval($this->getParam('iPage'));
        $iRows = intval($this->getParam('iRows'));

        $regionID = $this->getParam('regionID');
        $priceID = intval($this->getParam('priceID'));
        $layoutID = intval($this->getParam('layoutID'));
        $featureID = addslashes(trim($this->getParam('featureID')));     //待定

        $result = array(
            'code' => 0,
            'msg' => 'ok'
        );


        if(empty($cityID) || empty($iPage) || empty($iRows)) {
            $result = array(
                'code' => 1,
                'msg' => '参数不全'
            );
        }else {
            $result['data'] = array(
                'iTotalNum' => 0,
                'iPage' => $iPage,
                'iRows' => $iRows,
                'list' => array()
            );
            $iShowRank = true;

            $recommend = Yaf_G::getConf('recommend',null,'mapi');
            if (empty($featureID)) {
                $sql = $this->genSearch();
                $sqlTotal = str_replace('*,', 'count(*) num,', $sql);

                $loupanAll = Model_Loupan::query($sqlTotal);
                if ($this->getParam('lat') && $this->getParam('lon')) {
                    $lng = $this->getParam('lon');
                    $lat = $this->getParam('lat');
                    $sql .= "  ORDER BY distance ASC";
                    //echo $sql;
                } else {
                    $sql .= " ORDER BY FIELD(sEJScore, 'AAA', 'A+', 'BBB+', 'C', '') ASC";

                }

            } else {
                $sql = $this->genFeatureSearch();
                $sqlTotal = str_replace('*,', 'count(*) num,', $sql);
                $loupanAll = Model_Loupan::query($sqlTotal);

                $aCity = Model_City::getCityByID($cityID);
                $listType = Model_CricUnitZT0114::getCurrYearListType($aCity['sCityName']);
                if (!empty($listType)) {
                    foreach($listType as $aListType) {
                        if ($aListType['sCode'] == $featureID && $aListType['sName'] == '最佳户型榜') {
                            $iShowRank = false;
                            break;
                        }
                    }

                }

            }


            $iPage = max($iPage, 1);
            $sql .= ' limit '.  ($iPage - 1) * $iRows . ',' . $iRows;

            $aLoupan = Model_Loupan::query($sql);
            $result['data']['iTotalNum'] = isset($loupanAll[0]['num']) ? $loupanAll[0]['num'] : 0;

            if(!empty($aLoupan)) {
                $staticUrl = Yaf_G::getConf('static','domain');
                $i = ($iPage - 1)*$iRows + 1;
                foreach($aLoupan as $lp) {
                    $sFlagText = $lp['sEJScore'];

                    $detail = Model_LoupanDetail::getDetail($lp['iAutoID']);
                    $advantage = null;
                    if(!empty($detail)) {
                        $advantage = $detail['sAdvantageLabel'];
                    }

                    $sPrice = empty($lp['iBidingPrice']) ? '待定' : number_format($lp['iBidingPrice']);
                    $sUnit = empty($lp['iBidingPrice']) ? '' : '元/平';
                    if ($iShowRank) {
                        if ($i > 3) {
                            $sRate = '';
                        } else {
                            $sRate = strval($i);
                        }
                    } else {
                        $sRate = '';
                    }

                    $result['data']['list'][] = array(
                        'iBuildingID' => $lp['iAutoID'],
                        'sLat' => $lp['iLat'],
                        'sLon' => $lp['iLng'],
                        'distance' => isset($lp['distance']) ? round($lp['distance'], 2).'km' : '',
                        'sImgUrl' => empty($lp['sZongPinPic'])? Util_Uri::getDefaultImg() : Util_Uri::getCricViewURL($lp['sZongPinPic'], 213, 160),
                        'sName' => $lp['sName'],
                        'sPrice' => $sPrice,
                        'iPriceTrend' => $lp['iPriceTrend'],
                        'sOnSellHouseNum' => $lp['iOnSellHouseNum'],
                        'sUnit' => $sUnit,
                        'sRegion' => $lp['sRegionName']. '-'. $lp['sZoneName'],
                        'sRate' => !empty($featureID) ? $sRate : $lp['iTotalScore'],
                        'iFlagType' => Model_Loupan::getRecommend($sFlagText),
                        'sListName' => '',      // 暂时没有数据，以后会再补上（cara说）
                        'sAdvantages' => $advantage
                    );
                    $i++;
                }
            }
        }

        return $this->showMsg($result, true);
    }




    /**
     * 搜索有评测报告的楼盘
     */
    public function listRBuildingsAction()
    {
        $cityID = intval($this->getParam('cityID'));
        $iPage = intval($this->getParam('iPage'));
        $iRows = intval($this->getParam('iRows'));

        $regionID = $this->getParam('regionID');
        $priceID = intval($this->getParam('priceID'));
        $layoutID = $this->getParam('layoutID');
//        $featureID = intval($this->getParam('featureID'));     //待定
        $keyWord = $this->getParam('keyWord'); //用户输入的楼盘名字或地址

        $result = array(
            'code' => 0,
            'msg' => 'ok'
        );

        if(empty($cityID) || empty($iPage) || empty($iRows)) {
            $result = array(
                'code' => 1,
                'msg' => '参数不全'
            );
        }else {
            $result['data'] = array(
                'iTotalNum' => 0,
                'iPage' => $iPage,
                'iRows' => $iRows,
                'list' => array()
            );

            $recommend = Yaf_G::getConf('recommend',null,'mapi');
            $sql = $this->genSearch();

            $evaOption = array(
                'iCityID' => $cityID,
                'sFinished !=' => '',
                'iStatus' => 1
            );

            //评测报告中的楼盘id对应的是cric_xf表Unit的ID字段，要先转成UnitID，然后再到xinfang数据库中根据sMapUnitID来搜索
            $evaluations = Model_Evaluation::getAll(['where' => $evaOption, 'group' => 'iUnitID']);
            $buildingIDs = array();

            if(!empty($evaluations)) {
                foreach($evaluations as $evaluation) {
                    $unit = Model_CricUnit::getRow(array('where'=> array('ID'=>$evaluation['iUnitID'], 'State'=>1)));
                    if(!empty($unit)) {
                        $buildingIDs[] = $unit['UnitID'];
                    }
                }

                if(!empty($buildingIDs)) {
                    $sql .= ' and sMapUnitID in (';
                    $len = sizeof($buildingIDs);
                    $last = $len -1;
                    for($i = 0; $i < $len; $i ++) {
                        if($i == $last) {
                            $sql .= "'". $buildingIDs[$i]. "'";
                        }else {
                            $sql .= "'". $buildingIDs[$i]. "',";
                        }
                    }
                    $sql .= ')';

                    $sqlTotal = str_replace('*,', 'count(*) num,', $sql);

                    $loupanAll = Model_Loupan::query($sqlTotal);

                    $sql .= " ORDER BY FIELD(sEJScore, 'AAA', 'A+', 'BBB+', 'C', '') ASC";

                    $iPage = max($iPage, 1);
                    $sql .= ' limit '.  ($iPage - 1) * $iRows . ',' . $iRows;

                    $aLoupan = Model_Loupan::query($sql);

                    $result['data']['iTotalNum'] = $loupanAll[0]['num'];

                    if(!empty($aLoupan)) {
                        foreach($aLoupan as $lp) {
                            $sFlagText = $lp['sEJScore'];

                            $sPrice = empty($lp['iBidingPrice']) ? '待定' : number_format($lp['iBidingPrice']);
                            $sUnit = empty($lp['iBidingPrice']) ? '' : '元/平';

                            $result['data']['list'][] = array(
                                'iBuildingID' => $lp['iAutoID'],
                                'sLat' => $lp['iLat'],
                                'sLon' => $lp['iLng'],
                                'sImgUrl' => empty($lp['sZongPinPic']) ? Util_Uri::getDefaultImg() : Util_Uri::getCricViewURL($lp['sZongPinPic'], 213, 160),
                                'sName' => $lp['sName'],
                                'sPrice' => $sPrice,
                                'iPriceTrend' => $lp['iPriceTrend'],
                                'sOnSellHouseNum' => $lp['iOnSellHouseNum'],
                                'sUnit' => $sUnit,
                                'sRegion' => $lp['sRegionName']. '-'. $lp['sZoneName'],
                                'sRate' => $lp['iTotalScore'],
                                'iFlagType' => Model_Loupan::getRecommend($sFlagText),
                                'sListName' => ''      // 暂时没有数据，以后会再补上（cara说）
                            );
                        }
                    }

                }

            }

        }

        return $this->showMsg($result, true);
    }




    /**
     * 获取详情页面数据
     */
    public function detailAction()
    {
        $result = array(
            'code' => 0,
            'msg' => 'ok',
            'data' => array()
        );

        $iBuildingID = intval($this->getParam('buildingID'));
        if(!empty($iBuildingID)) {
            $bDetail = Model_Loupan::getDetail($iBuildingID);
            $relatedDetail = Model_LoupanDetail::getDetail($iBuildingID);
            $imgNum = Model_LoupanPic::getCnt(array('where'=> array('iLoupanID'=> $iBuildingID, 'iStatus'=> 1)));

            $aRoomType = array();
            $aAnalystsOpinion = array();
            if(!empty($bDetail)) {
                $iUnitID = intval(Model_Loupan::getUnitCricID($bDetail['sMapUnitID']));

                $aRoomType = Model_CricRoomType::getRoomTypeByBID($bDetail['sMapUnitID']);
                $aAnalystsOpinion = Model_CricAnalystsOpinion::getAnalystsOpinionByBID($bDetail['sMapUnitID']);

                $result['data']['analyst'] = array();
                if(!empty($aAnalystsOpinion)) {
                    $result['data']['analyst']['iAnalystID'] = $aAnalystsOpinion[0]['aid'];
                    $result['data']['analyst']['sName'] = $aAnalystsOpinion[0]['AnalystsName'];
                    $result['data']['analyst']['sAvatarImgUrl'] = empty($aAnalystsOpinion[0]['bAnalystHead'])? Util_Uri::getDefaultImg(2) : Util_Uri::getCricViewURL($aAnalystsOpinion[0]['bAnalystHead'], 120, 120, 0, 0, 1);
                    $result['data']['analyst']['sTitle'] = $aAnalystsOpinion[0]['AnalystsTitle'];
                    $result['data']['analyst']['sText'] = $aAnalystsOpinion[0]['Opinion'];
                }

                $lowPrice = Model_CricRoom::getLowestPrice($bDetail['sMapUnitID']);

                $color = Yaf_G::getConf('recommend_color',null,'mapi');
                $recommend = Yaf_G::getConf('recommend_text',null,'mapi');
                $recommendFlag = Yaf_G::getConf('recommend',null,'mapi');

                $comflag = $bDetail['sEJScore'];
                $tagList = Model_CricUnitZT0114::getUnitListType($bDetail['sHomeID']);
                $result['data']['iBuildingID'] = $bDetail['iAutoID'];
                $result['data']['sBuildName'] = $bDetail['sName'];
                $result['data']['sHouseRemark'] = isset($relatedDetail['sHouseRemark']) ? $relatedDetail['sHouseRemark'] : '';
                $result['data']['sHeadImgUrl'] = empty($bDetail['sZongPinPic']) ? Util_Uri::getDefaultImg(3) : Util_Uri::getCricViewURL($bDetail['sZongPinPic'], 640, 420, 19, 4);
                $result['data']['sImgNum'] = $imgNum;

                $result['data']['sTouchwebUrl'] = 'http://'.Yaf_G::getConf('touchweb', 'domain'). '/h5/xf/xfdetail?id='. $bDetail['iAutoID'];

                $sPrice = empty($bDetail['iBidingPrice']) ? '待定' : number_format($bDetail['iBidingPrice']);
                $sUnit = empty($bDetail['iBidingPrice']) ? '' : '元/平';

                $result['data']['sPrice'] = $sPrice;
                $result['data']['sPriceUnit'] = $sUnit;

                $sPrice = empty($bDetail['iCricPrice']) ? '待定' : number_format($bDetail['iCricPrice']);
                $sUnit = empty($bDetail['iCricPrice']) ? '' : '元/平';

                $result['data']['suggestPrice'] = $sPrice;
                $result['data']['suggestPriceUnit'] = $sUnit;

                $result['data']['tagList'] = $tagList;
                $result['data']['sAddress'] = '['.$bDetail['sRegionName'].'-'.$bDetail['sZoneName'].'] '.$bDetail['sAddress'];
                $result['data']['sCircleLocation'] = isset($relatedDetail['sCircleLocation']) ? $relatedDetail['sCircleLocation'] : '';
                $result['data']['sLat'] = $bDetail['iLat'];
                $result['data']['sLon'] = $bDetail['iLng'];

                $advantages = explode(';',$relatedDetail['sAdvantageLabel']);
                if(!empty($advantages)) {
                    if(sizeof($advantages) >= 2) {
                        $result['data']['advantages'] = array($advantages[0], $advantages[1]);
                    }else {
                        $result['data']['advantages'] = array($advantages[0]);
                    }
                }else {
                    $result['data']['advantages'] = array();
                }

                $disAdvantages = explode(';',$relatedDetail['sRiskLabel']);;
                array_pop($disAdvantages);
                if(!empty($disAdvantages)) {
                    $result['data']['disAdvantages'] = array($disAdvantages[0]);
                }else {
                    $result['data']['disAdvantages'] = array();
                }

                $aChapter = Model_Evaluation::getEvaluationInfo($iUnitID, $this->isApp);
                $result['data']['comment'] = array(
                    'sFlagText' => Model_Loupan::getRecommendText($comflag),
                    'sFlagColor' => Model_Loupan::getRecommendColor($comflag),
                    'iFlagType' => Model_Loupan::getRecommend($comflag),
                    'sRate' => $bDetail['iTotalScore'],
                    'sRateRoomType' => $bDetail['iRoomTypeScore'],
                    'sRateDesign' => $bDetail['iDesignScore'],
                    'sRateHouse' => $bDetail['iHouseManageScore'],
                    'sRatePark' => $bDetail['iParkScore'],
                    'sRateLocation' => $bDetail['iLocationScore'],
                    'sCPRatio' => $bDetail['iCPRatioScore'],
                    'sCompletePageUrl' => $aChapter['sCompletePageUrl'],
                    'chapter' => $aChapter['chapter']
                );

                if( $aChapter['iEvaluateID'] ){
                    $result['data']['comment']['iEvaluateID'] = $aChapter['iEvaluateID'];
                }

                $result['data']['sAvatarHeadUrl'] = '';

                if($aChapter['analystID']){
                    $analystID = intval($aChapter['analystID']);
                    $analyst = Model_Analysts::getDetail($analystID);

                    if($analyst) {
                        $result['data']['sAvatarHeadUrl'] = empty($analyst['AnalystHead'])? Util_Uri::getDefaultImg(2) : Util_Uri::getCricViewURL($analyst['AnalystHead'], 120, 120, 0, 0, 1);
                    }
                }

                $result['data']['loanInfo'] = array();

                $rate = Yaf_G::getConf('rate',null,'mapi');
                $rate = $rate / 12;
                $totalPrice = empty($lowPrice) ? 0 : intval($lowPrice['total']);

                $firstPay = $totalPrice * 0.3;
                $firstPay = round($firstPay);
                //等额本息还款法:
                //每月月供额=〔贷款本金×月利率×(1＋月利率)＾还款月数〕÷〔(1＋月利率)＾还款月数-1〕
                //总利息=还款月数×每月月供额-贷款本金
                $loan = $totalPrice * 0.7;
                $payMonth = $loan * $rate * pow((1 + $rate), 240) / (pow((1 + $rate), 240) - 1);
                $payMonth = round($payMonth);

                $totalInterest = $payMonth * 240 - $loan;

                $result['data']['loanInfo']['sTotalPrice'] = ceil($totalPrice/10000);
                $result['data']['loanInfo']['sTotalPriceUnit'] = '万元';
                $result['data']['loanInfo']['sLoanInfo'] = '贷款'. ceil($loan/10000). '万，等额本息还款，按揭20年';
                $result['data']['loanInfo']['iFirstPay'] = ceil($firstPay/10000);
                $result['data']['loanInfo']['iInterest'] = ceil($totalInterest/10000);
                $result['data']['loanInfo']['iTotalLoan'] = ceil($loan/10000);

                $result['data']['loanInfo']['sFirstPay'] = ceil($firstPay/10000).'万';
                $result['data']['loanInfo']['sInterest'] = ceil($totalInterest/10000).'万';
                $result['data']['loanInfo']['sTotalLoan'] =ceil( $loan/10000).'万';

                $result['data']['loanInfo']['sMonthPay'] = $payMonth.'元' ;

                if(!empty($lowPrice)) {
                    $result['data']['loanInfo']['typeCode'] = $lowPrice['TypeCode'];
                    $result['data']['loanInfo']['typeName'] = $lowPrice['TypeName'];
                    $result['data']['loanInfo']['area'] = $lowPrice['Area'].'m²';
                    $result['data']['loanInfo']['blockNumber'] = $lowPrice['BlockNumber'];
                    $result['data']['loanInfo']['roomNumber'] = $lowPrice['RoomNumber'];
                }

                $result['data']['priceTrend'] = array();

                $result['data']['houseType'] = array();
                if(!empty($aRoomType)) {
                    $result['data']['houseType']['sTotalNum'] = sizeof($aRoomType). '种';
                    $result['data']['houseType']['list'] = array();

                    foreach($aRoomType as $type) {
                        $result['data']['houseType']['list'][] = array(
                            'sName' => $type['TypeCode']. ' '. $type['TypeName']. ' '. $type['Area']. 'm²',
                            'sTypeCode' => $type['TypeCode'],
                            'sTypeName' => $type['TypeName'],
                            'sArea' => $type['Area']. 'm²',
                            'sImgUrl' => empty($type['ImageCode']) ? Util_Uri::getDefaultImg(3) : Util_Uri::getCricViewURL($type['ImageCode'], 640, 420, 19, 4)
                        );
                    }
                }

                //$result['data']['nearByList'] = array();
                $result['data']['detailInfo'] = array();
                if(!empty($relatedDetail)) {
                    $result['data']['detailInfo']['sOpenTime'] = $bDetail['sOpenTime'];
                    $result['data']['detailInfo']['sDeliverTime'] = $bDetail['sDeliverTime'];
                    $result['data']['detailInfo']['iPlanCount'] = $relatedDetail['iPlanCount'];
                    $result['data']['detailInfo']['sAcreage'] = number_format($relatedDetail['iBuildArea']). 'm²';
                    $result['data']['detailInfo']['sEstateType'] = $relatedDetail['sHomeType'];
                    $result['data']['detailInfo']['sBuildType'] = $relatedDetail['sBuildType'];
                    $result['data']['detailInfo']['sFitmentDesign'] = $relatedDetail['sFitmentDesign'];
                    $result['data']['detailInfo']['iFitmentPrice'] = $relatedDetail['iFitmentPrice'];
                    $result['data']['detailInfo']['sGreenRate'] = $relatedDetail['sGreenRate'];
                    $result['data']['detailInfo']['iPlotRatio'] = $relatedDetail['iPlotRatio'];
                    $result['data']['detailInfo']['iParkCount'] = $relatedDetail['iParkCount'];
                    $result['data']['detailInfo']['sParking'] = $relatedDetail['sParking'];
                    $result['data']['detailInfo']['sManageCorp'] = $relatedDetail['sManageCorp'];
                    $result['data']['detailInfo']['sManageFee'] = $relatedDetail['sManageFee'];
                    $result['data']['detailInfo']['sName'] = $relatedDetail['sDeveloper'];
                    $result['data']['detailInfo']['sSaleAddress'] = $relatedDetail['sSaleAddress'];
                    $result['data']['detailInfo']['sSalePhone'] = $relatedDetail['sSalePhone'];
                }

                $result['data']['shoppingGuide'] = array();
                $aNews = Model_News::getNewsByBID($iUnitID, 0, 1, 1);
                if (!empty($aNews['aList'])) {
                    $city = Model_City::getCityByID(intval($aNews['aList'][0]['iCityID']));
                    $cityName = $city['sFullPinyin'];

                    $result['data']['shoppingGuide'] = [
                        'total' => $aNews['iTotal'],
                        'sTitle' => $aNews['aList'][0]['sShortTitle'],
                        'sText' => $aNews['aList'][0]['sAbstract'],
                        'sJumpUrl' => Util_Uri::getNewsDetailUrl($aNews['aList'][0]['iNewsID'], $cityName, 2)
                    ];
                }

                $result['data']['userRemark'] = array();
                $aRemark = Model_Remark::getListByBID($iBuildingID, 1, 1);
                if (!empty($aRemark['aList'])) {
                    $result['data']['userRemark'] = ['sTotal' => $aRemark['iTotal'].'条',
                        'sText' => $aRemark['aList'][0]['sContent']];
                }
            }
        }else {
            $result = array(
                'code' => 1,
                'msg' => '参数不全'
            );
        }

        return $this->showMsg($result, true);
    }

    /**
     * 获取网友点评
     */
    public function remarklistAction()
    {
        $result = array(
            'code' => 0,
            'msg' => 'ok',
            'data' => array()
        );
        $buildingID = intval($this->getParam('buildingID'));
        $iPage = intval($this->getParam('iPage'));
        $iRows = intval($this->getParam('iRows'));

        if(!empty($buildingID) && !empty($iPage) && !empty($iRows)) {
            $result['data'] = array(
                'iTotalNum' => 0,
                'iPage' => $iPage,
                'iRows' => $iRows,
                'list' => array()
            );

            $remarks = Model_Remark::getListByBID($buildingID, $iPage, $iRows);
            $result['data']['iTotalNum'] = $remarks['iTotal'];

            if(!empty($remarks['aList'])) {
                foreach($remarks['aList'] as $remark) {
                    $result['data']['list'][] = array(
                        'iRemarkID' => $remark['iAutoID'],
                        'sName' => $remark['sName'],
                        'sContent' => $remark['sContent'],
                        'sAvatorImgUrl' => empty($remark['sAvatorImgUrl']) ? Util_Uri::getDefaultImg(2) : Util_Uri::getCricViewURL($remark['sAvatorImgUrl'], 120, 120, 0, 0, 1),
                        'sTime' => date('Y-m-d H:i:s',$remark['iCreateTime'])
                    );
                }
            }
        }else {
            $result['code'] = 1;
            $result['msg'] = '参数不全';
        }

        return $this->showMsg($result, true);
    }

    /**
     * 提交点评
     */
    public function commitremarkAction()
    {
        $result = array('code' => 0, 'msg' => '提交成功', 'data' => array());
        $userID = intval($this->getParam('userID'));
        $buildingID = intval($this->getParam('buildingID'));
        $content = $this->getParam('content');

        if(!empty($buildingID) && !empty($content)) {
            $data = array(
                'sContent' => $content,
                'iBuildingID' => $buildingID,
                'iCreateTime' => time(),
                'iUpdateTime' => time()
            );
            if(!empty($userID)) {
                $data['iUserId'] = $userID;
            }

            $insert = Model_Remark::addData($data);
            if(!$insert) {
                $result['code'] = 1;
                $result['msg'] = '数据库错误';
            }
        }else {
            $result['code'] = 1;
            $result['msg'] = '参数不全';
        }

        return $this->showMsg($result, true);
    }

    /**
     * 楼盘导购列表
     */
    public function sellguidelistAction()
    {
        $result = array('code' =>0, 'msg' =>'ok', 'data' =>array());
        $buildingID = intval($this->getParam('buildingID'));
        $cityID = intval($this->getParam('cityID'));

        $iPage = intval($this->getParam('iPage'));
        $iRows = intval($this->getParam('iRows'));

        if(!empty($iPage) && !empty($iRows)) {
            $result['data']['iTotalNum'] = 0;
            $result['data']['iPage'] = $iPage;
            $result['data']['iRows'] = $iRows;
            $result['data']['list'] = array();

            if($buildingID) {
                $buildingID = Model_Loupan::BuildingIDConvert($buildingID);
                $buildingID = Model_Loupan::getUnitCricID($buildingID);
            }

            $aNews = Model_News::getNewsByBID($buildingID, $cityID, $iPage, $iRows);
            $result['data']['iTotalNum'] = $aNews['iTotal'];

            if(!empty($aNews['aList'])) {
                foreach($aNews['aList'] as $news) {
                    //$newsUrl = Yaf_G::getConf('touchweb','domain');
                    $newsUrl = '';
                    if ($news['iCityID'] > 0) {
                        $cityID = $news['iCityID'];
                    }
                    $city = Model_City::getDetail($cityID);
                    $cityFullPinyin = $city['sFullPinyin'];
                    $newsUrl .= Util_Uri::getNewsDetailUrl($news['iNewsID'], $cityFullPinyin, 2, $this->isApp);//'/h5/news/detail?iNewsID='. $news['iNewsID'];

                    $result['data']['list'][] = array(
                        'iNewsID' => $news['iNewsID'],
                        'sText' => $news['sTitle'],
                        'sAuthor' => $news['sAuthor'],
                        'sTime' => date('Y-m-d',$news['iPublishTime']),
                        'sImgUrl' =>Util_Uri::getNewsImgUrl($news['sImage'],213,160),
                        'sJumpToUrl' => $newsUrl
                    );
                }
            }
        }else {
            $result = array('code' =>1, 'msg' =>'参数不全');
        }

        return $this->showMsg($result, true);
    }

    /**
     * 楼盘图片列表
     */
    public function buildimagesAction()
    {
        $result = array(
            'code' =>0,
            'msg' =>'ok',
            'data' =>array(
                'xiaoguoUrl' => array(),
                'huxingUrl' => array(),
                'shijingUrl' => array(),
                'zongpingUrl' => array(),
                'zhoubianUrl' => array(),
            )
        );
        $buildingID = intval($this->getParam('buildingID'));

        if(!empty($buildingID)) {
            $aPic = Model_LoupanPic::getPicsByBID($buildingID);
            $buildingID = Model_Loupan::BuildingIDConvert($buildingID);
            $aHuxingPic = Model_CricRoomType::getRoomTypeByBID($buildingID);

            if(!empty($aPic)) {
                foreach($aPic as $pic) {
                    switch($pic['iPictureType']){
                        case 1:
                            $result['data']['shijingUrl'][] = Util_Uri::getCricViewURL($pic['sPictureCode'], 1, 1, 19, 4);
                            break;
                        case 2:
                            $result['data']['xiaoguoUrl'][] = Util_Uri::getCricViewURL($pic['sPictureCode'], 1, 1, 19, 4);
                            break;
                        case 3:
                            $result['data']['zongpingUrl'][] = Util_Uri::getCricViewURL($pic['sPictureCode'], 1, 1, 19, 4);
                            break;
                        case 5:
                            $result['data']['zhoubianUrl'][] = Util_Uri::getCricViewURL($pic['sPictureCode'], 1, 1, 19, 4);
                            break;
                        default:
                            break;
                    }
                }
            }

            if(!empty($aHuxingPic)) {
                foreach($aHuxingPic as $pic) {
                    if(empty($pic['ImageCode'])) {
                        continue;
                    }else {
                        $result['data']['huxingUrl'][] =  Util_Uri::getCricViewURL($pic['ImageCode'], 1, 1, 19, 4);
                    }
                }
            }
        }else {
            $result = array('code' =>1, 'msg' =>'参数不全');
        }

        return $this->showMsg($result, true);
    }

    /**
     * 获取指定类别楼盘图片
     * param $type 1实景图, 2效果图, 3总评图, 5周边图
     * $width
     * $height
     * $num 数量
     */
    public function getImageByTypeAction(){
        $buildingID = intval($this->getParam('buildingID'));
        $type = intval($this->getParam('type'));
        $width = $this->getParam('width');
        $height = $this->getParam('height');

        $result = array(
            'code' => 0,
            'msg' => 'ok',
            'data' => array()
        );

        if(!empty($buildingID) && !empty($type)) {
            $pics = Model_LoupanPic::getAll(array(
                'where' => array(
                    'iLoupanID' => $buildingID,
                    'iStatus' => 1,
                    'iPictureType' => $type
                )
            ));
            if(!empty($pics)) {
                foreach($pics as $pic) {
                    if(!empty($width) && !empty($height)) {
                        if($width >= 600 || $height >= 600) {
                            $result['data']['list'][] = Util_Uri::getCricViewURL($pic['sPictureCode'], $width, $height, 19, 4);
                        }else {
                            $result['data']['list'][] = Util_Uri::getCricViewURL($pic['sPictureCode'], $width, $height);
                        }
                    }else {
                        $result['data']['list'][] = $pic['sPictureCode'];
                    }
                    $result['data']['sRemark'][] = $pic['sRemark'];
                }
            }
        }else {
            $result = array('code' =>1, 'msg' =>'参数不全');
        }

        return $this->showMsg($result, true);
    }

    /**
     * 获取户型图
     * param $buildingID
     * $width
     * $height
     */
    public function gethximageAction(){
        $buildingID = intval($this->getParam('buildingID'));
        $width = $this->getParam('width');
        $height = $this->getParam('height');

        $result = array(
            'code' => 0,
            'msg' => 'ok',
            'data' => array()
        );

        if(!empty($buildingID)) {
            $buildingID = Model_Loupan::BuildingIDConvert($buildingID);
            $aHuxingPic = Model_CricRoomType::getRoomTypeByBID($buildingID);

            if(!empty($aHuxingPic)) {
                foreach($aHuxingPic as $pic) {
                    if(empty($pic['ImageCode'])) {
                        continue;
                    }else {
                        $room = array(
                            'typeName' => $pic['TypeName'],
                            'typeCode' => $pic['TypeCode'],
                            'area' => $pic['Area']
                        );

                        $room['advantage'] = array();
                        if(!empty($pic['AssessA'])) {
                            $advantage = Model_CricRoomAssessSpecific::getAll(array(
                                'where' => array(
                                    'ID IN' => $pic['AssessA']
                                )
                            ));

                            if(!empty($advantage)){
                                foreach($advantage as $a) {
                                    $room['advantage'][] = $a['Assess'];
                                }
                            }
                        }

                        $room['disadvantage'] = array();
                        if(!empty($pic['AssessB'])) {
                            $disadvantage = Model_CricRoomAssessSpecific::getAll(array(
                                'where' => array(
                                    'ID IN' => $pic['AssessB']
                                )
                            ));

                            if(!empty($disadvantage)){
                                foreach($disadvantage as $da) {
                                    $room['disadvantage'][] = $da['Assess'];
                                }
                            }
                        }


                        if(!empty($width) && !empty($height)) {
                            if($width >= 600 || $height >= 600) {
                                $room['sImageUrl'] = Util_Uri::getCricViewURL($pic['ImageCode'], $width, $height, 19, 4);
                            }else {
                                $room['sImageUrl'] = Util_Uri::getCricViewURL($pic['ImageCode'], $width, $height);
                            }
                        }else {
                            $room['sImageUrl'] = $pic['ImageCode'];
                        }

                        $result['data']['list'][] = $room;
                    }
                }
            }
        }else {
            $result = array('code' =>1, 'msg' =>'参数不全');
        }
        return $this->showMsg($result, true);
    }

    /**
     * 户型详情页
     */
    public function housetypedetailAction()
    {
        $result = array(
            'code' => 0,
            'msg' => 'ok',
            'data' => array(
                'list' => array()
            )
        );

        $buildingID = intval($this->getParam('buildingID'));
        if(!empty($buildingID)) {
            $buildingID = Model_Loupan::BuildingIDConvert($buildingID);
            $aHouseType = Model_CricRoomType::getRoomTypeByBID($buildingID);
            if(!empty($aHouseType)) {
                foreach($aHouseType as $type) {
                    $aAdvantages = array();
                    $aDisadvantages = array();

                    if(!empty($type['AssessA'])){
                        $advantages = Model_CricRoomAssessSpecific::getAssess($type['AssessA']);
                    }
                    if(!empty($type['AssessB'])){
                        $disadvantages = Model_CricRoomAssessSpecific::getAssess($type['AssessB']);
                    }

                    foreach($advantages as $advantage) {
                        $aAdvantages[] = $advantage['Assess'];
                    }

                    foreach($disadvantages as $disadvantage) {
                        $aDisadvantages[] = $disadvantage['Assess'];
                    }

                    $result['data']['list'][] = array(
                        'sTypeCode' => $type['TypeCode'],
                        'sName' => $type['TypeCode']. '/'. $type['TypeName']. '/'. $type['Area']. 'm²',
                        'sHouseType' => $type['TypeName'], // 户型
                        'sAcreage' => $type['Area']. 'm²',      // 面积
                        'sPrice' => ceil($type['MinTotalPrice']/10000). '-'. ceil($type['MaxTotalPrice']/10000). '万',      // 价格
                        'imageUrlList' => empty($type['ImageCode']) ? Util_Uri::getDefaultImg(4) : Util_Uri::getCricViewURL($type['ImageCode'], 640, 420, 19, 4),
                        'advantages' => $aAdvantages,      // 优势
                        'disadvantages' => $aDisadvantages     // 劣势
                    );
                }
            }
        }

        return $this->showMsg($result, true);
    }

    /**
     * 获取周边楼盘
     */
    public function getSurroundingAction()
    {
        $result = array(
            'code' => 0,
            'msg' => 'ok',
            'data' => array()
        );

        $buildingID = intval($this->getParam('buildingID'));
        $cityCode = $this->getParam('cityCode');
        $regionName = $this->getParam('regionName');
        $num = $this->getParam('num');

        if(!empty($buildingID) && !empty($cityCode) && !empty($regionName)) {
            $where = array(
                'sCityCode' => $cityCode,
                'sRegionName' => $regionName,
                'iAutoID <>' => $buildingID
            );
            $num = empty($num) ? 8 : $num;
            $buildings = Model_Loupan::getList($where, 1, 'iUpdateTime desc', $num, '', '', false);

            if(!empty($buildings['aList'])) {
                foreach($buildings['aList'] as $building) {
                    $result['data'][] = array(
                        'buildingID' => $building['iAutoID'],
                        'name' => $building['sName'],
                        'regionName' => $building['sRegionName'],
                        'price' => number_format($building['iBidingPrice']),
                        'image' => empty($building['sZongPinPic']) ? Util_Uri::getDefaultImg(3) : Util_Uri::getCricViewURL($building['sZongPinPic'], 640, 420, 19, 4)
                    );
                }
            }
        }else {
            $result = array(
                'code' => 1,
                'msg' => '参数不全'
            );
        }

        return $this->showMsg($result, true);
    }

    /**
     * 获取单元信息
     */
    public function getunitinfoAction()
    {
        $result = array(
            'code' => 0,
            'msg' => 'ok',
            'data' => array()
        );

        $buildingID = intval($this->getParam('buildingID'));
        $typeCode = $this->getParam('houseTypeCode');

        if(!empty($buildingID) && !empty($typeCode)){
            $homeID = Model_Loupan::getHomeID($buildingID);
            $buildingID = Model_Loupan::BuildingIDConvert($buildingID);
            $aRoom = Model_CricRoom::getRoomByType($buildingID, $typeCode);

            if(!empty($aRoom)) {
                $aBtR = array();

                foreach($aRoom as $room) {
                    $blockNum = $room['BlockNumber'];
                    if(!array_key_exists($blockNum, $aBtR)) {
                        $aBtR[$blockNum] = array();
                    }

                    $aBtR[$blockNum][] = array(
                        'iCodeID' => $room['RoomID'],
                        'sName' => $room['RoomNumber']
                    );
                }

                $huxing = $aRoom[0]['TypeName'];
                $huxingInfo = Model_CricRoomTypeRemaining::getRoomOfType($homeID, $huxing);
                $result['data']['sHouseLeft'] = empty($huxingInfo) ? 0 : $huxingInfo['HomeRemainingNum'];

                $result['data']['buildUnit'] = array();
                foreach ($aBtR as $block => $rooms) {
                    $troom = array();
                    foreach($rooms as $room) {
                        $troom[] = array(
                            'iCodeID' => $room['iCodeID'],
                            'sName' => $room['sName']
                        );
                    }

                    $result['data']['buildUnit'][] = array(
                        'sName' => $block,
                        'room' => $troom
                    );
                }
            }
        }else {
            $result = array(
                'code' => 1,
                'msg' => '参数不全'
            );
        }

        return $this->showMsg($result, true);
    }

    /**
     * 获取贷款信息
     */
    public function getloaninfoAction()
    {
        $buildingID = intval($this->getParam('buildingID'));
        $roomID = $this->getParam('roomID');

        $result = array(
            'code' => 0,
            'msg' => 'ok',
            'data' => array()
        );

        if(!empty($roomID) || true) {
            $room = Model_CricRoom::getPriceByID($roomID);
            $rate = Yaf_G::getConf('rate',null,'mapi');
            $rate = $rate / 12;
            $totalPrice = intval($room['TotalPrice']);

            $firstPay = $totalPrice * 0.3;
            $firstPay = round($firstPay);
            //等额本息还款法:
            //每月月供额=〔贷款本金×月利率×(1＋月利率)＾还款月数〕÷〔(1＋月利率)＾还款月数-1〕
            //总利息=还款月数×每月月供额-贷款本金
            $loan = $totalPrice * 0.7;
            $payMonth = $loan * $rate * pow((1 + $rate), 240) / (pow((1 + $rate), 240) - 1);
            $payMonth = round($payMonth);

            $totalInterest = $payMonth * 240 - $loan;

            $result['data']['sTotalPrice'] = ceil($totalPrice/10000);
            $result['data']['sTotalPriceUnit'] = '万元';
            $result['data']['sLoanInfo'] = '贷款'. ceil($loan/10000). '万，等额本息还款，按揭20年';
            $result['data']['iFirstPay'] = ceil($firstPay/10000);
            $result['data']['iInterest'] = ceil($totalInterest/10000);
            $result['data']['iTotalLoan'] = ceil($loan/10000);

            $result['data']['sFirstPay'] = ceil($firstPay/10000).'万';
            $result['data']['sInterest'] = ceil($totalInterest/10000).'万';
            $result['data']['sTotalLoan'] =ceil( $loan/10000).'万';

            $result['data']['sMonthPay'] = $payMonth.'元' ;
        }else {
            $result = array(
                'code' => 1,
                'msg' => '参数不全'
            );
        }

        return $this->showMsg($result, true);
    }

    public function genSearch() {
        $cityID = intval($this->getParam('cityID'));
        $iPage = intval($this->getParam('iPage'));
        $iRows = intval($this->getParam('iRows'));

        $regionID = $this->getParam('regionID');
        $priceID = intval($this->getParam('priceID'));
        $layoutID = addslashes($this->getParam('layoutID'));
        $featureID = $this->getParam('featureID');     //榜单
        $keyWord = addslashes($this->getParam('keyword')); //用户输入的楼盘名字或地址

        $sql = 'select *';
        if ($this->getParam('lat') && $this->getParam('lon')) {
            $lng = $this->getParam('lon');
            $lat = $this->getParam('lat');
            $sql .= ",(SQRT(POW((iLng-$lng)*102.83, 2) + POW((iLat-$lat)*111.71, 2))) distance";
        }

        $sql .= ' from t_loupan where iCityID ='. $cityID. ' and iStatus = 1 and iTotalScore > 0';

        if(!empty($regionID)) {
//            $region = Model_CricRegion::getDetail($regionID);
//            if(!empty($region)){
//                $sql .= " and sRegionName = '". $region['RegionName']. "'";
//            }

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

        if(!empty($keyWord)) {
            $sql .= " and(sName like '%". $keyWord. "%' or sAddress like '%". $keyWord. "%')";
        }

        if(!empty($featureID)) {
            $where = array(
                'where' => array(
                    'ListID' => $featureID,
                    'State' => 1
                )
            );
            $zhuntiDetail = Model_CricUnitZT0114Detail::getPair($where, 'ItemID', 'HomeID');

            if(!empty($zhuntiDetail)) {
                $aHomeID = array_values($zhuntiDetail);
                $sHomeID = implode("','", $aHomeID);

                $sql .= " and sHomeID IN ('". $sHomeID. "')";
            }
        }

        return $sql;
    }

    public function genFeatureSearch() {
        $cityID = intval($this->getParam('cityID'));
        $iPage = intval($this->getParam('iPage'));
        $iRows = intval($this->getParam('iRows'));

        $featureID = $this->getParam('featureID');     //榜单


        $sql = 'select * from t_loupan where iCityID ='. $cityID. ' and iStatus = 1';

        if(!empty($featureID)) {
            $where = array(
                'where' => array(
                    'ListID' => $featureID,
                    'State' => 1
                ),
                'order' => 'Rank ASC'
            );
            $zhuntiDetail = Model_CricUnitZT0114Detail::getPair($where, 'ItemID', 'HomeID');

            if(!empty($zhuntiDetail)) {
                $aHomeID = array_values($zhuntiDetail);
                $sHomeID = implode("','", $aHomeID);
                $sInSetHomeID = implode(",", $aHomeID);
                $sql .= " and sHomeID IN ('". $sHomeID. "') ORDER BY FIND_IN_SET(sHomeID, '$sInSetHomeID')";
                //echo $sql;
            }
        }

        return $sql;
    }

    /**
     * 获取楼盘分析列表
     */
    public function getanalystlistAction()
    {
        $buildingID = intval($this->getParam('buildingID'));

        $result = array(
            'code' => 0,
            'msg' => 'ok',
            'data' => array('list'=>array())
        );

        if(!empty($buildingID)) {
            $unitID = Model_Loupan::BuildingIDConvert($buildingID);
            if(!empty($unitID)) {
                $opnions = Model_CricAnalystsOpinion::getAnalystsOpinionByBID($unitID);

                if(!empty($opnions)) {
                    foreach($opnions as $opnion) {
                        $result['data']['list'][] = array(
                            'iAnalystID' => $opnion['aid'], // 分析师ID
                            'sAvatarImgUrl' => empty($opnion['bAnalystHead']) ? Util_Uri::getDefaultImg(2) : Util_Uri::getCricViewURL($opnion['bAnalystHead'], 120, 120, 0, 0, 1),   // 分析师头像URL
                            'sName' => $opnion['AnalystsName'],       // 分析师名
                            'sTitle' => $opnion['AnalystLevel'],// 分析师称谓
                            'sText' => $opnion['Opinion']  // 分析内容
                        );
                    }
                }
            }
        }else {
            $result = array(
                'code' => 1,
                'msg' => '参数不全'
            );
        }

        return $this->showMsg($result, true);
    }

    /**
     * 根据老表中的id获取新表中的楼盘ID
     * @return bool
     */
    public function getNewLouIDAction()
    {
        $sID = intval($this->getParam('id'));
        $result = array(
            'code' => 0,
            'msg' => 'ok',
            'data' => array('list'=>array())
        );
        if(!empty($sID)) {
            $unitID = Model_CricUnit::getNewIDByUnitID($sID);
            if ($unitID == 0) {
                $result['msg'] = '没有对应UnitID的楼盘';
                $result['code'] = 1;
            } elseif ($unitID == -1) {
                $result['msg'] = '新表中没有同步楼盘数据';
                $result['code'] = 1;
            } else {
                $result['data']['list']['LouID'] = $unitID;
            }
        }else {
            $result = array(
                'code' => 1,
                'msg' => '参数不全'
            );
        }

        return $this->showMsg($result, true);
    }


}