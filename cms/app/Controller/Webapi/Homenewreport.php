<?php

/**
 * Created by PhpStorm.
 * User: cjj
 * Date: 2015/05/04
 * cric 的 新房评测
 */
class Controller_Webapi_Homenewreport extends Controller_Webapi_Base
{
    public function actionBefore()
    {

    }

    public function getHomenewreportAction()
    {
        $homeId = $this->getParam('homeId');
        $result = Model_CricHomeNewReport::getHomeNewReport($homeId);
        return $this->showMsg($result, true);
    }

}