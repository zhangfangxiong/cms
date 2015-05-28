<?php

/**
 * Created by PhpStorm.
 * User: xiaoyao
 * Date: 2015/1/27
 * Time: 9:29
 */
class Controller_Pcnews_Author extends Controller_News_Author
{

    protected function _getTypeID()
    {
        return Model_Author::AUTHOR_EVALUATION_NEWS;
    }

    protected function _assignUrl()
    {
        $this->assign('sListUrl', '/pcnews/author/');
        $this->assign('sAddUrl', '/pcnews/author/add/');
        $this->assign('sDelUrl', '/pcnews/author/del/');
        $this->assign('sEditUrl', '/pcnews/author/edit/');
    }
}