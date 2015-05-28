<?php

class Controller_Api_News extends Controller_Api_Base
{

    const TP_PATH_A = '/Api/News/firstunitnews.phtml';
    const TP_PATH_B = '/Api/News/listunitnews.phtml';
    /**
     * 第一个楼盘资讯
     */
    public function firstUnitNewsAction()
    {
       if (!isset($_GET['unitID'])) {
           $this->outPutHtml('');
       }
        $aParam['iLoupanID'] = intval($_GET['unitID']); // 楼盘ID

        if (empty( $aParam['iLoupanID'] )) {
            $this->outPutHtml('');
        }
        $aParam['page'] = isset($_GET['page']) ? intval($_GET['page']) : 1; // 当前页
        $aParam['pageSize'] =  1; // 返回条数
        $aParam['iPublishStatus'] = 1;
        $result = Sdk_Cms_News::getUnitNews($aParam);
        if (!empty($result['data']['aList'])) {
            $this -> __subStr($result['data']['aList']);
            $cityInfo = $this->getCityInfoByID($this -> getCityId($result['data']['aList']));
            $lpCityCode =  $this->getCityCodeByUnitId($aParam['iLoupanID']);
            $news = reset($result['data']['aList']);
            $Html =  $this->getView()->render(self::TP_PATH_A,array(
                'news' => $news,
                'cityInfo' => $cityInfo,
                'lpCityCode' => $lpCityCode,
                'staticBaseUrl' => $this->getStaticBaseUrl(),
                'newsBaseUrl' => $this->getNewsBaseUrl(),
            ));
            $this->outPutHtml($Html);
        }  else {
            $this->outPutHtml('');
        }

    }

    public function getStaticBaseUrl(){

        $domainConfig = Yaf_G::getConf('domain',null,'global_common');
        return $domainConfig['static'];
    }

    public function getNewsBaseUrl(){

        $domainConfig = Yaf_G::getConf('domain',null,'global_common');
        return $domainConfig['news'];
    }

    /**
     *  列表楼盘资讯
     */
    public function listUnitNewsAction()
    {
        if (!isset($_GET['unitID'])) {
            $this->outPutHtml('');
        }
        $aParam['iLoupanID'] = intval($_GET['unitID']); // 楼盘ID
        if (empty( $aParam['iLoupanID'] )) {
            $this->outPutHtml('');
        }
        $aParam['page'] = isset($_GET['page']) ? intval($_GET['page']) : 1; // 当前页
        $aParam['pageSize'] =  isset($_GET['pageSize']) ? intval($_GET['pageSize']) : 1; // 返回条数
        $aParam['iPublishStatus'] = 1;
        $result = Sdk_Cms_News::getUnitNews($aParam);
        if (!empty($result['data']['aList'])) {
            $this -> __subStr($result['data']['aList']);
            $cityInfo = $this->getCityInfoByID($this -> getCityId($result['data']['aList']));
            $lpCityCode =  $this->getCityCodeByUnitId($aParam['iLoupanID']);
            $news = $result['data'];
            $Html =  $this->getView()->render(self::TP_PATH_B,array(
                'news' => $news,
                'cityInfo' => $cityInfo,
                'lpCityCode' => $lpCityCode,
                'staticBaseUrl' => $this->getStaticBaseUrl(),
                'newsBaseUrl' => $this->getNewsBaseUrl(),
            ));
            $this->outPutHtml($Html);
        } else {
            $this->outPutHtml('');
        }
    }



    public function __subStr(&$aList) {
        if (empty($aList)) {
            return;
        }
        $len = 100;
        foreach ($aList as &$news) {
            if(empty($news['sAbstract'])) {
                $news['desc'] = strip_tags($news['sContent']);
                if(mb_strlen($news['desc']) > $len){
                    $news['desc'] = mb_substr($news['desc'], 0, $len);
                    $news['desc'] .= '......  ';
                }
            }else {
                $news['desc'] = $news['sAbstract'];
                if(mb_strlen($news['desc']) > $len){
                    $news['desc'] = mb_substr($news['desc'], 0, $len);
                    $news['desc'] .= '......  ';
                }
            }

        }
    }

    public function getCityId(&$List)
    {
        $cityId = array();
        if (!empty($List)) {
            foreach($List as $item) {
                if ($item['iCityID'] !=0) {
                    $cityId[] = $item['iCityID'];
                }
            }
        }
       if (!empty($cityId)) {
           return array_unique($cityId);
       }
        return null;
    }

    /*
     * $city ID 数组
     * */
    public function getCityInfoByID($city) {
        if (empty($city)) {
            return null;
        }
        $arr =  Sdk_Cms_City::getCityById($city);
        if (isset($arr['data'])) {
            return $arr['data'];
        }
        return null;
    }

    /**
     * 通过楼盘ID 获取城市code
     *
     */
    public function getCityCodeByUnitId($unitID) {

        $arr =  Sdk_Cms_Unit::getCityCodeByUnitID($unitID);
        if (isset($arr['data']['CityCode'])) {
            return $arr['data']['CityCode'];
        }
        return  '';
    }

    public function outPutHtml($html) {
        echo $html;die;
    }


}