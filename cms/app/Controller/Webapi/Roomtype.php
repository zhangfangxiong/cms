<?php

/**
 * Created by PhpStorm.
 * User: cjj
 * Date: 2015/05/04
 * 新房API
 */
class Controller_Webapi_Roomtype extends Controller_Webapi_Base
{
    public function actionBefore()
    {

    }

    public function getRecommendedRoomTypesAction()
    {
        $unitID = $this->getParam('unitID');
        $result = Model_CricRoomType::getRecommendedRoomTypes($unitID);
        return $this->showMsg($result, true);
    }

}