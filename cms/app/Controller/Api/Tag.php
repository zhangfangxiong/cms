<?php

/**
 * Created by PhpStorm.
 * User: len
 * Date: 2015/1/27
 * Time: 11:17
 */
class Controller_API_Tag extends Controller_Api_Base
{
    /**
     * 取得热门标签
     * param iTypeID类型id
     * param orderBy 以哪个字段排序
     * param pageSize 返回数据条数
     */
    public function tagsAction()
    {
        $iTypeID = intval($this->getParam('iTypeID'));
        $orderBy = $this->getParam('orderBy');
        $orderBy = empty($orderBy) ? 'iNewsNum' : $orderBy;
        $pageSize = intval($this->getParam('pageSize'));
        $iCityID = intval($this->getParam('iCityID'));
        if(empty($pageSize))
        {
            $pageSize = 6;
        }

        $aList = Model_Tag::getHotTag($iCityID, $iTypeID, $orderBy, $pageSize);
        return $this->showMsg($aList, true);
    }

    /**
     *根据tagid取得tag名称
     * param iStatus
     * param iTagID tagid,多个id以逗号
     */
    public function getTagAction()
    {
        $iTagID = $this->getParam('iTagID');
        $iStatus = intval($this->getParam('iStatus'));
        $aList = Model_Tag::getTagByIDs($iTagID, $iStatus);
        return $this->showMsg($aList, true);
    }

}