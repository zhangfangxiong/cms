<?php

/**
 * Created by PhpStorm.
 * User: len
 * Date: 2015/1/27
 * Time: 11:17
 */
class Controller_API_News extends Controller_Api_Base
{

    public function lprankAction ()
    {
        $sMonth = $this->getParam('sMonth');
        $sCity = $this->getParam('sCity');
        $aCity = Model_City::getCityByFullPinyin($sCity);
        if (empty($aCity)) {
            return $this->showMsg('', false); // 城市不存在
        }

        $aLprank = Model_Lprank::getByCity($aCity['iCityID'], $sMonth);
        $aRet = array(
            'aMNews' => $aLprank['aMNews'],
            'aQNews' => $aLprank['aQNews']
        );
        return $this->showMsg($aRet, true);
    }

    /**
     * 资讯列表
     * param iPublishStatus 是否发布
     * param iTagID 标签id
     * param page 页数
     */
    public function newsListAction ()
    {
        $aParam['iPublishStatus'] = intval($this->getParam('iPublishStatus'));
        $aParam['iTagID'] = intval($this->getParam('iTagID'));
        $aParam['iETime'] = date("Y-m-d H:i:s", time()); // 可延迟发布，前端只显示发布时间小于当前时间的
        $iCityID = intval($this->getParam('iCityID'));
        $aParam['iCityID'] = [$iCityID, 0];

        $iPage = intval($this->getParam('page'));
        $sOrder = 'iPublishTime DESC';

        $aList = Model_News::getPageList($aParam, $iPage, $sOrder);
        return $this->showMsg($aList, true);
    }

    /**
     * 相关文章
     * param iNewsID 资讯id
     * param iTagID tagid,多个id以逗号
     * param pageSize 返回数据条数
     */
    public function relatedNewsAction ()
    {
        $iNewsID = intval($this->getParam('iNewsID'));
        $iTagID = $this->getParam('iTagID');
        $pageSize = intval($this->getParam('pageSize'));
        $iCityID = intval($this->getParam('iCityID'));

        if (empty($pageSize)) {
            $pageSize = 6;
        }

        $aList = Model_News::getRelatedNews($iCityID, $iNewsID, $iTagID, $pageSize);
        return $this->showMsg($aList, true);
    }

    /**
     * 资讯详情
     * param id资讯id
     */
    public function newsDetailAction ()
    {
        $aNews = array();
        $iNewsID = intval($this->getParam('id'));
        if ($iNewsID) {
            $aNews = Model_News::getDetail($iNewsID);

            //转换city
            $aCity = !empty($aNews['iCityID']) ? Model_City::getCityByID($aNews['iCityID']) : [];
            $aNews['sCityName'] = isset($aCity['sCityName']) ? $aCity['sCityName'] : '';

            // 楼盘名字替换链接
            Model_News::changeNewsLouName($aNews);
        }
        return $this->showMsg($aNews, true);
    }

    /**
     * 修改文章中带楼盘名的内容
     */
    public function changeNewsLouNameAction ()
    {
        $detail = [];
        $detail['iNewsID'] = $this->getParam('iNewsID') ? intval($this->getParam('iNewsID')) : 0;
        $detail['iCityID'] = $this->getParam('iCityID');
        $detail['iCategoryID'] = $this->getParam('iCategoryID');
        $detail['sTitle'] = $this->getParam('sTitle');
        $detail['sShortTitle'] = $this->getParam('sShortTitle');
        $detail['sImage'] = $this->getParam('sImage');
        $detail['sKeyword'] = $this->getParam('sKeyword');
        $detail['sAbstract'] = $this->getParam('sAbstract');
        $detail['sContent'] = $this->getParam('sContent');
        $detail['sLoupanID'] = $this->getParam('sLoupanID');
        $detail['sTag'] = $this->getParam('sTag');
        $detail['iAuthorID'] = $this->getParam('iAuthorID');
        $detail['iVisitNum'] = 0;
        $detail['sAuthor'] = $this->getParam('sAuthor');
        $detail['iShareTimes'] = 0;
        $detail['iPublishTime'] = strtotime($this->getParam('iPublishTime'));
        $detail['sMedia'] = strtotime($this->getParam('sMedia'));
        if (! empty($detail) && $detail['sContent']) {
            // 楼盘名字替换链接
            Model_News::changeNewsLouName($detail);
        }
        return $this->showMsg($detail, true);
    }

    /**
     * 今日热点
     * param pageSize 返回数据条数
     */
    public function newsHotTodayAction ()
    {
        $len = intval($this->getParam('len'));
        $iCityID = intval($this->getParam('iCityID'));

        $aList = $len ? Model_News::getDayHotNewsList($iCityID, $len) : Model_News::getDayHotNewsList($iCityID);
        $aReturn = array();
        if (! empty($aList)) {
            $iKey = 'dayHot';
            $aReturn = $this->dataAss($aList, $iKey);
        }
        if (! empty($aReturn)) {
            return $this->showMsg($aReturn, true);
        }
        return $this->showMsg("请求失败", false);
    }

    /**
     * 新闻推荐
     * param pageSize 返回数据条数
     */
    public function topShareAction ()
    {
        $len = intval($this->getParam('len'));
        $iCityID = intval($this->getParam('iCityID'));

        $aList = $len ? Model_News::getDayShareNewsList($iCityID, $len) : Model_News::getDayShareNewsList($iCityID);
        $aReturn = array();
        if (! empty($aList)) {
            $iKey = 'dayShare';
            $aReturn = $this->dataAss($aList, $iKey);
        }
        if (! empty($aReturn)) {
            return $this->showMsg($aReturn, true);
        }
        return $this->showMsg("请求失败", false);
    }

    private function dataAss ($aList, $iKey)
    {
        if (isset($aList['redis'])) {
            if (! empty($aList['redis'])) {
                foreach ($aList['redis'] as $key => $value) {
                    $aTmp = Model_News::getNewsByID($key);
                    $aTmp[$iKey] = $value;
                    $aReturn[] = $aTmp;
                }
            }
            if (! empty($aList['mysql'])) {
                foreach ($aList['mysql'] as $key => $value) {
                    $aTmp = Model_News::getNewsByID($key);
                    $aTmp[$iKey] = $value;
                    $aReturn[] = $aTmp;
                }
            }
        } else {
            foreach ($aList as $key => $value) {
                $aTmp = Model_News::getNewsByID($key);
                $aTmp[$iKey] = $value;
                $aReturn[] = $aTmp;
            }
        }
        return $aReturn;
    }

    /**
     * 楼盘资讯
     */
    public function unitNewsAction ()
    {
        $aParam['iPublishStatus'] = intval($this->getParam('iPublishStatus'));
        $aParam['iLoupanID'] = intval($this->getParam('iLoupanID'));
        $aParam['iETime'] = date("Y-m-d H:i:s", time()); // 可延迟发布，前端只显示发布时间小于当前时间的
        $iPage = intval($this->getParam('page'));
        $sOrder = 'iPublishTime DESC';
        $iPageSize = intval($this->getParam('pageSize'));
        $aList = Model_News::getPageList($aParam, $iPage, $sOrder, $iPageSize);
        return $this->showMsg($aList, true);
    }
}