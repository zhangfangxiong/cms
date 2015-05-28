<?php

/**
 * Created by PhpStorm.
 * User: chasel
 * Date:  2015/4/29
 * Time:  15:40
 */
class Controller_Question_Api extends Yaf_Controller
{
    public function actionBefore()
    {
        $dataAll = $this->getParams();
        $this->writeLog($dataAll);

        $key = Yaf_G::getConf('key', null, 'question');
        $token = Yaf_G::getConf('token', null, 'question');
        $keyReceived = $this->getParam('key');
        $tokenReceived = $this->getParam('token');

        if ($key != $keyReceived || $token != $tokenReceived) {
            $res = array('errno' => 1, 'errmsg' => '验签失效', 'status'=>false);
            echo json_encode($res);
            exit;
        }
    }

    /**
     * 百度问答API接口-订阅接口-提问通知
     */
    public function askAction()
    {
        $question_id = $this->getParam('question_id');

        $title = $this->getParam('title');
        $content = $this->getParam('content');
        $pictures = $this->getParam('pictures');
        $cid = $this->getParam('cid');
        $cname = Util_Tools::characet($this->getParam('cname'));
        $anonymous = $this->getParam('anonymous');
        $hit_keywords = $this->getParam('hit_keywords');
        $hit_cids = $this->getParam('hit_cids');
        $lbs_province = $this->getParam('lbs_province');
        $lbs_city = $this->getParam('lbs_city');
        $lbs_district = $this->getParam('lbs_district');
        $lbs_street = $this->getParam('lbs_street');
        $create_time = intval($this->getParam('create_time'));
        $appkey = intval($this->getParam('appkey'));
        $appname = $this->getParam('appname');
        $uid = $this->getParam('uid');
        $uname = Util_Tools::characet($this->getParam('uname'));

        $data = array(
            'sQuestionId' => $question_id,
            'sTitle' => $title,
            'sContent' => $content,
            'sPictures' => $pictures,
            'sCid' => $cid,
            'sCname' => $cname,
            'iAnonymous' => $anonymous,
            'sHitKeywords' => $hit_keywords,
            'sHitCids' => $hit_cids,
            'sLbsProvince' => $lbs_province,
            'sLbsCity' => $lbs_city,
            'sLbsDistrict' => $lbs_district,
            'sLbsStreet' => $lbs_street,
            'iCreateTime' => $create_time,
            'iAppKey' => $appkey,
            'sAppName' => $appname,
            'sUid' => $uid,
            'sUname' => $uname,
            'sChangedCity' => rtrim($lbs_city, '市'),
            'iPushTime' => time(),
            'iUpdateTime' => time()
        );

        $error = Model_Question::addData($data);
        if($error) {
            $res = array('errno' => 0, 'errmsg' => 'success');
        }else {
            $res = array('errno' => 1, 'errmsg' => 'fail');
        }

        echo json_encode($res);

        $this->autoRender(false);
    }

    /**
     * 百度问答API接口-提交接口-回答
     */
    public function replyAction()
    {
//        header("content-type:text/html; charset=utf-8");
        $questionID = $this->getParam('question_id');

        $content = $this->getParam('content');
        $app_uid = $this->getParam('app_uid');
        $app_uname = $this->getParam('app_uname');
        $pic_urls = $this->getParam('pic_urls');

        $result = array();

        if(!empty($questionID) && !empty($content)) {
            $objOpenapi = new Vendor_Question_OpenApi();

            $reply = $objOpenapi->submitReply($questionID,$content,$app_uid,$app_uname,'','',$pic_urls);

            $data = array(
                'sQuestionID' => $questionID,
                'sContent' => $content,
                'sAppUID' => $app_uid,
                'sAppUName' => $app_uname,
                'sPicUrls' => $pic_urls,
                'iCreateTime' => time(),
                'iUpdateTime' => time()
            );

            if(isset($reply['errno']) && $reply['errno'] === 0) {
                $data['sReplyID'] = $reply['data']['replyid'];
                Model_Reply::addData($data);

                $data['errorno'] = 0;
                $result['status'] = true;

                $question = Model_Question::getRow(array('where'=>array('sQuestionId' => $questionID)));
                if(!empty($question)){
                    $creatTime = $question['iCreateTime'];
                    $time = time() - $creatTime;
                    $update = array('iIsAnswer' => 1);
                    if($time <= 24* 60 * 60 ) {
                        $update['iIsVaildAnswer'] = 1;
                    }
                    $update['iAnswerTime'] = time();

                    Model_Question::updateByID($questionID, 1, $update);
                }
            }else {
                $data['iStatus'] = 0;

                $where = array(
                    'sQuestionID' => $questionID,
                    'sAppUID' => $app_uid,
                    'iStatus' => 0
                );
                $isReplyFail = Model_Reply::getRow(['where' => $where]);
                if(!empty($isReplyFail)) {
                    $upReply = array(
                        'iAutoID' => $isReplyFail['iAutoID'],
                        'sContent' => $content,
                        'iUpdateTime' => time()
                    );
                    Model_Reply::updData($upReply);
                }else{
                    Model_Reply::addData($data);
                }

                $data['errno'] = $reply['errno'];
                $data['errmsg'] = $reply['errmsg'];

                $result['status'] = false;
            }
            $result['code'] = $reply['errno'];

            $msg = explode('---',$reply['errmsg']);
            $result['msg'] = $msg[0];

            $this->writeLog($data);

            $opc = Yaf_G::getConf('operate', null, 'question');
            if(!$result['code']) {
                Model_QuestionLog::writeLog($questionID, $opc[0], $content);
            }else {
                $status = false;
                Model_QuestionLog::writeLog($questionID, $opc[0], $content, 0);
            }
        }else {
            $result['code'] = 999;
            $result['status'] = false;
            $result['msg'] = '问题编号和回答内容不能为空';
        }

        return $this->showMsg($result, $result['status']);
//        $this->autoRender(false);
    }

    /**
     * 百度问答API接口-提交接口-追答
     */
    public function replyAgainAction()
    {
        $questionID = $this->getParam('questionID');

        $replyID = intval($this->getParam('replyID'));
        $content = $this->getParam('content');
        $appUid = $this->getParam('app_uid');
        $appUname = $this->getParam('app_uname');
        $picUrls = $this->getParam('pic_urls');

        $result = array();

        if(!empty($questionID) && !empty($content) && !empty($replyID)) {
            $objOpenapi = new Vendor_Question_OpenApi();

            $reply = $objOpenapi->submitRereply($questionID, $replyID, $content, $appUid, $appUname);

            if(isset($reply['errno']) && $reply['errno'] === 0) {
                $data = array(
                    'sQuestionID' => $questionID,
                    'sReplyID' => $replyID,
                    'sContent' => $content,
                    'sAppUID' => $appUid,
                    'sAppUName' => $appUname,
                    'sPicUrls' => $picUrls,
                    'iCreateTime' => time(),
                    'iUpdateTime' => time()
                );

                Model_Rereply::addData($data);

                $data['errorno'] = 0;
            }else {
                $data['errno'] = $reply['errno'];
                $data['errmsg'] = $reply['errmsg'];
            }
            $result['code'] = $reply['errno'];
            $result['msg'] = $reply['errmsg'];

            $this->writeLog($data);
        }else {
            $result['code'] = 999;
            $result['msg'] = '问题编号,回答编号和回答内容不能为空';
        }

        return $this->showMsg($result, true);
    }

    /**
     * 百度问答API接口-订阅接口-剔除通知
     */
    public function deleteAction()
    {
        $questionID = $this->getParam('question_id');

        $reason = intval($this->getParam('reason'));
        $deleteTime = intval($this->getParam('create_time'));

        $param = array(
            'iReason' => $reason,
            'iDeleteTime' => $deleteTime,
            'iStatus' => 0
        );
        $error = Model_Question::updateByID($questionID, 1, $param);

        if($error) {
            $res = array('errno' => 0, 'errmsg' => 'success');
        }else {
            $res = array('errno' => 1, 'errmsg' => 'fail');
        }

        echo json_encode($res);

        $this->autoRender(false);
    }

    /**
     * 百度问答API接口-订阅接口-评价通知
     */
    public function evaluateAction()
    {
        $questionID = $this->getParam('question_id');

        $replyId = intval($this->getParam('reply_id'));
        $evaluateType = intval($this->getParam('evaluate_type'));
        $createTime = intval($this->getParam('create_time'));
        $appkey = intval($this->getParam('appkey'));
        $appname = $this->getParam('appname');
        $uid = $this->getParam('uid');
        $uname = $this->getParam('uname');

        $data = array(
            'sQuestionID' => $questionID,
            'iReplyID' => $replyId,
            'iEvaluateType' => $evaluateType,
            'iAppKey' => $appkey,
            'sAppName' => $appname,
            'sUserId' => $uid,
            'sUserName' => $uname,
            'iCreateTime' => $createTime,
            'iUpdateTime' => time()
        );
        $error = Model_Evaluate::addData($data);

        if($error) {
            $res = array('errno' => 0, 'errmsg' => 'success');
        }else {
            $res = array('errno' => 1, 'errmsg' => 'fail');
        }

        echo json_encode($res);
        $this->autoRender(false);
    }

    /**
     * 百度问答API接口-订阅接口-追问通知
     */
    public function askAgainAction()
    {
        $questionID = $this->getParam('question_id');

        $replyId = intval($this->getParam('reply_id'));
        $reaskId = intval($this->getParam('reask_id'));
        $content = $this->getParam('content');
        $pictures = $this->getParam('pictures');
        $createTime = intval($this->getParam('create_time'));
        $appkey = intval($this->getParam('appkey'));
        $appname = $this->getParam('appname');
        $uid = $this->getParam('uid');
        $uname = $this->getParam('uname');

        $data = array(
            'sQuestionId' => $questionID,
            'sContent' => $content,
            'sPictures' => $pictures,
            'iReplyId' => $replyId,
            'iReaskId' => $reaskId,
            'iAppKey' => $appkey,
            'sAppName' => $appname,
            'sUid' => $uid,
            'sUname' => $uname,
            'iCreateTime' => $createTime,
            'iUpdateTime' => time(),
            'iPushTime' => time(),
            'iQuestionType' => 2
        );

        $error = Model_Question::addData($data);

        $updata = array('iReaskId' => $reaskId);
        Model_Question::updateByID($questionID, 1, $updata);

        if($error) {
            $res = array('errno' => 0, 'errmsg' => 'success');
        }else {
            $res = array('errno' => 1, 'errmsg' => 'fail');
        }

        echo json_encode($res);
        $this->autoRender(false);
    }

    /*
     * 写日志
     * param $content 接口数据
     */
    public function writeLog($content){
        $url = $this->getRequest()->getHttpHost(). $this->getRequest()->getRequestUri();

        $data = array(
//            'sQuestionID' => $content['question_id'],
            'sInterfaceUrl' => $url,
            'sContent' => http_build_query($content),
            'iCreateTime' => time(),
            'iUpdateTime' => time()
        );

        Model_InterfaceLog::addData($data);
    }

}