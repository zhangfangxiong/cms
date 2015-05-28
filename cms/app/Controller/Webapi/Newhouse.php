<?php

/**
 * Created by PhpStorm.
 * User: cjj
 * Date: 2015/05/04
 * 新房API
 */
class Controller_Webapi_Newhouse extends Controller_Webapi_Base
{
      public function actionBefore ()
      {

      }
    // 新房搜索
    public function searchAction ()
    {
        $searchParam = $this->getParam('searchParam');
        $cityCode = $this->getParam('cityCode');
        $pageSize = intval($this -> getParam('pageSize'));
        $result = Model_CricUnit::search($searchParam, $cityCode,($searchParam['pg']-1)*$pageSize,$pageSize);
        return $this->showMsg($result, true);
    }

    // 分析师评楼
    public function unitRecommendedAction ()
    {
        $cityCode = $this->getParam('cityCode');
        $result = Model_CricUnitRecommended::unitRecommendedList($cityCode);
        return $this->showMsg($result, true);
    }


    // 搜索提示
    public function searchSuggestAction() {

    }

    // 楼盘相关信息
    public function detailAction()
    {
        $unitID = $this->getParam('unitID');
        $reuslt['unit'] = Model_CricUnit::getLoupanByID($unitID); // 楼盘基本信息
        if (empty($reuslt['unit'])) {
            return $this->showMsg(null, false);
        }
        $reuslt['roomType'] = Model_CricRoomType::getRoomTypeByUnitID($reuslt['unit']['UnitID']); // 房型数据
        $reuslt['block'] = Model_CricBlock::getBlockByUnitID($reuslt['unit']['UnitID']); // 楼栋
        $reuslt['batchType'] = Model_CricBatchTypeFull::getBatchTypeFull($reuslt['unit']['UnitID']); // 批次信息
        return $this->showMsg($reuslt, true);
    }

    // 最新批次信息 ,返回的是最新批次的一组批次数据。
    public function newestBatchTypesAction()
    {
        $unitID = $this->getParam('unitID');
        $reuslt = Model_CricBatchTypeFull::getNewestBatchTypes($unitID);
        return $this->showMsg($reuslt, true);
    }


    // 楼盘优惠信息
    public function unitSalesAction()
    {
        $unitID = $this->getParam('unitID');
        $result = Model_CricUnitSale::getUnitSale($unitID);
        return $this->showMsg($result, true);
    }

    // cric 楼盘图片
    public function unitPicturesAction()
    {
        $unitID = $this->getParam('unitID');
        $result = Model_CricUnitPicture::getUnitPictures($unitID);
        return $this->showMsg($result, true);
    }

    //价目表(房间)
    public function getRoomByUnitIdAction()
    {
        $unitID = $this->getParam('unitID');
        if(empty($unitID)){
            return null;
        }
        $result = Model_CricRoom::getRoomByUnitId($unitID);
        return $this->showMsg($result, true);
    }

    //价目表(户型)
    public function getRoomTypeAction()
    {
        $unitID = $this->getParam('unitID');
        if(empty($unitID)){
            return null;
        }
        $result = Model_CricRoomType::getRoomType($unitID);
        return $this->showMsg($result, true);
    }

    //组装房价指导数据
    public function blockExtensionAction()
    {
        $unitID = $this->getParam('unitID');
        if(empty($unitID)){
            return null;
        }
        $result = Model_CricBlock::getBlockRoomInfo($unitID);
        return $this->showMsg($result, true);
    }

    //房贷计算
    public function getRoomAction()
    {
        $BlockID = $this->getParam('BlockID');
        $RoomID = $this->getParam('RoomID');
        if(empty($BlockID) && empty($RoomID)){
            return null;
        }
        $result = Model_CricRoom::getRoom($BlockID,$RoomID);
        return $this->showMsg($result, true);
    }

    //在售房源(block表)
    public function getBlockByUnitIDAction()
    {
        $unitID = $this->getParam('unitID');
        $type = $this->getParam('type');
        if(empty($unitID) && !empty($type)){
            return null;
        }
        $result = Model_CricBlock::getBlock($unitID,$type);
        return $this->showMsg($result, true);
    }

    //在售房源(unit表)
    public function getByUnitIDAction(){
        $unitID = $this->getParam('unitID');
        if(empty($unitID)){
            return null;
        }
        $result = Model_CricUnit::getByUnitID($unitID);
        return $this->showMsg($result, true);
    }
    public function batchTypeBlockAction()
    {
        $unitID = addslashes($this->getParam('unitID'));
        $result = Model_CricBatchTypeBlock::getBatchTypeBlockByUID($unitID);
        return $this->showMsg($result, true);
    }

    public function  batchTypeNewListAction()
    {
        $homeID = addslashes($this->getParam('homeID'));
        Model_CricBatchTypeFullNew::GetBatchTypeNewList($homeID);
    }

    //获取优惠
    public function getUnitSaleDataAction()
    {
        $unitID = $this->getParam('unitID');
        if(empty($unitID)){
            return null;
        }
        $result = Model_CricUnitSale::getUnitSaleData($unitID);
        return $this->showMsg($result, true);
    }

    //房价点评网（价目表->房价走势）
    public function getBatchTypeFullDataAction()
    {
        $unitID = $this->getParam('unitID');
        $where = $this->getParam('where');
        if(empty($unitID)){
            return null;
        }
        $result = Model_CricBatchTypeFull::getBatchTypeFullData($unitID,$where);
        return $this->showMsg($result, true);
    }

    //房价点评网（价目表->房价走势）
    public function getPriceTrendDataAction()
    {
        $unitID = $this->getParam('unitID');
        $homeType = $this->getParam('homeType');
        if (empty($unitID)) {
            return null;
        }
        $result = Model_CricBatchTypeFull::getPriceTrendData($unitID, $homeType);
        return $this->showMsg($result, true);
    }

    // 推荐楼盘
    public function getRecommendedUnitAction()
    {
        $cityCode =  addslashes($this->getParam('cityCode'));
        $homeId = addslashes($this->getParam('homeId'));
        $result = Model_CricUnit::getRecommendedUnit($cityCode,$homeId);
        return $this->showMsg($result, true);
    }

    // 开发商说
    public function addDeveloperInfoAction()
    {
        $param = $this->getParam('addParam');
        $rs = Model_CricDeveloper::addDeveloperInfo($param);
        return $this->showMsg($rs, $rs);
    }
}