<?php

/**
 * Created by PhpStorm.
 * User: xiaoyao
 * Date: 2015/1/23
 * Time: 10:48
 */
class Controller_Cmd_Crawlcricnews extends Controller_Cmd_Base
{

    protected $oHttpclient = null;
    protected $iCityCurrentCnt = 0;
    protected $ormCrawlNew = null;

    protected $cityMap = ['fj' => 'fz', 'guizhou' => 'gy', 'gx' => 'nn', 'hn' => 'zz', 'sc' => 'cd', 'sd' => 'jn', 'jl' => 'cc', 'yn' => 'km'];
    public function indexAction()
    {
        $this->oHttpclient = new Util_HttpClient();
        $this->ormCrawlNew = Util_Common::getOrm('crawl_data', 't_cricnews', false);
        $this->ormCrawlNew->setDebug(false);
        $this->ormCrawlNew->setNeedCache(false);
        $aCityList = $this->_getCity();
        if ($aCityList) {
            foreach ($aCityList as $aCity) {
                $this->_fetchNewsFromApi($aCity);

            }

        }
    }

    private function _fetchNewsFromApi($aCity)
    {
        echo "Start fetch {$aCity['sCityName']} new list", PHP_EOL;
        $this->iCityCurrentCnt = 0;
        $aNewsList = $this->_getPageNews($aCity['sFullPinyin'], 1);
        if ($aNewsList) {
            $iTotal = $aNewsList['newsString']['total'];
            $iPage = ceil($iTotal / 10);
            for ($i = 1; $i <= $iPage; $i++) {
                $aNewsRet = $this->_getPageNews($aCity['sFullPinyin'], $i);
                if ($aNewsRet && isset($aNewsRet['newsString']['data'])) {
                    $this->_addNews($aNewsRet['newsString']['data']);
                }
                if (Util_Common::getDebug()) {
                    Util_Common::getDebug()->clear();
                }
            }
        }
        echo "Finish fetch {$aCity['sCityName']} new list", PHP_EOL;
    }

    private function _addNews($aNewsList)
    {
        foreach ($aNewsList as $aNews) {
            $aNewRow = $this->ormCrawlNew->fetchRow(['where' => ['id' => $aNews['id']]]);
            if (!empty($aNewRow)){
                continue;
            }
            $sNewsContent = $this->_getNewDetailFromUrl($aNews['url']);
            $sAuthor = $this->_regAuthor($sNewsContent);
            $aNews['new_author'] = trim($sAuthor);
            $sLoupanID = $this->_regLoupanID($aNews['content']);
            $aNews['sLoupanID'] = $sLoupanID;
            $this->ormCrawlNew->insert($aNews);
            $this->iCityCurrentCnt++;
            echo "get news count {$this->iCityCurrentCnt}", PHP_EOL;
            //exit;
        }
    }

    private function _getPageNews($sCity, $iPage)
    {
        $sUrl = 'http://www.fangjiadp.com/' . $sCity . '/newslist/' . $iPage;
        $code = $this->oHttpclient->post($sUrl, []);
        $aNewsList = [];
        echo $sUrl, PHP_EOL;
        echo "code:{$code}", PHP_EOL;
        if (200 == $code) {
            $aNewsList = json_decode($this->oHttpclient->content, true);
        }
        return $aNewsList;
    }

    private function _getNewDetailFromUrl($url)
    {
        $code = $this->oHttpclient->post($url, []);
        echo $url, PHP_EOL;
        if (200 == $code) {
            return $this->oHttpclient->content;
        }
        return false;
    }

    private function _regAuthor($sNewsContent)
    {
        $rule = '/\<span\>作者：(.*?)\<\/span\>/';
        preg_match($rule, $sNewsContent, $aMatch);
        if (isset($aMatch[1])) {
            return $aMatch[1];
        }
        return '';
    }

    private function _getCity()
    {
        return $aCity = Model_City::getAll(['where' => ['iStatus' => 1]]);
    }


    public function importoneAction()
    {
        $this->ormCrawlNew = Util_Common::getOrm('crawl_data', 't_cricnews', false);
        $this->ormCrawlNew->setDebug(false);
        $this->ormCrawlNew->setNeedCache(false);

        //, 1, 2, 3, 4
        $aNewsList = $this->ormCrawlNew->fetchAll(['where' => ['inewid IN' => [3166]]]);
        $this->_addNewsToCms($aNewsList);

    }
    public function importAction()
    {
        $this->ormCrawlNew = Util_Common::getOrm('crawl_data', 't_cricnews', false);
        $this->ormCrawlNew->setDebug(false);
        $this->ormCrawlNew->setNeedCache(false);
        $i = 0;
        do {
            $aNewsList = $this->ormCrawlNew->fetchAll(
                [
                    'where' => ['cmsnewsid' => 0],
                    'limit' => 500,
                    'order' => 'updatetime ASC'
                ]
            );
            if(empty($aNewsList)) {
                break;
            }
            echo $i, PHP_EOL;
            $i++;
            $this->_addNewsToCms($aNewsList);
            if (Util_Common::getDebug()) {
                Util_Common::getDebug()->clear();
            }

        } while(true);
        /**
        $iTotal = $this->ormCrawlNew->fetchCnt();
        $iLimit = 500;
        $iPage = ceil($iTotal / $iLimit);
        echo "start import news", PHP_EOL;
        for ($i = 0; $i < $iPage; $i++) {
            $iStart = $i * $iLimit;
            $sLimit = "{$iStart}, {$iLimit}";
            $aNewsList = $this->ormCrawlNew->fetchAll(['limit' => $sLimit, 'order' => 'updatetime ASC']);
            $this->_addNewsToCms($aNewsList);
            echo $iStart .' / ' . $iTotal . ' = '. ceil($iStart / $iTotal * 100) . ' % finish', PHP_EOL;
            Util_Common::getDebug()->clear();
            //print_r($aNewsList);exit;
        }*/
        echo "finish import news", PHP_EOL;
    }

    private function _addNewsToCms($aNewsList)
    {
        if (!empty($aNewsList)) {
            foreach($aNewsList as $aNews) {
                $insertData = [];
                $insertData['iPublishTime'] = $insertData['iCreateTime'] = $aNews['createtime'];

                $insertData['iUpdateTime'] = $aNews['updatetime'];
                $insertData['iPublishStatus'] = 1;
                $insertData['iStatus'] = 1;
                $insertData['sTag'] = '';
                $insertData['sKeyword'] = '';
                $insertData['sSource'] = '';
                $insertData['sContent'] = $aNews['content'];
                $insertData['sAbstract'] = $aNews['zhaiyao'];
                $citykey = array_keys($this->cityMap);
                if (in_array($aNews['city'], $citykey)) {
                    $city = $this->cityMap[$aNews['city']];
                } else {
                    $city = $aNews['city'];
                }
                $aCity = Model_City::getCityByPinyin($city);
                if (empty($aCity)) {
                    $aCity = Model_City::getCityByFullPinyin($city);
                }
                //echo $city;
                //var_dump($aCity);
                $insertData['iCityID'] = isset($aCity['iCityID']) ? $aCity['iCityID'] : 0;
                $insertData['sShortTitle'] = $insertData['sTitle'] = $aNews['title'];
                $insertData['sImage'] = $aNews['pic'];
                $insertData['sAuthor'] = $aNews['author'];
                $insertData['sLoupanID'] = $aNews['sLoupanID'];
                $insertData['iTypeID'] = Model_News::GUIDE_NEWS;
                $aCate = Model_Category::getCategoryByName(Model_Category::CATEGORY_GUIDE_NEWS, '历史文章');
                $insertData['iCategoryID'] = $aCate['iCategoryID'];
                $insertData['iAuthorID'] = 0;
                $insertData['sSource'] = $aNews['url'];
                //print_r($aCate);
                $iNewsID = Model_News::addData($insertData);
                $this->ormCrawlNew->update(['inewid' => $aNews['inewid'], 'cmsnewsid' => $iNewsID]);
                //exit;
            }
        }
    }


    private function _regLoupanID($sContent)
    {
        //$doc = new DOMDocument();
        //$doc->loadHTML($sContent);
        //$xpath = new DOMXpath($doc);
        //print_r($xpath);
        //print_r($xpath->query('/html/body/div/div/div'));
        //$html = new simple_html_dom();
        //$html->load($sContent);
        //$a = $html->find('a');
        //print_r($a);
        $rule = '/<a(.*?)href="(.*?)"(.*?)>(.*?)<\/a>/i';
        //$rule = '/<a[^>]+>[^>]+a>/';
        $sLoupanID = '';
        preg_match_all($rule, $sContent, $aMatch);
        var_dump($aMatch);
        if (isset($aMatch[2])) {
            $aLoupanID = [];
            foreach($aMatch[2] as $key => $value) {
                $loupanID = basename($value);
                if (is_numeric($loupanID)) {
                    array_push($aLoupanID, $loupanID);
                }
            }
            if ($aLoupanID) {
                $aLoupanID = array_unique($aLoupanID);
                $sLoupanID = implode(',', $aLoupanID);
            }

        }
        return $sLoupanID;

    }

}