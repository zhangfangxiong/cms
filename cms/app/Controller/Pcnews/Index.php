<?php

class Controller_Pcnews_Index extends Controller_News_Index
{
    protected function _getTypeID()
    {
        return Model_News::EVALUATION_NEWS;
    }

    protected function _getTypeCategory()
    {
        return Model_Category::CATEGORY_EVALUATION_NEWS;
    }

    protected function _getTypeTag()
    {
        return Model_Tag::TAG_EVALUATION_NEWS;
    }

    protected function _assignUrl()
    {
        $this->assign('sListUrl', '/pcnews/');
        $this->assign('sAddUrl', '/pcnews/add/');
        $this->assign('sEditUrl', '/pcnews/edit/');
        $this->assign('sDelUrl', '/pcnews/del/');
        $this->assign('sPublishUrl', '/pcnews/publish/');
        $this->assign('sOffUrl', '/pcnews/off/');
    }

}