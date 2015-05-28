<?php

/**
 * User: xuchuyuan
 * Date: 2015/4/6
 */
class Controller_API_Article extends Controller_Api_Base {

    const PAGE = 1;
	const PAGESIZE = 20;

	/**
     * 执行Action前执行
     * @see Yaf_Controller::actionBefore()
     */
    public function actionBefore () {

    }

    /**
     * 资讯列表
     * param page 页数
     */
    public function newsListAction ()
    {
    	// 传入参数：城市id，当前页数，每页条数，时间
        $iCityID = intval($this->getParam('iCityID'));
        if ($iCityID) {
            $aParam['iCityID'] = [$iCityID, 0];
        }
        //$aParam['iCityID'] = intval($this->getParam('iCityID')) ? intval($this->getParam('iCityID')) : '' ;
    	$iPage = intval($this->getParam('page')) ? intval($this->getParam('page')) : self::PAGE;
    	$iPagesize = intval($this->getParam('pagesize')) ? intval($this->getParam('pagesize')) : self::PAGESIZE;
        $aParam['iETime'] = (!empty($this->getParam('iETime'))
        					&& (time() > strtotime($this->getParam('iETime'))) )
        					? $this->getParam('iETime')
        					: date("Y-m-d H:i:s", time());
        $aParam['iPublishStatus'] = 1;
        $sOrder = 'iPublishTime desc';

        $aList = Model_News::getPageList($aParam, $iPage, $sOrder, $iPagesize);
        foreach ($aList['aList'] as $key => $value) {
        	$aCity = Model_City::getCityByID($value['iCityID']);
        	$aList['aList'][$key]['sCityName'] = isset($aCity['sCityName']) ? $aCity['sCityName'] : '';
        	$aList['aList'][$key]['iPublishTime'] = date('Y-m-d H:i:s', $value['iPublishTime']);
        }
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
            // 楼盘名字替换链接
            Model_News::changeNewsLouName($aNews);
        }

        if($aNews){
        	//转换city，楼盘，标签，时间戳
        	$aCity = !empty($aNews['iCityID']) ? Model_City::getCityByID($aNews['iCityID']) : [];
        	$aNews['sCityName'] = isset($aCity['sCityName']) ? $aCity['sCityName'] : '';

        	$aLoupan = !empty($aNews['sLoupanID']) ? Model_CricUnit::getLoupanNames($aNews['sLoupanID']) : [];
        	$aNews['sLoupanName'] = isset($aLoupan[$aNews['sLoupanID']]) ? $aLoupan[$aNews['sLoupanID']] : '';

        	$aTag = !empty($aNews['sTag']) ? Model_Tag::getTagByIDs($aNews['sTag']) : [];
        	$aNews['sTagName'] = isset($aTag) ? $aTag : '';

        	$aNews['iPublishTime'] = date('Y-m-d H:i:s', $aNews['iPublishTime']);
        	$aNews['iCreateTime'] = date('Y-m-d H:i:s', $aNews['iCreateTime']);
        	$aNews['iUpdateTime'] = date('Y-m-d H:i:s', $aNews['iUpdateTime']);
        }
        return $this->showMsg($aNews, true);
    }


}