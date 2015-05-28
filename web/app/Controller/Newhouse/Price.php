<?php

/**
 * 房价点评网价目表
 * Date: 2015/5/19
 * author:ddc
 * Time: 13:27
 */
class Controller_Newhouse_Price extends Controller_NewHouseBase
{
    //价目表
    public function indexAction()
    {
        //参数条件
        $roomTypeStr = $this->getParam('rt');
        $areaRangeStr = $this->getParam('ar');
        $blockIdStr = $this->getParam('bl');
        $roomIdStr = $this->getParam('ro');
        $this->assign('roomTypeStr', isset($roomTypeStr)?$roomTypeStr:'');
        $this->assign('areaRangeStr', isset($areaRangeStr)?$areaRangeStr:'');
        $this->assign('blockIdStr', isset($blockIdStr)?$blockIdStr:'');
        $this->assign('roomIdStr', isset($roomIdStr)?$roomIdStr:'');
        $this->assign('unitID', $this->unitInfo['unit']['ID']); //传入楼盘id用于房贷计算
        $this->assign('UnitID', $this->unitInfo['unit']['UnitID']); //用于房价走势图
        //房价指导功能
        $room = Sdk_Cms_Newhouse::getHouseType($this->unitInfo['unit']['UnitID']);
        $roomType = Sdk_Cms_Newhouse::getRoomType($this->unitInfo['unit']['UnitID']);
        $roomTypeGroup = Model_RoomType::formatRoomTyepData($roomType['data']);
        $blockExtension = Sdk_Cms_Newhouse::blockExtension($this->unitInfo['unit']['UnitID']);
        $this->assign('romm', $room);
        $this->assign('roomType', $roomType);  //前端房贷计算器使用
        $this->assign('roomTypeGroup', $roomTypeGroup);  //用来前端在售房源显示
        $this->assign('blockExtension', $blockExtension);
        //在售房源功能
        $block = Sdk_Cms_Newhouse::getBlock($this->unitInfo['unit']['UnitID'],1);
        $unit = Sdk_Cms_Newhouse::getUnit($this->unitInfo['unit']['UnitID']);
        $blockList = Sdk_Cms_Newhouse::getBlock($this->unitInfo['unit']['UnitID'],2);
        $this->assign('blocks', isset($block['data'][0])?$block['data'][0]:'');
        $this->assign('unit', $unit['data']);
        $this->assign('blockList', $blockList['data']);
        // 批次类型类型
        $groupBatchType = Model_BatchTypes::getGroupBatchType($this->unitInfo['batchType']);
        $this->assign('groupBatchType',$groupBatchType);
        $this->assign('batchType',Model_BatchTypes :: getBatchType($groupBatchType));
        $this->assign('batchTypeList', $this->unitInfo['batchType']);//判断是否有别墅
        // 剩余房源
        $roomTypeRemaining = Model_RoomTypeRemaining::getRoomTypeRemaining($this->unitInfo['unit']['HomeID']);
        $remainingRooms = Model_RoomTypeRemaining::getRemainingRooms($roomTypeRemaining);
        $this->assign('remainingRooms', $remainingRooms);
        $this->assign('roomTypeRemaining', $roomTypeRemaining);
        //房价走势
        $unitSale = Sdk_Cms_Newhouse::getUnitSaleData($this->unitInfo['unit']['UnitID']);
        $this->assign('unitSale', $unitSale['data']);
        // 区域信息
        $regionInfo = Model_City::getRegionInfo($this->sCurrCityCode,$this->unitInfo['unit']['RegionName']);
        $this->assign('regionInfo', $regionInfo);
        //价目表左侧资讯
        $id = Sdk_Cms_Xfsearch::getUnitIdById($this->unitInfo['unit']['ID'],2); //获取loupan表中的ID
        $news = Sdk_Cms_Newhouse::getUnitNews($id['data']['iAutoID'],$this->iCurrCityID);
        $this->assign('news', $news['data']['list']);
//        echo '<pre>';
//        print_r($news);
//        die;

    }

    //房贷计算
    public function getRoomPriceAction()
    {
        $BlockId = $this->getParam('BlockId');
        $RoomID = $this->getParam('RoomID');
        $unitID = $this->getParam('unitID');
        $id = Sdk_Cms_Xfsearch::getUnitIdById($unitID,2);  //获取loupan表中的ID
        if(isset($id['data']['iAutoID']) && !empty($id['data']['iAutoID'])) {
            $data = Sdk_Cms_Newhouse::getRoom($BlockId, $RoomID);
            $price = Sdk_Cms_Newhouse::getRoomPrice($id['data']['iAutoID'], $RoomID); //调用房贷计算需要传入loupan id
        }
        $result = array('RoomData'=>isset($data['data'][0])?$data['data'][0]:'','Fangdai'=>isset($price['data'])?$price['data']:'');
        return $this->showMsg($result, true);
    }


    //房价走势
    public function getPriceTrendDataAction()
    {
//        $UnitID = $this->getParam('UnitID');
//        print_r($UnitID);
        //$UnitID = '05EF3AB3-37B8-4A9C-AC0D-5CE4D55FEB3B';
        $UnitID = 'F932CD88-5A6F-41D1-9652-A44C50B2C939';
        $where = "UnitId = '$UnitID' ORDER BY PushDate DESC,rowDate DESC";
        $batchTypeFulls = Sdk_Cms_Newhouse::getBatchTypeFullData($UnitID,$where);


        $extraData = '';
        $priceList = '';

        $houstList = array();
        $cottageList = array();
        $apartmentList = array();

        $houseExtraData = array();
        $cottageExtraData = array();
        $apartmentExtraData = array();

        $temCottagePrice = null;
        $temCottageGuidePrice = 0.0;
        $temHousePrice = null;
        $temHouseGuidePrice = 0.0;
        $temApartmentPrice = null;
        $temApartmentGuidePrice = 0.0;

        $isShowHousePrice = 0;  //0：不显示；1：显示
        $isShowCottagePrice = 0; //0：不显示；1：显示
        $isShowApartmentPrice = 0; //0：不显示；1：显示

        $curMonth = date('Y-m',time()).'-01';


        if(!empty($batchTypeFulls['data'])) {
            foreach ($batchTypeFulls['data'] as $item) {
                if ($item['HomeType'] == '别墅') {
                    $item['HomeType'] = '别墅';
                } else {
                    if ($item['HomeType'] == '酒店式公寓') {
                        $item['HomeType'] = '酒店式公寓';
                    } else {
                        $item['HomeType'] = '普通住宅';
                    }
                }
            }

            for ($i = 11; $i >= 0; $i--) {
                $statMonth = date('Y-m-d 00:00:00', strtotime("$curMonth-1 month"));
                $nextMonth = date('Y-m-d 00:00:00', strtotime("$statMonth+1 month"));

                $where = "UnitId = '$UnitID' AND HomeType = '别墅' ORDER BY PushDate DESC,rowDate DESC";
                $temCottageList = Sdk_Cms_Newhouse::getBatchTypeFullData($UnitID, $where);
//                echo '<pre>';
//                print_r($temCottageList);
//                die;

                $where = "UnitId = '$UnitID' AND MarketDate >= '$statMonth' AND MarketDate < '$nextMonth' AND HomeType = '普通住宅' ORDER BY PushDate DESC,rowDate DESC";
                $temHouseList = Sdk_Cms_Newhouse::getBatchTypeFullData($UnitID, $where);

                $where = "UnitId = '$UnitID' AND MarketDate >= '$statMonth' AND MarketDate < '$nextMonth' AND HomeType = '酒店式公寓' ORDER BY PushDate DESC,rowDate DESC";
                $temApartmentList = Sdk_Cms_Newhouse::getBatchTypeFullData($UnitID, $where);

                $cottageLen = count($temCottageList);
                $houseLen = count($temHouseList);
                $apartmentLen = count($temApartmentList);

                $sumSalePrice = 0;
                $sumGuidePrice = 0;

                if ($cottageLen > 0) {
                    foreach ($temCottageList['data'] as $key => $value) {
                        $sumSalePrice += $value['SalePrice'];
                        $avgSalePrice = round($sumSalePrice / $cottageLen, 0);
                        $temCottagePrice = strval($avgSalePrice);

                        $sumGuidePrice += $value['CricPrice'];
                        $avgGuidePirce = round($sumGuidePrice / $cottageLen, 0);
                        $temCottageGuidePrice = strval($avgGuidePirce);
                    }
                }
                array_push($cottageList, $temCottagePrice);
                $cottageExtraData[]['CricPrice'] = $temCottageGuidePrice;
                $cottageExtraData[]['SalePrice'] = $temCottagePrice;

                if ($houseLen > 0) {
                    foreach ($temHouseList['data'] as $key => $value) {
                        $sumSalePrice += $value['SalePrice'];
                        $avgSalePrice = round($sumSalePrice / $cottageLen, 0);
                        $temHousePrice = strval($avgSalePrice);

                        $sumGuidePrice += $value['CricPrice'];
                        $avgGuidePirce = round($sumGuidePrice / $cottageLen, 0);
                        $temHouseGuidePrice = strval($avgGuidePirce);
                    }
                }
                array_push($houstList, $temHousePrice);
                $houseExtraData[]['CricPrice'] = $temHouseGuidePrice;
                $houseExtraData[]['SalePrice'] = $temHousePrice;

                if ($apartmentLen > 0) {
                    foreach ($temApartmentList['data'] as $key => $value) {
                        $sumSalePrice += $value['SalePrice'];
                        $avgSalePrice = round($sumSalePrice / $cottageLen, 0);
                        $temApartmentPrice = strval($avgSalePrice);

                        $sumGuidePrice += $value['CricPrice'];
                        $avgGuidePirce = round($sumGuidePrice / $cottageLen, 0);
                        $temApartmentGuidePrice = strval($avgGuidePirce);
                    }
                }
                array_push($apartmentList, $temApartmentPrice);
                $apartmentExtraData[]['CricPrice'] = $temApartmentGuidePrice;
                $apartmentExtraData[]['SalePrice'] = $temApartmentPrice;
            }
            $priceList = array(
                '别墅' => implode(',', $cottageList),
                '住宅' => implode(',', $houstList),
                '公寓' => implode(',', $apartmentList)
            );
            $extraData = array('别墅' => $cottageExtraData, '住宅' => $houseExtraData, '公寓' => $apartmentExtraData);
            if ($temHousePrice != null) {
                $isShowHousePrice = 1;
            }

            if ($temCottagePrice != null) {
                $isShowCottagePrice = 1;
            }

            if ($temApartmentPrice != null) {
                $isShowApartmentPrice = 1;
            }
            $result = array(
                'PriceList' => $priceList,
                'ExtraData' => $extraData,
                'isShowHousePrice' => $isShowHousePrice,
                'isShowCottagePrice' => $isShowCottagePrice,
                'isShowApartmentPrice' => $isShowApartmentPrice
            );
            $this->showMsg($result, true);
        }
    }

    //成交情况
    public function dynamicSalesCountListAction(){
        //获取当前城市
        $CityName = $this->sCurrCityName;
        if(isset($CityName)) {
            $where = "AND b.State = 1 AND a.CityName = '$CityName' ORDER BY a.DealDate DESC, CAST(a.Counts AS SIGNED) DESC";
            $allUnitDeals = Sdk_Cms_Newhouse::getUnitDealByCity($where);
            echo '<pre>';
            print_r($allUnitDeals);
            die;
        }
    }
}