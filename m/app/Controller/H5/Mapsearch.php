<?php

/**
 * Created by PhpStorm.
 * Author: wangsufei
 * CreateTime: 2015/4/28 14:44
 * Description: Xfsearch.php
 */
class Controller_H5_Mapsearch extends Controller_Base {

    /**
     * 执行动作之前
     */
    public function actionBefore() {
        parent::actionBefore();
        $this->setFrame('touchweb.phtml');
    }

    /**
     * 执行Action后的操作
     * @see Yaf_Controller::actionAfter()
     */
    public function actionAfter() {
        parent::actionAfter();
    }

    /**
     * 地图找房
     */
    public function indexAction() {
        $aCurrCity = $this->getCurrCity();
        $this->assign('aCurrCity', $aCurrCity);
        $this->aMeta['title'] .= '地图找房';
    }
     /**
     * Ajax获取数据
     */
    public function mapajaxAction() {
        $aCurrCity = $this->getCurrCity();
        $aParam['cityID'] = $aCurrCity["iCodeID"];
        $aParam['zoomLevel'] = addslashes($this->getParam('zoomLevel'));
        $aParam['minLat'] = addslashes($this->getParam('minLat'));
        $aParam['minLon'] = addslashes($this->getParam('minLon'));
        $aParam['maxLat'] = addslashes($this->getParam('maxLat'));
        $aParam['maxLon'] = addslashes($this->getParam('maxLon'));
        $this->assign('aCurrCity', $aCurrCity);
        $aData = Sdk_Cms_Mapsearch::city($aParam);
        if (!$aData['status']) {
            return $this->showMsg($aData['data']['msg'], false);
        } else {
            return $this->showMsg($aData, true);
        }
    }

}
