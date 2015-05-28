<?php

/**
 * Created by PhpStorm.
 * User: chasel
 * Date: 2015/4/23
 * Time: 11:33
 */
class Controller_H5_Chart extends Controller_Base {

    /**
     * 执行动作之前
     */
    public function actionBefore() {
        parent::actionBefore();
        $this->setFrame('newsh5frame.phtml');
    }

    /**
     * 图表
     * @return
     */
    public function indexAction() {
        $buildingID = $this->getParam('buildingID');

        $data = Sdk_Cms_Loupan::getChart($buildingID);

        $current = date("Y-n", time());
//var_dump($data['data']);exit;
        $this->assign('data', $data['data']);
    }

}
