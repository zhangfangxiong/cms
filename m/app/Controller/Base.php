<?php

class Controller_Base extends Yaf_Controller
{
    /**
     * 当前城市
     * @var unknown
     */

    private $aCurrCity = array();
    protected static $sTouchwebRoot = '';
    protected static $isApp = 0;//是否是app
    protected static $noFooter = 0;//默认有头部
    protected static $noHeader = 0;//默认有尾部

    private $aCurrCityDefault = array
    (
        'sName' => '上海',
        'sPinyin' => 'shanghai',
        'sPinyinShort' => 'sh',
        'sLat' => '31.2382',
        'sLon' => '121.469',
        'iCodeID' => '12'
    );

    protected $sBreadcrumbs = null;

    protected $aMeta = [
        'title' => '房价点评网 房价走势 房价评估 房产估价网 - ',
        'keywords' => '房价,房价走势,房价评估,房产估价网 - 房价点评网',
        'description' => '房价点评网独立第三方房产价格指导平台，历经11年的基础数据准备和理论研究积累，经多方专家论证及严密计算，让用户得到的报告更加权威专业。 所有数据真实丈量，
        信息详实，是市面上唯一能够精准到房间号的评估工具。时时监测一手房和二手房的相关成交以及市场调研数据，定期发布全国重点城市的房价指数报告',
    ];

    /**
     * 执行Action前执行
     * @see Yaf_Controller::actionBefore()
     */
    public function actionBefore()
    {
        $sCityID = $this->getParam('cityID');
        self::$isApp = $this->getParam('isApp');
        self::$noFooter = $this->getParam('noFooter');
        self::$noHeader = $this->getParam('noHeader');
        if ($sCityID) {
            $this->setCurrCity($sCityID);
        }
        //头部的特殊处理
        if (self::$noHeader) {
            Util_Cookie::set('noHeader',1);
        } elseif (self::$noHeader === '0'){
            Util_Cookie::delete('noHeader');
        }
        if (self::$noHeader || (Util_Cookie::get('noHeader') && self::$noHeader !== '0')) {
            $noHeader = 1;
        } else{
            $noHeader = 0;
        }
        //isApp参数的特殊处理
        if (self::$isApp) {
            Util_Cookie::set('isApp',1);
        } elseif (self::$isApp === '0'){
            Util_Cookie::delete('isApp');
        }
        if (self::$isApp || (Util_Cookie::get('isApp') && self::$isApp !== '0')) {
            $isApp = 1;
        } else{
            $isApp = 0;
        }
        $this->setFrame('touchweb.phtml');
        self::$sTouchwebRoot = 'http://' . Yaf_G::getConf('touchweb', 'domain');
        $sStaticRoot = 'http://' . Yaf_G::getConf('static', 'domain');

        //获取城市列表
        $this->assign('aCurrentCity', $this->getCurrCity());

        $this->assign('sStaticRoot', $sStaticRoot);
        $this->assign('sTouchwebRoot', self::$sTouchwebRoot);
        $this->assign('sCurrClass', $this->_name);
        $this->assign('isApp', $isApp);
        $this->assign('noFooter', self::$noFooter);
        $this->assign('noHeader', $noHeader);
        $this->assign('aEntrenceInfo', $this->_EntrenceUrl());
    }

    protected function _404()
    {
        $sBody = $this->getView()->render('404.phtml');
        $this->getResponse()->setBody($sBody);
        return false;
    }

    /**
     * 执行Action后的操作
     * @see Yaf_Controller::actionAfter()
     */
    public function actionAfter()
    {

        $this->assign('aMeta', $this->aMeta);
        if ($this->autoRender() == true) {
            $aDebug = Util_Common::getDebugData();
            if ($aDebug) {
                $this->assign('__showDebugInfo__', 'showDebugInfo(' . json_encode($aDebug) . ');');
            }
            $this->assign('sBreadcrumbs', $this->sBreadcrumbs);
        } else {
        }
    }

    /**
     * Ajax或API请求时，返回json数据
     * @param unknown $mMsg
     * @param unknown $bRet
     */
    protected function showMsg($mMsg, $bRet)
    {
        $aData = array(
            'data' => $mMsg,
            'status' => $bRet
        );
        $sDebug = Util_Common::getDebugData();
        if ($sDebug) {
            $aData['debug'] = $sDebug;
        }
        $response = $this->getResponse();
        $response->appendBody(json_encode($aData, JSON_UNESCAPED_UNICODE));
        $this->autoRender(false);
    }

    protected function getCurrCity()
    {
        if (empty($this->aCurrCity)) {
            $aCurrCity = Util_Cookie::get("CurrCity");
            if (empty($aCurrCity)) {
                $this->aCurrCity = $this->aCurrCityDefault;
            } else {
                $this->aCurrCity = Util_Cookie::get("CurrCity");
            }
        }
        return $this->aCurrCity;
    }

    protected function setCurrCity($iCodeID)
    {
        //获取城市列表
        $aCityList = Sdk_Cms_Ucenter::city(array('withCode' => 1));
        if (isset($aCityList)) {
            Util_Cookie::set('CurrCity', $aCityList['data']['list'][$iCodeID], 86400 * 365);
            $this->aCurrCity = $aCityList['data']['list'][$iCodeID];
        }
    }

    /**
     * 判断当前是否登陆
     */
    protected static function checkHasLogin()
    {
        if (Util_Cookie::get('touchWebUserInfo')) {
            return Util_Cookie::get('touchWebUserInfo');
        } else {
            return false;
        }
    }

    //一些链接入口整理
    protected function _EntrenceUrl()
    {
        $wwwIndex = 'http://' . Yaf_G::getConf('fangjiadp', 'domain');
        $aCurrCity =$this->getCurrCity();
        return array(
            'ucenter' => array('name' => '个人中心', 'url' => self::$sTouchwebRoot . '/h5/ucenter/more'),
            'homeSearch' => array('name' => '首页搜索', 'url' => self::$sTouchwebRoot . '/h5/search'),
            'consultOnline' => array('name' => '在线咨询', 'url' => self::$sTouchwebRoot . '/h5/analyst/ask'),
            'evaluation' => array('name' => '评测列表', 'url' => self::$sTouchwebRoot . '/h5/xf/evaluation'),
            'aboutUs' => array('name' => '关于我们', 'url' => self::$sTouchwebRoot . '/h5/ucenter/aboutus'),
            'service' => array('name' => '网络服务协议', 'url' => self::$sTouchwebRoot . '/h5/ucenter/pagreement'),
            'index' => array('name' => '首页', 'url' => $wwwIndex.'/'.$aCurrCity['sPinyin'].'/advanced/search'),
            'analyst' => array('name' => '分析师详细页', 'url' => self::$sTouchwebRoot . '/h5/analyst/detail'),//后面传分析师ID,参数名id
            'houseloan' => array('name' => '房价计算器', 'url' => self::$sTouchwebRoot . '/h5/houseloan/start'),
            'mapsearch' => array('name' => '地图找房', 'url' => self::$sTouchwebRoot . '/h5/mapsearch'),
            'bigPic' => array('name' => '相册', 'url' => self::$sTouchwebRoot . '/h5/Evaluation/photo'),//后面传评测报告ID,参数名eID,图片ID，参数名iImageId
        );
    }
}