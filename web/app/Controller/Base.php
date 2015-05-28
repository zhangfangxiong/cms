<?php

class Controller_Base extends Yaf_Controller
{

    /**
     * 当前城市
     * @var unknown
     */
    protected $iCurrCityID = null;

    protected $sCurrCityCode = null;

    protected $sCurrCityName = null;


    protected $sBreadcrumbs = null;

    protected $aMeta = [
        'title' => '房价点评网 房价走势 房价评估 房产估价网-',
        'keywords' => '房价,房价走势,房价评估,房产估价网 - 房价点评网',
        'description' => '房价点评网独立第三方房产价格指导平台，历经11年的基础数据准备和理论研究积累，经多方专家论证及严密计算，让用户得到的报告更加权威专业。 所有数据真实丈量，
        信息详实，是市面上唯一能够精准到房间号的评估工具。时时监测一手房和二手房的相关成交以及市场调研数据，定期发布全国重点城市的房价指数报告',
    ];
    /**
     * 执行Action前执行
     * @see Yaf_Controller::actionBefore()
     */
    public function actionBefore ()
    {
        $guid = Util_Cookie::get('guid');
        if (empty($guid)) {
            Util_Cookie::set('guid', Util_Guid::get('-'), 86400*365);
        }
        $aCity = Util_Cookie::get('aCity');
        $sCityCode = $this->getParam('city');

        if (!empty($aCity) && $aCity['sFullPinyin'] == $sCityCode) {

        } else {

            $aRet = Sdk_Cms_City::getCityByCode($sCityCode);

            if (!empty($aRet) && $aRet['status'] == true && !empty($aRet['data'])) {
                $aCity = $aRet['data'];
                Util_Cookie::set('aCity', ['iCityID' => $aCity['iCityID'], 'sCityName' => $aCity['sCityName'], 'sFullPinyin' => $aCity['sFullPinyin']]);
            }
        }


        $this->sBreadcrumbs = '<a href="http://www.fangjiadp.com/">房价点评首页</a>'.'<span> &gt;</span> ';

        $this->iCurrCityID = $aCity['iCityID'];
        $this->sCurrCityCode = $aCity['sFullPinyin'];
        $this->sCurrCityName = $aCity['sCityName'];

        $this->assign('iCurrentCityID', $this->iCurrCityID);
        $this->assign('sCurrCityCode', $this->sCurrCityCode);
        $this->assign('sCurrCityName', $this->sCurrCityName);
        $this->assign('newsUrl',  $this -> getNewsUrl());
        $this->assign('sStaticRoot', 'http://' . Yaf_G::getConf('static', 'domain'));

        $this->assign('sBreadcrumbs', $this->sBreadcrumbs);

        if (empty($aCity)) {
            return $this->_404();
        }

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
    public function actionAfter ()
    {
        if ($this->autoRender() == true) {

            $this->assign('aMeta', $this->aMeta);
            $this->assign('oRequest', $this->_request);
            $this->assign('switch_version' , $this->_checkSwitchVersion());
//            if (isset($_COOKIE['fjdp_version'])) {
//                $this->assign('fjdp_version', $_COOKIE['fjdp_version']);
//            }
            $this->assign('fjdp_version' ,Yaf_G::getEnv());
            $aDebug = Util_Common::getDebugData();
            if ($aDebug) {
                $this->assign('__showDebugInfo__', 'showDebugInfo(' . json_encode($aDebug) . ');');
            }
            $this->assign('sBreadcrumbs', $this->sBreadcrumbs);
        } else {}
    }

    protected function _checkSwitchVersion()
    {
        $switch_version = false;
        $allowip = Yaf_G::getConf('allowip', 'switchVersion');
        $sClientIp = $this->_request->getClientIP();
        if (in_array($sClientIp, $allowip)) {
            $switch_version = true;
        }
        return $switch_version;
    }

    /**
     * Ajax或API请求时，返回json数据
     * @param unknown $mMsg
     * @param unknown $bRet
     */
    protected function showMsg ($mMsg, $bRet)
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

    /**
     * 生成面包屑
     */
    protected function generateBreadcrumbs ($options = null) {
        $len = sizeof($options);
        if(is_array($options) && !empty($options)){
            $index = 0;
            foreach($options as $name => $url) {
                $index ++;
                if(empty($url)) {
                    $this->sBreadcrumbs .= $name;
                }else {
                    $this->sBreadcrumbs .= '<a href="'.$url.'">'.$name. '</a>';
                }

                if($index != $len) {
                    $this->sBreadcrumbs .= '<span>&gt;</span>';
                }
            }
        }

    }

    protected function getNewsUrl()
    {
    return Yaf_G::getConf('newsUrl');
    }

    // 获取当前城市信息
    protected function getCurrCityInfo()
    {

      $aCity = Util_Cookie::get('aCity');
      if (!empty($aCity)) {
          return $aCity;
      }
      $IP = $this->getRequest()->getClientIP();
      if (empty($IP)) {
          return null;
      }

      $ipApi = 'http://ip.house.sina.com.cn/api.php';
      //$ipApi.= "?ip=".$IP;
      $http = new Util_HttpClient();
      $code = $http -> get($ipApi);
      if ($code == 200) {
          $result = json_decode($http->content,true);
          $aRet =  Sdk_Cms_City::getCityByName($result['city']);
          $aCity = null;
          if (!empty($aRet) && $aRet['status'] == true && !empty($aRet['data'])) {
              $aCity = $aRet['data'];
              Util_Cookie::set('aCity', ['iCityID' => $aCity['iCityID'], 'sCityName' => $aCity['sCityName'], 'sFullPinyin' => $aCity['sFullPinyin']]);
          }
          return $aCity;
      } else {
          return null;
      }


    }

    // 当前url
    protected function getCurUrl(){
        $url = (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') ? 'https://' : 'http://';
        $url .= $_SERVER['HTTP_HOST'];
        $url .= isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : urlencode($_SERVER['PHP_SELF']) . '?' . urlencode($_SERVER['QUERY_STRING']);
        return $url;
    }
}