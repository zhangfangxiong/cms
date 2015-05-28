<?php

/**
 * Created by PhpStorm.
 * User: len
 * Date: 2015/1/27
 * Time: 11:17
 */
class Controller_Ajax_Index extends Controller_Ajax_Base
{
    protected $bCheckLogin = false;


    /**
     * 楼盘AutoComplate
     */
    public function unitAction()
    {
        $sCityCode = $this->getParam('sCityCode');
        $sKey = $this->getParam('sKey');
        $aData = Model_CricUnit::searchAutoComplete($sCityCode, $sKey, $this->iCurrCityID);
        return $this->showMsg($aData, true);
    }

    /**
     * 城市AutoComplete
     */
    public function cityAction()
    {
        $sKey = $this->getParam('sKey');
        $aData = Model_City::searchAutoComplete($sKey);
        return $this->showMsg($aData, true);
    }

    /**
     * 文章作者AutoComplete
     */
    public function authorAction()
    {
        $sKey = $this->getParam('sKey');
        $iTypeID = $this->getParam('iTypeID');
        $aData = Model_Author::searchAutoComplete($sKey, $iTypeID);
        return $this->showMsg($aData, true);
    }

    /**
     * 文章标签AutoComplete
     */
    public function tagAction()
    {
        $sKey = $this->getParam('sKey');
        $iTypeID = $this->getParam('iTypeID');
        $iCityID = $this->getParam('iCityID');
        $aData = Model_Tag::searchAutoComplete($iTypeID, $iCityID, $sKey);
        return $this->showMsg($aData, true);
    }

    public function newsAction()
    {
        $sKey = $this->getParam('sKey');
        $iCityID = $this->getParam('iCityID');
        $aData = Model_News::searchAutoComplete($sKey, $iCityID);
        return $this->showMsg($aData, true);
    }

    public function metroAction()
    {
        $iCityID = intval($this->getParam('iCityID'));
        $aMetroList = Model_Metro::getAll(
            [
                'where' => [
                    'iCityID' => $iCityID,
                    'iStatus' => 1
                ]
            ]
        );
        return $this->showMsg($aMetroList, true);
    }

    public function metrostationAction()
    {
        $iMetroID = intval($this->getParam('iMetroID'));
        $aStationList = Model_Metro::getMetroStation($iMetroID);
        return $this->showMsg($aStationList, true);
    }

    public function busAction()
    {
        $sKey = $this->getParam('sKey');
        $iCityID = $this->getParam('iCityID');
        $aData = Model_Bus::searchAutoComplete($sKey, $iCityID);
        $this->showMsg($aData, true);
    }

    public function stationsAction()
    {
        $iBusID = $this->getParam('iBusId');
        $aData = Model_BusStation::getStationsByBid($iBusID);
        $this->showMsg($aData, true);
    }
}