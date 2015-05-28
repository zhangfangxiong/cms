<?php

class Controller_Map_Index extends Controller_Base
{
    /**
     * 地图找房
     */
    public function IndexAction() {
        $cityInfo = Model_City::getCityInfo($this->sCurrCityCode);
        $this->assign('cityInfo', $cityInfo);
        $aParam['cityID'] = $cityInfo["ID"];
        $aParam['zoomLevel'] = 12;
        $aData = Sdk_Cms_Mapsearch::pccity($aParam);
        $this->assign('mapfoot',1);
        $this->assign('mapData', json_encode($aData));
    }
     /**
     * Ajax获取数据
     */
    public function MapajaxAction() {
        $cityInfo = Model_City::getCityInfo($this->sCurrCityCode);
        $aParam['cityID'] = $cityInfo["ID"];
        $aParam['zoomLevel'] = addslashes($this->getParam('zoomLevel'));
        $aParam['minLat'] = addslashes($this->getParam('minLat'));
        $aParam['minLon'] = addslashes($this->getParam('minLon'));
        $aParam['maxLat'] = addslashes($this->getParam('maxLat'));
        $aParam['maxLon'] = addslashes($this->getParam('maxLon'));
        $this->assign('cityInfo', $cityInfo);
        $aData = Sdk_Cms_Mapsearch::pccity($aParam);
        if (!$aData['status']) {
            return $this->showMsg($aData['data']['msg'], false);
        } else {
            return $this->showMsg($aData, true);
        }
    }
}