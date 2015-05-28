<?php
/**
 * Created by PhpStorm.
 * User: chasel
 * Date: 2015/3/23
 * Time: 15:30
 */

class Controller_Mapi_Base extends Yaf_Controller
{

    protected $isApp = false;
    /**
     * Ajax或API请求时，返回json数据
     * @param unknown $mMsg
     * @param unknown $bRet
     */
    protected function showMsg ($mMsg, $bRet)
    {
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json; charset=utf-8');
        $response->appendBody(json_encode($mMsg, JSON_UNESCAPED_UNICODE));
        $this->autoRender(false);

        return false;
    }

    /**
     * 执行Action前执行
     * @see Yaf_Controller::actionBefore()
     */
    public function actionBefore ()
    {
        //加密算法
        //基础数据

        if (!$this->_isInternal()) {

            $iTime = $this->getParam('_time', 0);
            if (time() - $iTime > 1800) {
                return $this->showMsg(['code' => 1, 'msg' => '验签失效!'], false);
            }
            $sSign = $this->getParam('_sign');
            if ($sSign != md5($iTime . Yaf_G::getConf('signkey', 'sdk'))) {
                return $this->showMsg(['code' => 1, 'msg' => '验签失效!'], false);
            }
        }
        $params = $this->getParams();
        if (isset($_SERVER['HTTP_APP_IDFA']) || (isset($params['isApp']) && !empty($params['isApp']))) {
            $this->isApp = true;
        }

        $this->_logApp();

    }


    protected function _isInternal()
    {
        $isInternal = false;
        $allowip = Yaf_G::getConf('allowip', 'switchVersion');
        $sClientIp = $this->_request->getClientIP();
        if (in_array($sClientIp, $allowip)) {
            $isInternal = true;
        }
        return $isInternal;
    }

    private function _logApp()
    {
        $aInfo = $this->getParams();
        $strlen = strlen('HTTP_APP_');
        foreach ($_SERVER as $k => $v) {
            if (substr($k, 0, $strlen) == 'HTTP_APP_') {
                $aInfo[strtolower(substr($k, 5))] = $v;
            }
        }

        if (isset($aInfo['app_type']) && isset($aInfo['app_idfa']) && isset($aInfo['app_v'])
        && isset($aInfo['app_source']) && isset($aInfo['app_device_model']) && isset($aInfo['app_os_version']) && isset($aInfo['app_channel'])
        ) {
            $aInsertLog = [];
            $aInsertLog['sAppType'] = trim($aInfo['app_type']);
            $aInsertLog['sAppIdFa'] = trim($aInfo['app_idfa']);
            $aInsertLog['sAppVersion'] = trim($aInfo['app_v']);
            $aInsertLog['sAppSource'] = trim($aInfo['app_source']);
            $aInsertLog['sAppDiviceModel'] = trim($aInfo['app_device_model']);
            $aInsertLog['sAppOsVersion'] = trim($aInfo['app_os_version']);
            $aInsertLog['sAppChannel'] = trim($aInfo['app_channel']);
            $aInsertLog['sUrl'] = $this->getRequest()->getRequestUri();
            $aInsertLog['sParam'] = json_encode($this->getParams(), JSON_UNESCAPED_UNICODE);
            $aInsertLog['sIP'] = $this->getRequest()->getClientIP();
            Model_AppLog::addData($aInsertLog);
        }
    }
    /**
     * 执行Action后的操作
     * @see Yaf_Controller::actionAfter()
     */
    public function actionAfter ()
    {
    }

}