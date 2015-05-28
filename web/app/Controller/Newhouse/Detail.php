<?php

/**
 * 新房详情
 * Date: 2015/2/2
 * author:cjj
 * Time: 13:27
 */
class Controller_Newhouse_Detail extends Controller_NewHouseBase
{

    // 楼盘首页
    public function IndexAction()
    {
        $this->unitInfo['unit']['EvaluationUrl'] = '';
        $this->assign('unit', $this->unitInfo['unit']);
        $this->assign('block', $this->unitInfo['block']);
        $this ->setShareUrl($this->unitInfo['unit']['RegionName'],$this->unitInfo['unit']['UnitName']);
        // 评级
        $this->assign('pingji', Model_Unit::getSuggest());
        // 剩余房源
        $roomTypeRemaining = Model_RoomTypeRemaining::getRoomTypeRemaining($this->unitInfo['unit']['HomeID']);
        $remainingRooms = Model_RoomTypeRemaining::getRemainingRooms($roomTypeRemaining);
        $this->assign('remainingRooms', $remainingRooms);
        $this->assign('roomTypeRemaining', $roomTypeRemaining);
        // 批次类型类型
        $groupBatchType = Model_BatchTypes::getGroupBatchType($this->unitInfo['batchType']);
        $this->assign('groupBatchType',$groupBatchType);
        $this->assign('batchType',Model_BatchTypes :: getBatchType($groupBatchType));
        // 房间信息
        $roomTypeGroup = Model_RoomType::groupRoomType($this->unitInfo['roomType']);
        $newRoomTypes = Model_RoomType::formatRoomType($this->unitInfo['roomType']);
        $rooms = Model_Room::getRoomsByUnitId($this->unitInfo['unit']['UnitID']);
        $blockExtension = Model_Block::getBlockExtension($this->unitInfo['block'],$rooms);
        $this->assign('rooms', $rooms);
        $this->assign('newRoomTypes', $newRoomTypes);
        $this->assign('roomTypeGroup', $roomTypeGroup);
        $this->assign('roomTypes', $this->unitInfo['roomType']);
        $this->assign('blockExtension', $blockExtension);

        // 楼盘优惠信息
        $unitSales = Model_Unit::getFilterUnitSales($this->unitInfo['unit']['UnitID']);
        $this->assign('unitSales', $unitSales);
        // 新评测报告数据 包含章节分类和数据
        $unitEvaluation = Model_UnitEvaluation::evaluationChapter($this->unitInfo['unit']['ID']);
        $this->assign('unitEvaluation', $unitEvaluation);
        // cric 的图片
        $cricUnitPictures = Model_UnitPictures::getUnitPictures($this->unitInfo['unit']['UnitID']);
        $this->assign('cricUnitPictures', $cricUnitPictures);
        // 旧版评测报告
        $this -> getOldEvaluation($this->unitInfo['unit']['ID'],$this->unitInfo['unit']['HomeID'],empty($unitEvaluation) ? 1:0);
        // 推荐户型
        if (empty($unitEvaluation)) {
            $recommendedRoomTypes = Model_RoomType::getRecommendedRoomTypes($this->unitInfo['unit']['UnitID']);
            $this->assign('recommendedRoomTypes', $recommendedRoomTypes);
        } else {
            $this->assign('recommendedRoomTypes', array());
        }
        // 区域信息
        $regionInfo = Model_City::getRegionInfo($this->sCurrCityCode,$this->unitInfo['unit']['RegionName']);
        $this->assign('regionInfo', $regionInfo);
        //
        $BatchBlock = Model_BatchTypes::getBatchTypeBlock($this->unitInfo['unit']['UnitID']);

        $this->assign('batchBlock', $BatchBlock);
        $BatchTypeFullNew = Model_BatchTypes::getBatchTypeNewList($this->unitInfo['unit']['HomeID']);
        $this->assign('batchTypeFullNew', $BatchTypeFullNew);
    }

    // 楼盘动态
    public function getUnitDynamicAction()
    {
        $iUnitId = intval($this->getParam('unitid'));
        $newsDynamic = Model_UnitNews::getUnitNewsFromLeju($iUnitId,$this->cityInfo,1,1,"{subtype@eq}3");
        return $this->showMsg(array('unitDynamic'=>$newsDynamic,'dataCode'=>1), true);
    }

    // 楼盘房间
    public function getUnitRoomsAction()
    {
        $result = Model_Room::getRoomsByUnitId($this->unitInfo['unit']['UnitID']);
        return $this->showMsg($result, true);
    }

    // 推荐楼盘
    public function getRecommendedUnitAction()
    {
        $homeId = $this->getParam('homeId');
        $result = Model_Unit::getRecommendedUnit($this->sCurrCityCode,$homeId);
        return $this->showMsg($result, true);
    }


    // 房间价格
    public function getRoomPriceAction() {
        $blockId = $this->getParam('blockId');
        $roomId = $this->getParam('roomId');
        $result = Model_Room::getRoomPriceInfoByRoomId($blockId,$roomId);
        if (empty($result)) {
            return $this->showMsg(array('dataCode'=> 1), true);
        }
        $result['dataCode'] = 0;
        $result['Price'] = number_format($result['Price']);
        $result['TotalPrice'] = floor($result['TotalPrice']/10000);
        return $this->showMsg($result, true);
    }

    // 开发商说
    public function addDeveloperInfoAction()
    {
        $param['DeveloperName'] = urldecode($this->getParam('developerName'));
        $param['Comment'] = urldecode($this->getParam('comment'));
        $param['ContactName'] = urldecode($this->getParam('contactName'));
        $param['LogoPicture'] = $this->getParam('logoPic');
        $param['ContactPhone'] = $this->getParam('contactPhone');
        $param['UnitId'] = $this->unitInfo['unit']['UnitID'];
        $param['UnitName'] = $this->unitInfo['unit']['UnitName'];
        $result = Sdk_Cms_Newhouse::addDeveloperInfo($param);
        return $this->showMsg(array('code'=> $result['status']>=1 ?1:0), $result['status']);
    }

    public function setShareUrl($RegionName,$unitName)
    {
        $arr[$this->cityInfo['CityName']] = Model_City::getCityUrl($this->cityInfo['CityCode']);
        if (!empty($RegionName)) {
            $arr[$RegionName] = Model_City::getCityUrl($this->cityInfo['CityCode'])."?r=".urlencode($RegionName);
        }
        $arr[$unitName] = '';
        $this->generateBreadcrumbs($arr);
    }

    public function getOldEvaluation($iUnitId,$homeID,$newEvalFlag)
    {
         $totalCount = 0;
         $cricTotalCount = 0;
         if ($newEvalFlag == 1) {  // 无最新cms的评测报告
             $reportDataDic = Model_UnitEvaluation::getGetEvaluationReportFormLeju($iUnitId,$this->cityInfo);
             if (isset($reportDataDic['total'])) {
                 $totalCount = $reportDataDic['total'];
             }
             if ($totalCount>=1) {
                 $this->assign('EvaluationReportData', $reportDataDic);
             } else {
                 $homeNewReport = Model_HomeNewReport::getHomeNewReport($homeID);
                 if (!empty($homeNewReport)) {
                     $cricTotalCount = sizeof($homeNewReport);
                 }
                 $this->assign('HomeNewReport', $homeNewReport);
             }
         }

        $this->assign('totalCount', $totalCount);
        $this->assign('cricTotalCount', $cricTotalCount);
    }
}