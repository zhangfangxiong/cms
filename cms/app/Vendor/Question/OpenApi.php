<?php
/**
 * 知道开放平台api
 * 输入输出均为utf8编码
 */
class Vendor_Question_OpenApi {
    private $_host = '';
    private $_appCfg = '';
    
    /**
     * 构造函数，进行配置的初始化
     */
    public function __construct() {
        $hostFlag = Yaf_G::getConf('ZHIDAO_OPENAPI_TEST_FLAG', null, 'question');
        $host = Yaf_G::getConf('ZHIDAO_OPENAPI_HOST', null, 'question');
        $hostTest = Yaf_G::getConf('ZHIDAO_OPENAPI_HOST_TEST', null, 'question');
        $appkey = Yaf_G::getConf('ZHIDAO_OPENAPI_APPKEY', null, 'question');
        $securitykey = Yaf_G::getConf('ZHIDAO_OPENAPI_SECURITYKEY', null, 'question');

        $this->_host = $hostFlag ? $hostTest : $host;
        $this->_appCfg = array(
            'appkey'      => $appkey,
            'securitykey' => $securitykey
        );
        
        if ($appkey == '') {
            $this->_writeLog("please fill the appkey in zhidao_conf.inc.php");
        }
        
        if ($securitykey == '') {
            $this->_writeLog("please fill the securitykey in zhidao_conf.inc.php");
        }
    }
    
    /**
     * 向百度知道开放平台提交回答（参数必须为utf8编码）
     * @param string $questionid      必填，要回答的问题id
     * @param string $content         必填，回答内容
     * @param uint   $appUid          必填，您站点内的回答用户id
     * @param string $appUname        必填，您站点内的回答用户名
     * @param string $appUavatar      必填，您站点内的回答用户头像图片url
     * @param string $appUprofile     必填，您站点内的回答用户个人主页url
     * @param string $appUninterest   可选，您站点内的回答用户个人擅长点
     * @param string $appUrl          可选，您站点内对应的问题页面url
     * @return array(
     *     'errno'  => 0,             本次提交成功与否，0成功，其他值失败
     *     'errmsg' => '',            本次提交的中文提示信息
     *     'data'   => array(
     *         'questionid' => '',    提交成功时，返回的问题id
     *         'replyid'    => '',    提交成功时，返回的回答id
     *     ) 
     * )  
     */
    public function submitReply($questionid, $content, $appUid, $appUname, $appUavatar, $appUprofile, $appUninterest = '', $appUrl = '') {
        $data = array();
        $data['questionid']    = trim($questionid);
        $data['content']       = trim($content);
        $data['app_uid']       = intval($appUid);
        $data['app_uname']     = trim($appUname);
        $data['app_uavatar']   = trim($appUavatar);
        $data['app_uprofile']  = trim($appUprofile);
        $data['app_uinterest'] = trim($appUninterest);
        $data['app_url']       = trim($appUrl);
        $data['pic_urls']      = '';
        
        //构建权限参数
        $this->_buildSign($data);
       
        //发送请求
        return $this->_sendRequest($this->_host . '/openapi/submit/reply', $data);
    }    
    
    /**
     * 向百度知道开放平台提交追答（参数必须为utf8编码）
     * @param string $questionid  必填，要追答的问题id
     * @param uint   $replyid     必填，要追答的回答id
     * @param string $content     必填，追答内容
     * @param uint   $appUid      必填，您站点内的追答用户id
     * @param string $appUname    必填，您站点内的追答用户名
     * @return array(
     *     'errno'  => 0,             本次提交成功与否，0成功，其他值失败
     *     'errmsg' => '',            本次提交的中文提示信息
     * )  
     */
    public function submitRereply($questionid, $replyid, $content, $appUid, $appUname) {
        $data = array();
        $data['questionid']    = trim($questionid);
        $data['replyid']       = intval($replyid);
        $data['content']       = trim($content);
        $data['app_uid']       = intval($appUid);
        $data['app_uname']     = trim($appUname);
        
        //构建权限参数
        $this->_buildSign($data);
         
        //发送请求
        return $this->_sendRequest($this->_host . '/openapi/submit/rereply', $data);
    }
    
    /**
     * 构建安全参数
     * @param $data
     */
    private function _buildSign(&$data) {
        $appKey = $this->_appCfg['appkey'];
        $sk     = $this->_appCfg['securitykey'];
    
        $qid     = isset($data['questionid']) ? $data['questionid'] : '';
    
        $data['appkey'] = $appKey;
        $data['sign']   = md5("$sk&$appKey&$qid");
    }
    
    /**
     * 向指定url发起请求
     * @param $url   指定url
     * @param $data  发送的数据
     */
    private function _sendRequest($url, $data) {
        $out = array(
            'errno'  => 0,
            'errmsg' => '',
            'data'   => array(),
        );
    
        $postStr = '';
        foreach ($data as $key=>$value) {
            if (is_array($value)) {
                $value = implode(',', $value);
            }
            $postStr .= ($key . '=' . urlencode($value) . '&');
        }
    
        //build request
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postStr);
        curl_setopt($curl, CURLOPT_USERAGENT, 'zhidao_sdk');
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 15);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Expect: '));    //avoid continue100
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
   
        //exec
        $curlRes = curl_exec($curl);
        
        //connect failed
        if ($curlRes === false) {
            $out['errno']  = 1;
            $out['errmsg'] = 'connect failed, error:' . curl_error($curl);
            $this->_writeLog("connect failed, maybe network is invalid. url[$url] error[" . curl_error($curl) . "]");
        }
        else {
            $out = json_decode($curlRes, true);
        }
        
        //close
        curl_close($curl);
        
        //fill response
        return $out;
    }
    
    /**
     * 写log
     * @param $log 日志内容
     */
    private function _writeLog($log) {
        $hostFlag = Yaf_G::getConf('ZHIDAO_OPENAPI_TEST_FLAG', null, 'question');
        if ($hostFlag) {
            echo $log . "<br />";
        }
    }
}