<?php

class Controller_Pcnews_Tag extends Controller_News_Tag
{

    protected function _getTypeID()
    {
        return Model_Tag::TAG_EVALUATION_NEWS;
    }


    protected function _assignUrl()
    {
        $this->assign('sListUrl', '/pcnews/tag/');
        $this->assign('sAddUrl', '/pcnews/tag/add/');
        $this->assign('sEditUrl', '/pcnews/tag/edit/');
        $this->assign('sDelUrl', '/pcnews/tag/del/');
    }
}