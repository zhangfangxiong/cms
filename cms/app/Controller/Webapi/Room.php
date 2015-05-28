<?php

/**
 * Created by PhpStorm.
 * User: cjj
 * Date: 2015/05/04
 * 新房API
 */
class Controller_Webapi_Room extends Controller_Webapi_Base
{
    public function actionBefore()
    {

    }


    // 获取小区所有房间
    public function getRoomsByUnitIdAction()
    {
        $unitId = addslashes($this->getParam('unitID'));
        $result = Model_CricRoom::getRoomsByUnitId($unitId);
        return $this->showMsg($result, true);
    }

    public function getRoomPriceInfoByRoomIdAction()
    {
        $blockId = addslashes($this->getParam('blockId'));
        $roomId = addslashes($this->getParam('roomId'));
        $result = Model_CricRoom::getRoomPriceInfoByRoomId($blockId,$roomId);
        return $this->showMsg($result, true);
    }

}