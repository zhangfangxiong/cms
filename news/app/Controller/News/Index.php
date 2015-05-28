<?php

class Controller_News_Index extends Controller_Base
{

    protected $aAdv = [];
    protected $aBanner = [];

    /**
     * 资讯列表首页
     */
    public function indexAction()
    {
        $this->_setAdv();
        $this->_setBanner();
        $this->_checkIndexData();
    }

    /**
     * 资讯列表
     *
     */
    public function listAction()
    {
        $currentPage = intval($this->getParam('page'));
        $currentPage = max($currentPage, 1);

        $tagId = intval($this->getParam('tagId'));
        $currentTag = array();
        if(!empty($tagId)) {
            $currentTag = $tags = Sdk_Cms_Tag::getTag($tagId);
            $currentTag = $currentTag['data'];
        }

        $data = Sdk_Cms_News::getNews($currentPage, $this->iCurrCityID, $tagId);
        $total = $data['data']['iTotal'];
        $pager = Util_Page::getFrontPage(
            $total, $currentPage, 20, '/{city}/news/list/tag/{tagId}/{page}/', [ 'city' => $this->sCurrCityCode, 'tagId' => $tagId,]
        );

        $aList = $data['data']['aList'];

        $newses = array();

        if(!empty($aList)) {
            foreach($aList as $news){
                $tags = null;
                if(!empty($news['sTag'])) {
                    $tags = Sdk_Cms_Tag::getTag($news['sTag'], 1);
                    $tags = $tags['data'];
                }

                $desc = null;
                if(empty($news['sAbstract'])) {
                    $desc = strip_tags($news['sContent']);
                    if (mb_strlen($desc) > 100){
                        $desc = mb_substr($desc, 0, 100);
                        $desc .= '......  ';
                    }
                }else {
                    $desc = $news['sAbstract'];
                    if(mb_strlen($desc) > 100){
                        $desc = mb_substr($desc, 0, 100);
                        $desc .= '......  ';
                    }
                }

                $iShareTimes = ceil($news['iVisitNum'] / 10);
                $iShareTimes += $news['iShareTimes'];
                $newses[] = array(
                    'iNewsID' => $news['iNewsID'],
                    'sTitle' => $news['sTitle'],
                    'sShortTitle' => $news['sShortTitle'],
                    'sImage' => $news['sImage'],
                    'sContent' => $news['sContent'],
                    'sAbstract' => $desc,
                    'sAuthor' => $news['sAuthor'],
                    'sTag' => $tags,
                    'iPublishTime' => date('Y-m-d', $news['iPublishTime']),
                    'iShareTimes' => $iShareTimes
                );
            }
        }

        $aList = Sdk_Cms_News::getHot($this->iCurrCityID);
        $aList = $aList['data'];

        if(!empty($aList)) {
            foreach($aList as $news){
                $hotNews[] = array(
                    'iNewsID' => $news['iNewsID'],
                    'sShortTitle' => mb_substr($news['sShortTitle'], 0 ,15)
                );
            }
        }


        $buildings = Sdk_Cms_News::getBuilds($this->sCurrCityCode);
        $buildings = $buildings['data'];

        $this->assign('newses', $newses);
        $this->assign('hotNews', $hotNews);
        $this->assign('pager', $pager);
        $this->assign('buildings', $buildings);

        if(!empty($currentTag)){
            reset($currentTag);
            $currentTag = current($currentTag);
            $this->generateBreadcrumbs(array("资讯"=>'/'.$this->sCurrCityCode.'/news/index', $currentTag=>0));
            $this->aMeta['title'] = '新闻列表 - '.$currentTag. ' - '. $this->aMeta['title'];
            $this->assign('currentTagName', $currentTag);
        }


    }

    public function detailAction()
    {
        $iNewsID = intval($this->getParam('iNewsID'));

        //更新文章点击数
        $guid = Util_Cookie::get('guid');
        $key = $guid. $iNewsID;
        $memocache = Util_Common::getCache();
        if(!$memocache->get($key)) {
            Sdk_Cms_News::updVisit($iNewsID);
            $memocache->set($key, true, 10);
        }


        $data = Sdk_Cms_News::getDetail($iNewsID);
        $detail = $data['data'];
        if ($data['status'] == false ) {
            throw new Exception('该文章不存在，请联系管理员！');
            return false;
        }

        if(!empty($detail)) {
            if($detail['iStatus'] != 1 || $detail['iPublishStatus'] != 1 || $detail['iPublishTime'] >= time()) {
                throw new Exception('该文章不存在，请联系管理员！');
                return false;
            }
        }


        $iShareTimes = ceil($detail['iVisitNum'] / 10);
        $iShareTimes += $detail['iShareTimes'];
        $detail['iShareTimes'] = $iShareTimes;

        $related = array();
        if( !empty($detail['sTag'])) {
            $data = Sdk_Cms_News::getRelated($iNewsID, $detail['sTag'],$this->iCurrCityID);
            if(!empty($data['data']) && is_array($data['data'])) {
                foreach($data['data'] as $news){
                    $related[] = array(
                        'iNewsID' => $news['iNewsID'],
                        'sShortTitle' => mb_substr($news['sShortTitle'], 0 ,15),
                        'iPublishTime' => date('Y-m-d', $news['iPublishTime'])
                    );
                }
            }
        }

        $data = Sdk_Cms_News::getTopShare($this->iCurrCityID, 5);
        $topShare = array();

        if(!empty($data['data'])){
            foreach($data['data'] as $news){
                $topShare[] = array(
                    'iNewsID' => $news['iNewsID'],
                    'sShortTitle' => mb_substr($news['sShortTitle'], 0 ,15),
                    'iPublishTime' => date('Y-m-d', $news['iPublishTime'])
                );
            }
        }

        $buildings = Sdk_Cms_News::getSpecialBuilds($iNewsID);
        $buildings = $buildings['data'];

        $tags = null;
        if( !empty($detail['sTag'])) {
            $tags = Sdk_Cms_Tag::getTag($detail['sTag']);
            $tags = $tags['data'];
        }

        $this->assign('detail', $detail);
        $this->assign('related', $related);
        $this->assign('topShare', $topShare);
        $this->assign('buildings', $buildings);
        $this->assign('tags', $tags);

        $title = $detail['sShortTitle'];
        $this->generateBreadcrumbs(array("资讯"=>'/'.$this->sCurrCityCode.'/news/index', $title=>0));
        $this->aMeta['title'] = $detail['sShortTitle']. ' - '. $this->aMeta['title'];
    }

    public function actionAfter()
    {
        parent::actionAfter();
        $this->_assignUrl();
    }

    protected function _assignUrl()
    {
        $this->assign('nIndex', '/'.$this->sCurrCityCode.'/news/index/');
        $this->assign('nDetailUrl', '/'.$this->sCurrCityCode.'/news/detail/');
        $this->assign('nListUrl', '/'.$this->sCurrCityCode.'/news/list/tag/');
    }


    protected function _checkPreviewAvaild()
    {
        $time = $this->getParam('time');
        $iCurrentTime = time();

        if (empty($time) || ($iCurrentTime - $time) > 600) {
            $this->_404();
            return false;
        }
        return true;
    }
    public function previewdetailAction()
    {
        $iNewsID = $this->getParam('id', 0);
        if ($iNewsID > 0) {
            $data = Sdk_Cms_News::getDetail($iNewsID);
            $detail = $data['data'];
            if ($data['status'] == false ) {
                throw new Exception('该文章不存在，请联系管理员！');
                return false;
            }
        } else {
            if (!$this->_checkPreviewAvaild()) {
                return false;
            }
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
            $detail['sMedia'] = $this->getParam('sMedia');
        }

        $detailNews = Sdk_Cms_News::changeNewsLouName($detail)['data'];
        $detail['sContent'] = isset($detailNews['sContent']) ? $detailNews['sContent'] : '';
        
        $related = array();
        if( !empty($detail['sTag'])) {
            $data = Sdk_Cms_News::getRelated($detail['iNewsID'], $detail['sTag'], $this->iCurrCityID);
            if (!empty($data['data'])) {
                foreach($data['data'] as $news){
                    $related[] = array(
                        'iNewsID' => $news['iNewsID'],
                        'sShortTitle' => mb_substr($news['sShortTitle'], 0 ,15),
                        'iPublishTime' => date('Y-m-d', $news['iPublishTime'])
                    );
                }
            }
        }
        
        $data = Sdk_Cms_News::getTopShare($this->iCurrCityID, 5);
        $topShare = array();
        if (!empty($data['data'])) {
            foreach($data['data'] as $news){
                $topShare[] = array(
                    'iNewsID' => $news['iNewsID'],
                    'sShortTitle' => mb_substr($news['sShortTitle'], 0 ,15),
                    'iPublishTime' => date('Y-m-d', $news['iPublishTime'])
                );
            }
        }
        
        $title = $detail['sShortTitle'];
        $this->generateBreadcrumbs(array("资讯"=>'/'.$this->sCurrCityCode.'/news/index', $title=>0));
        $this->aMeta['title'] = $detail['sShortTitle']. ' - '. $this->aMeta['title'];

        $this->assign('detail', $detail);
        $this->assign('related', $related);
        $this->assign('topShare', $topShare);
        $this->actionAfter();
        $this->assign('_script', 'News/Index/detail.phtml');
        $sBody = $this->getView()->render($this->_frame);
        $this->getResponse()->setBody($sBody);
        return false;
    }

    public function previewcarouselAction()
    {
        if (!$this->_checkPreviewAvaild()) {
            return false;
        }
        $iNewsID = $this->getParam('iNewsID');
        $sImage = $this->getParam('sImage');
        $sDesc = $this->getParam('sDesc');
        $iPublishTime = $this->getParam('iPublishTime');
        $aNews = Sdk_Cms_News::getDetail($iNewsID);
        if (!empty($aNews['data']) && !empty($aNews['data']['sTag'])) {
            $aTag = Sdk_Cms_Tag::getTag($aNews['data']['sTag']);
            if (!empty($aTag['data'])) {
                $aNews['data']['aTag'] = $aTag['data'];
            }
        }
        //$this->_setBanner();
        $data = [];
        $data['sContent'] = $iNewsID;
        $data['sImage'] = $sImage;
        $data['sDesc'] = $sDesc;
        $data['iPublishTime'] = $iPublishTime;
        $data['aNews'] = $aNews['data'];
        $this->aBanner[] = $data;
        $this->_setAdv();
        $this->_checkIndexData();

        $this->assign('_script', 'News/Index/index.phtml');
        $this->actionAfter();
        $sBody = $this->getView()->render($this->_frame);
        $this->getResponse()->setBody($sBody);
        return false;
    }

    public function previewadvAction()
    {
        if (!$this->_checkPreviewAvaild()) {
            return false;
        }

        $sUrl = $this->getParam('sUrl');
        $sImage = $this->getParam('sImage');
        $iPublishTime = $this->getParam('iPublishTime');
        $sDesc = $this->getParam('sDesc');
        $data = [];
        $data['sUrl'] = $sUrl;
        $data['sImage'] = $sImage;
        $data['iPublishTime'] = $iPublishTime;
        $data['sDesc'] = $sDesc;

        $this->aAdv = $data;
        $this->_setBanner();
        $this->_checkIndexData();

        $this->assign('_script', 'News/Index/index.phtml');
        $this->actionAfter();
        $sBody = $this->getView()->render($this->_frame);
        $this->getResponse()->setBody($sBody);
        return false;
    }

    protected function _setBanner()
    {
        $banners = Sdk_Cms_News::getBanner($this->iCurrCityID);
        $this->aBanner = $banners['data'];
    }

    protected function _setAdv()
    {
        $advs = Sdk_Cms_News::getAdv($this->iCurrCityID);
        $this->aAdv = $advs['data'];
    }

    protected function _checkIndexData()
    {
        $currentPage = intval($this->getParam('page'));
        $currentPage = max($currentPage, 1);

        $tagId = intval($this->getParam('tagId'));

        $data = Sdk_Cms_News::getNews($currentPage, $this->iCurrCityID, $tagId);
        $total = $data['data']['iTotal'];
        $pager = Util_Page::getFrontPage(
            $total, $currentPage, 20, '/{city}/news/index/{page}/', ['city' => $this->sCurrCityCode]
        );
        $aList = $data['data']['aList'];

        $newses = array();
        if(!empty($aList)) {
            foreach($aList as $news){
                $tags = null;
                if(!empty($news['sTag'])) {
                    $tags = Sdk_Cms_Tag::getTag($news['sTag'], 1);
                    $tags = $tags['data'];
                }

                $desc = null;
                if(empty($news['sAbstract'])) {
                    $desc = strip_tags($news['sContent']);
                    if(mb_strlen($desc) > 100){
                        $desc = mb_substr($desc, 0, 100);
                        $desc .= '......  ';
                    }
                }else {
                    $desc = $news['sAbstract'];
                    if(mb_strlen($desc) > 100){
                        $desc = mb_substr($desc, 0, 100);
                        $desc .= '......  ';
                    }
                }
                $iShareTimes = ceil($news['iVisitNum'] / 10);
                $iShareTimes += $news['iShareTimes'];
                $newses[] = array(
                    'iNewsID' => $news['iNewsID'],
                    'sShortTitle' => $news['sShortTitle'],
                    'sTitle' => $news['sTitle'],
                    'sImage' => $news['sImage'],
                    'sContent' => $news['sContent'],
                    'sAbstract' => $desc,
                    'sAuthor' => $news['sAuthor'],
                    'sTag' => $tags,
                    'iPublishTime' => date('Y-m-d', $news['iPublishTime']),
                    'iShareTimes' => $iShareTimes
                );
            }
        }

        $hotTag = Sdk_Cms_Tag::getHotTag($this->iCurrCityID);
        $hotTag = $hotTag['data'];

        $buildings = Sdk_Cms_News::getBuilds($this->sCurrCityCode);
        $buildings = $buildings['data'];

        $this->assign('newses', $newses);
        $this->assign('hotTag', $hotTag);
        $this->assign('pager', $pager);
        $this->assign('buildings', $buildings);
        $this->assign('banners', $this->aBanner);
        $this->assign('advs', $this->aAdv);

        $this->generateBreadcrumbs(array("资讯"=>0));
        $this->aMeta['title'] = '新闻列表 - '.$this->aMeta['title'];

    }

    /**
     * 修改文章中带楼盘名的内容
     * @param $aNews
     */
    private function changeNewsLouName(&$aNews)
    {
        if ($aNews['sLoupanID']) {
            $sNewsIDs = explode(',',$aNews['sLoupanID']);
            foreach ($sNewsIDs as $key => $value) {
                $aTmp = Model_CricUnit::getLoupanNames($value);
                $aLoupan = Model_CricUnit::getLoupanByID($value);
                $sUrl = Model_CricUnit::getUnitUrl($aLoupan['CityCode'],$aLoupan['RegionName'],$aLoupan['DistrictName'],$value);
                $sSubting = '<a title="" target="_blank" href="'.$sUrl.'"><strong>'.$aTmp[$value].'</strong></a>';
                $preg = "/<a.*?href=([^>]+)>.*?".$aTmp[$value].".*?<\/a>/ix";
                $preg1 = "/=\"".$aTmp[$value].".*?\"/ix";
                if (mb_strpos($aNews['sContent'],$aTmp[$value]) !== false) {//存在楼盘名
                    //先去掉带标签的楼盘名
                    preg_match_all($preg,$aNews['sContent'],$link);
                    preg_match_all($preg1,$aNews['sContent'],$link1);
                    if (!empty($link)) {
                        foreach ($link[0] as $k => $val) {
                            $aNews['sContent'] = str_replace($val,$aTmp[$value], $aNews['sContent']);
                        }
                    }
                    if (!empty($link1)) {
                        foreach ($link1[0] as $k => $val) {
                            $aNews['sContent'] = str_replace($val,'=""', $aNews['sContent']);
                        }
                    }

                    //去掉title标签之内的楼盘名
                    str_replace('"'.$aTmp[$value].'"','""', $aNews['sContent']);
                    $aNews['sContent'] = str_replace($aTmp[$value],$sSubting, $aNews['sContent']);
                }
            }
        }
    }

}