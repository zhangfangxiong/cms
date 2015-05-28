<?php

/**
 * Created by PhpStorm.
 * User: cjj
 * Date: 2015/05/04
 * 新房API
 */
class Controller_Webapi_Roomtyperemaining extends Controller_Webapi_Base
{
    public function actionBefore()
    {

    }

    public function getRoomTypeRemainingAction()
    {
        $homeId = $this->getParam('homeId');
        $result = Model_CricRoomTypeRemaining::getRemainingByHomeID($homeId);
        return $this->showMsg($result, true);
    }

}