<?php

/**
 * Created by PhpStorm.
 * User: ddc
 * Date: 2015/4/29
 * 房价点评新房API
 */
class Controller_API_Xfcity extends Controller_Api_Base
{
    public function actionBefore ()
    {

    }
    /**
     * 根据城市code获取价格区间
     */
    public function getCityPriceSectionAction(){
        $cityCode = $this->getParam('CityCode');

        if(empty($cityCode)){
            return null;
        }
        $aList = Model_CricCity::getPriceSection($cityCode);
        return $this->showMsg($aList, true);
    }

    /**
     * 根据城市code获取城市所属区域
     */
    public function getCityRegionListAction(){
        $cityCode = $this->getParam('CityCode');

        if(empty($cityCode)){
            return null;
        }
        $aList = Model_CricRegion::getCityRegion($cityCode);
        return $this->showMsg($aList, true);
    }

    /**
     * 根据城市code获取城市所属小区首字母
     */
    public function getFirstLettersListAction(){
        $cityCode = $this->getParam('CityCode');

        if(empty($cityCode)){
            return null;
        }
        $aList = Model_CricUnit::getFirstLetters($cityCode);
        return $this->showMsg($aList, true);
    }

    /**
     * 根据城市code获取城市所属小区首字母
     */
    public function getLinkAction(){
        $cityCode = $this->getParam('CityCode');
        if(empty($cityCode)){
            return null;
        }
        $aList = Model_CricCityLinks::getLink($cityCode);
        return $this->showMsg($aList, true);
    }

    /*
     * 获取城市400电话
     */
    public function getCity400Action()
    {
        $cityName = $this->getParam('cityName');
        $regionName = $this->getParam('regionName');
        $result = Model_CricCityPhoneNumber::getCity400($cityName,$regionName);
        return $this->showMsg($result, true);
    }
}