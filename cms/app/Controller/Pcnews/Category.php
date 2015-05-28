<?php

class Controller_pcnews_Category extends Controller_News_Category
{

    protected function _getTypeID()
    {
        return Model_Category::CATEGORY_EVALUATION_NEWS;
    }

    protected function _assignUrl()
    {
        $this->assign('sListUrl', '/pcnews/category/');
        $this->assign('sAddUrl', '/pcnews/category/add/');
        $this->assign('sEditUrl', '/pcnews/category/edit/');
        $this->assign('sDelUrl', '/pcnews/category/del/');
    }
}