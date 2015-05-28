<?php

/**
 * Created by PhpStorm.
 * User: len
 * Date: 2015/1/27
 * Time: 11:17
 */
class Controller_API_City extends Controller_Api_Base
{
    /**
     * 城市列表
     */
    public function cityListAction()
    {
        $aWhere = array(
            'iStatus' => 1
        );
        $aList = Model_City::getAll(['where'=>$aWhere]);

        return $this->showMsg($aList, true);
    }

    public function getCityByCodeAction()
    {
        $sCityCode = $this->getParam('sCityCode');
        $aWhere = array(
            'iStatus' => 1,
            'sFullPinyin' => $sCityCode
        );

        $aList = Model_City::getRow(['where' => $aWhere]);
        return $this->showMsg($aList, true);
    }

    public function getCityByNameAction()
    {
        $sCityName = $this->getParam('sCityName');
        $aList = Model_City::getCityByName($sCityName);
        return $this->showMsg($aList, true);
    }

    public function getCityByIdAction()
    {
        $sCityID = $this->getParam('cityID');
        $aWhere = array(
            'iStatus' => 1,
            'iCityID in ' => implode(',',$sCityID)
        );
        $aList = Model_City::getAll(['where' => $aWhere]);
        $arr = array();
        if (!empty($aList)) {
            foreach($aList as $item) {
                $arr[$item['iCityID']]['sCityName'] = $item['sCityName'];
                $arr[$item['iCityID']]['sPinyin'] = $item['sPinyin'];
                $arr[$item['iCityID']]['sFullPinyin'] = $item['sFullPinyin'];
            }
        }
        return $this->showMsg($arr, true);
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
}