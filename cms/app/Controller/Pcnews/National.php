<?php

class Controller_Pcnews_National extends Controller_Pcnews_Index
{

    protected function _getCityID()
    {
        return 0;
    }

    public function actionAfter()
    {
        parent::actionAfter();
        $this->_assignUrl();
    }

    protected function _assignUrl()
    {
        $this->assign('sListUrl', '/pcnews/national/');
        $this->assign('sAddUrl', '/pcnews/national/add/');
        $this->assign('sEditUrl', '/pcnews/national/edit/');
        $this->assign('sDelUrl', '/pcnews/national/del/');
        $this->assign('sPublishUrl', '/pcnews/national/publish/');
        $this->assign('sOffUrl', '/pcnews/national/off/');
    }

}