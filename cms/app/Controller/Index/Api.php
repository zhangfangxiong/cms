<?php

class Controller_Index_Api extends Controller_Base
{

    protected $bCheckLogin = false;

    /**
     * 楼盘AutoComplate
     */
    public function unitAction ()
    {
        $sCityCode = $this->getParam('sCityCode');
        $sKey = $this->getParam('sKey');
        $aData = Model_CricUnit::searchAutoComplete($sCityCode, $sKey);
        $this->showMsg($aData, true);
    }
    
    /**
     * 作者
     */
    public function  authorAction(){
        $sKey = $this->getParam('sKey');
        $aData = Model_author::searchAutoComplate( $sKey);
        $this->showMsg($aData, true);
    }
}