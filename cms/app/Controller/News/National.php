<?php

class Controller_News_National extends Controller_News_Index
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
        $this->assign('sListUrl', '/news/national/');
        $this->assign('sAddUrl', '/news/national/add/');
        $this->assign('sEditUrl', '/news/national/edit/');
        $this->assign('sDelUrl', '/news/national/del/');
        $this->assign('sPublishUrl', '/news/national/publish/');
        $this->assign('sOffUrl', '/news/national/off/');
    }

}