<?php

/**
 * Created by PhpStorm.
 * User: chasel
 * Date:  2015/3/20
 * Time:  15:40
 */
class Controller_Question_Index extends Controller_Base
{
    /**
     * 百度问答-问题管理
     */
    public function listAction()
    {
        $cityType = intval($this->getParam('cityType'));
        $cityDP = $this->getParam('cityDP');
        $cityFJ = $this->getParam('cityFJ');
        $sCreateTime = $this->getParam('sCreateTime');
        $eCreateTime = $this->getParam('eCreateTime');
        $sPushTime = $this->getParam('sPushTime');
        $ePushTime = $this->getParam('ePushTime');
        $questionID = trim($this->getParam('questionID'));
        $answerStatus = intval($this->getParam('answerStatus'));
        $showStatus = intval($this->getParam('showStatus'));
        $title = trim($this->getParam('title'));
        $key = trim($this->getParam('key'));
        $type = intval($this->getParam('type'));
        $iPage = intval($this->getParam('page'));
        $iRows = Yaf_G::getConf('rows', null, 'question');
        $iPage = empty($iPage)? 1: $iPage;

        $where = array();
        if(!empty($cityType)) {
            switch($cityType) {
                case 1:
                    $where['sChangedCity'] = '其它';
                    break;
                case 2:
                    if(!empty($cityDP)) {
                        $where['sChangedCity'] = $cityDP;
                    }
                    break;
                case 3:
                    if(!empty($cityFJ)) {
                        $where['sChangedCity'] = $cityFJ;
                    }
                    break;
                case 4:
                    $where['sChangedCity'] = '';
                    break;
                default:
                    break;
            }
        }

        if(!empty($sCreateTime)) {
            $where['iCreateTime >='] = strtotime($sCreateTime."0:0:0");
        }
        $where['iCreateTime <='] = empty($eCreateTime) ? time() : strtotime($eCreateTime."23:59:59");

        $current = date('Y-m-d',time());

        $where['iPushTime >='] = empty($sPushTime) ? strtotime($current."0:0:0") : strtotime($sPushTime."0:0:0");
        $where['iPushTime <='] = empty($ePushTime) ? strtotime($current."23:59:59") : strtotime($ePushTime."23:59:59");

        if(!empty($questionID)) {
            $where['sQuestionId'] = $questionID;
        }

        if(!empty($answerStatus)) {
            $where['iIsAnswer'] = $answerStatus - 1;
        }

        if(!empty($showStatus)) {
            $where['iShowStatus'] = $showStatus - 1;
        }

        if(!empty($title)) {
            $where['sTitle Like'] = '%'.$title.'%';
        }

        if(!empty($key)) {
            $where['sContent LIKE'] = '%'.$key.'%';
        }

        $where['iQuestionType'] = 1;

        if(!empty($type) && (2 ==$type)) {
            $where['iReaskId >'] = 0;
        }

        $key = Yaf_G::getConf('key', null, 'question');
        $token = Yaf_G::getConf('token', null, 'question');

        $questions = Model_Question::getList($where, $iPage, 'iCreateTime desc', $iRows);

        $cityAll = Model_City::getAll(array('where'=>array('iStatus'=>1)));
        $dpCitys = array();
        foreach($cityAll as $city) {
            $dpCitys[] = $city['sCityName'];
        }

        $changedCityAll = Model_Question::getAll(array('group' => 'sChangedCity'));
        $changeCitys = array();
        foreach($changedCityAll as $city) {
            $changeCitys[] = rtrim($city['sChangedCity'], '市');
        }
        $changeCitys = array_unique($changeCitys);
        $changeCitys = array_diff($changeCitys, $dpCitys);


        $sPushTime = empty($sPushTime) ? $current : $sPushTime;
        $ePushTime = empty($ePushTime) ? date('Y-m-d',strtotime('+1 day')) : $ePushTime;

        $this->assign('cityType', $cityType);
        $this->assign('cityDP', $cityDP);
        $this->assign('cityFJ', $cityFJ);
        $this->assign('sCreateTime', $sCreateTime);
        $this->assign('eCreateTime', $eCreateTime);
        $this->assign('sPushTime', $sPushTime);
        $this->assign('ePushTime', $ePushTime);
        $this->assign('answerStatus', $answerStatus);
        $this->assign('showStatus', $showStatus);
        $this->assign('type', $type);

        $this->assign('questions', $questions);
        $this->assign('userName', $this->aCurrUser['sRealName']);
        $this->assign('userID', $this->aCurrUser['iUserID']);
        $this->assign('key', $key);
        $this->assign('token', $token);
        $this->assign('dpCitys', $dpCitys);
        $this->assign('changeCitys', $changeCitys);
    }

    /**
     * 百度问答-问题统计
     */
    public function statisticsAction()
    {
        $current = date('Y-m-d', time());
        $city = $this->getParam('city');
        $this->assign('seCity', $city);
        $sPushTime = $this->getParam('sPushTime');
        $ePushTime = $this->getParam('ePushTime');
        $iPage = intval($this->getParam('iPage'));
        $iPage = empty($iPage) ? 1 : $iPage;
        $iRows = Yaf_G::getConf('rows', null, 'question');

        $start = empty($sPushTime) ? strtotime($current.'00:00:00') : strtotime($sPushTime. '00:00:00');
//        $end = empty($ePushTime) ? strtotime($current.'23:59:59') : strtotime($ePushTime.'23:59:59');
        $tomorrow = date('Y-m-d',strtotime('+1 day'));
        $end = empty($ePushTime) ? strtotime($tomorrow. '00:00:00') : strtotime($ePushTime.'23:59:59');

        $total = Model_Question::getCnt(array('where'=>array('iPushTime <=' => $end, 'iPushTime >=' => $start, 'iQuestionType'=>1)));
        $totalAnswer = Model_Question::getCnt(array('where'=>array('iPushTime <=' => $end, 'iPushTime >=' => $start, 'iIsVaildAnswer' => 1, 'iQuestionType'=>1)));

        $totalHide = Model_Question::getCnt(array('where'=>array('iPushTime <=' => strtotime($end), 'iPushTime >=' => strtotime($start), 'iShowStatus' => 0, 'iQuestionType'=>1)));

        if(!empty($total)) {
            $answerRate = $totalAnswer / $total;
            $hideRate = $totalHide / $total;
        }else {
            $answerRate = 0;
            $hideRate = 0;
        }
//        $answerRate = round($answerRate, 4);
//        $hideRate = round($hideRate, 4) * 100;

        $cityAll = Model_Question::getAll(array('where'=> array('sChangedCity !='=> '', 'iQuestionType' => 1),'group' => 'sChangedCity'));
        $citys = array();
        foreach($cityAll as $c) {
            $citys[] = $c['sChangedCity'];
        }

        if(!empty($city)){
            if($city == '未分配城市') {
                $city = '';
            }

            $cTotal = Model_Question::getCnt(array('where'=>array('iPushTime <=' => $end, 'iPushTime >=' => $start, 'sChangedCity'=> $city, 'iQuestionType' => 1)));
            $ctotalAnswer = Model_Question::getCnt(array('where'=>array('iPushTime <=' => $end, 'iPushTime >=' => $start, 'iIsVaildAnswer' => 1, 'sChangedCity'=> $city, 'iQuestionType' => 1)));

            $ctotalHide = Model_Question::getCnt(array('where'=>array('iPushTime <=' => strtotime($end), 'iPushTime >=' => strtotime($start), 'iShowStatus' => 0, 'sChangedCity'=> $city, 'iQuestionType' => 1)));

            $canswerRate = 0;
            $chideRate = 0;
            if(!empty($cTotal)) {
                $canswerRate = $ctotalAnswer / $cTotal;
                $chideRate = $ctotalHide / $cTotal;
            }

            $this->assign('single', true);
            $this->assign('ctotal', $cTotal);
            $this->assign('ctotalAnswer', $ctotalAnswer);
            $this->assign('canswerRate', $canswerRate);
            $this->assign('ctotalHide', $ctotalHide);
            $this->assign('chideRate', $chideRate);

            if(empty($city)){
                $this->assign('city', '未分配城市');
            }else{
                $this->assign('city', $city);
            }
        }else {
            $iPage = max($iPage, 1);
            $limit = ' limit '.  ($iPage - 1) * $iRows . ',' . $iRows;

            $sqlTotal = "select count(*) total, sChangedCity from t_question where iPushTime BETWEEN $start and $end and iQuestionType = 1 group by sChangedCity desc". $limit;
            $aTotal = Model_Question::query($sqlTotal);

            $aTotalAnswer = array();
            $aTotalHide = array();

            if(!empty($aTotal)) {
                foreach($aTotal as $c) {
                    $sqlTotalAnswer = "select count(*) total from t_question where iIsVaildAnswer = 1 and iPushTime BETWEEN $start and $end and iQuestionType = 1 and sChangedCity ='".$c['sChangedCity']."'";
                    $answer = Model_Question::query($sqlTotalAnswer, 'row');
                    if(!empty($answer)) {
                        $aTotalAnswer[] = array('total'=> $answer['total'], 'sChangedCity' => $c['sChangedCity']);
                    }else {
                        $aTotalAnswer[] = array('total'=> 0, 'sChangedCity' => $c['sChangedCity']);
                    }

                    $sqlTotalHide = "select count(*) total from t_question where iShowStatus = 0 and iPushTime BETWEEN $start and $end and iQuestionType = 1 and sChangedCity ='".$c['sChangedCity']."'";
                    $hide = Model_Question::query($sqlTotalHide, 'row');
                    if(!empty($hide)) {
                        $aTotalHide[] = array('total'=> $hide['total'], 'sChangedCity' => $c['sChangedCity']);
                    }else {
                        $aTotalHide[] = array('total'=> 0, 'sChangedCity' => $c['sChangedCity']);
                    }
                }
            }


            $aList = array();
            $aPager = '';

            if(!empty($aTotal) && !empty($aTotalAnswer) && !empty($aTotalHide)) {
                $len = sizeof($aTotal);
                for($i = 0; $i < $len; $i ++) {
                    $city = empty($aTotal[$i]['sChangedCity']) ? '未分配城市' : $aTotal[$i]['sChangedCity'];

                    $aList[$city] = array(
                        'total' => $aTotal[$i]['total'],
                        'answer' => $aTotalAnswer[$i]['total'],
                        'hide' => $aTotalHide[$i]['total'],
                        'answerRate' => $aTotalAnswer[$i]['total'] / $aTotal[$i]['total'],
                        'hideRate' => $aTotalHide[$i]['total'] / $aTotal[$i]['total']
                    );
                }

                $num = sizeof($aTotal);
                $url = Util_Common::getUrl();
                $aPager = Util_Page::getPage($num, $iPage, $iRows, $url);
            }

            $this->assign('aPager', $aPager);
            $this->assign('single', false);
            $this->assign('aList', $aList);
        }

        $this->assign('total', $total);
        $this->assign('totalAnswer', $totalAnswer);
        $this->assign('answerRate', $answerRate);
        $this->assign('totalHide', $totalHide);
        $this->assign('hideRate', $hideRate);
        $this->assign('citys', $citys);
        $this->assign('start', $start);
        $this->assign('end', $end);
    }

    /**
     * 百度问答-问题日志
     */
    public function logsAction()
    {
        $city = intval($this->getParam('city'));
        $userName = $this->getParam('userName');
        $realName = $this->getParam('realName');
        $operate = trim($this->getParam('operate'));
        $questionID = trim($this->getParam('questionID'));
        $oprateResult = intval($this->getParam('oprateResult'));
        $start = $this->getParam('sCreateTime');
        $end = $this->getParam('eCreateTime');
        $param = trim($this->getParam('param'));
        $iPage = intval($this->getParam('page'));
        $iPage = empty($iPage) ? 1 : $iPage;
        $iRows = Yaf_G::getConf('rows', null, 'question');

        $current = date('Y-m-d', time());
        $start = empty($start) ? strtotime($current.'00:00:00') : strtotime($start.'00:00:00');

        $tomorrow = date('Y-m-d',strtotime('+1 day'));
        $end = empty($end) ? strtotime($tomorrow. '00:00:00') : strtotime($end.'23:59:59');
//        $end = empty($end) ? strtotime($current.'23:59:59') : strtotime($end.'23:59:59');

        $where = array(
            'iStatus' => 1,
            'iCreateTime >=' => $start,
            'iCreateTime <=' => $end
        );

        if(!empty($city)) {
            $where['iCityID'] = $city;
        }

        if(!empty($userName)) {
            $where['sUserName'] = $userName;
        }

        if(!empty($realName)) {
            $where['sRealName'] = $realName;
        }

        if(!empty($operate)) {
            $where['operate LIKE'] = $operate;
        }

        if(!empty($questionID)) {
            $where['sQuestionIDs LIKE'] = '%'.$questionID.'%';
        }


        if(!empty($oprateResult)) {
            if(1 == $oprateResult) {
                $where['iIsSuccess'] = 1;
            }
            if(2 == $oprateResult) {
                $where['iIsSuccess'] = 0;
            }
        }

        if(!empty($param)) {
            $where['sContent LIKE'] = '%'.$param.'%';
        }

        $logs = Model_QuestionLog::getList($where, $iPage, 'iCreateTime', $iRows);
        $len = sizeof($logs['aList']);
        for($i = 0; $i < $len; $i ++)
        {
            $dCity = Model_City::getDetail($logs['aList'][$i]['iCityID']);
            if(!empty($dCity)) {
                $logs['aList'][$i]['cityName'] = $dCity['sCityName'];
            }
        }

        $users = Model_User::getAll(array('where'=>array('iStatus'=>1)));/////////////////////////////////////////////////应该带上角色,后续补上
        $citys = Model_City::getAll(array('where'=>array('iStatus'=>1)));

        $city = intval($this->getParam('city'));
        $userName = $this->getParam('userName');
        $realName = $this->getParam('realName');

        $this->assign('seCity', $city);
        $this->assign('userName', $userName);
        $this->assign('realName', $realName);
        $this->assign('oprateResult', $oprateResult);

        $this->assign('logs', $logs);
        $this->assign('users', $users);
        $this->assign('citys', $citys);
        $this->assign('start', $start);
        $this->assign('end', $end);
    }

    /**
     * 百度问答-我的回答
     */
    public function myanswerAction()
    {
        $status = intval($this->getParam('status'));
        $start = $this->getParam('sAnswerTime');
        $end = $this->getParam('eAnswerTime');
        $content = trim($this->getParam('content'));
        $uid = $this->aCurrUser['iUserID'];
        $iPage = $this->getParam('page');
        $iRows = Yaf_G::getConf('rows', null, 'question');

        $iPage = empty($iPage)? 1: $iPage;

        $current = date('Y-m-d', time());
        $start = empty($start) ? strtotime($current.'00:00:00') : strtotime($start.'00:00:00');
        $tomorrow = date('Y-m-d',strtotime('+1 day'));
        $end = empty($end) ? strtotime($tomorrow. '00:00:00') : strtotime($end.'23:59:59');
//        $end = empty($end) ? strtotime($current.'23:59:59') : strtotime($end.'23:59:59');

        $where = array(
            'sAppUID' => $uid,
            'iCreateTime <=' => $end,
            'iCreateTime >=' => $start,
        );
        if(!empty($status)) {
            if(1 == $status) {
                $where['iStatus'] = 1;
            }

            if(2 == $status) {
                $where['iStatus'] = 0;
            }
        }

        if(!empty($content)) {
            $where['sContent LIKE'] = '%'. $content. '%';
        }

        $replys = Model_Reply::getList($where, $iPage, 'iCreateTime DESC', $iRows);
        if(!empty($replys['aList'])) {
            $len = sizeof($replys['aList']);
            for($i = 0; $i < $len; $i ++) {
                $questioDetail = Model_Question::getRow(array('where'=>array('sQuestionId'=> $replys['aList'][$i]['sQuestionID'], 'iQuestionType'=> 1)));
                if(!empty($questioDetail)) {
                    $replys['aList'][$i]['qTitle'] = $questioDetail['sTitle'];
                    $replys['aList'][$i]['qContent'] = $questioDetail['sContent'];
                }
            }
        }

        $key = Yaf_G::getConf('key', null, 'question');
        $token = Yaf_G::getConf('token', null, 'question');

        $this->assign('status', $status);
        $this->assign('replys', $replys);
        $this->assign('userName', $this->aCurrUser['sRealName']);
        $this->assign('userID', $this->aCurrUser['iUserID']);
        $this->assign('start', $start);
        $this->assign('end', $end);
        $this->assign('key', $key);
        $this->assign('token', $token);
    }

    /**
     * 百度问答-详情
     */
    public function detailAction()
    {
        $questionID = $this->getParam('questionID');
        if(!empty($questionID)){
            $detail = Model_Question::getRow(array('where'=>array('sQuestionId'=>$questionID, 'iQuestionType'=>1)));
            $replys = Model_Reply::getAll(array('where'=>array('sQuestionID'=>$questionID, 'iStatus'=>1)));

            $this->assign('detail', $detail);
            $this->assign('replys', $replys);
        }
    }

    /**
     * 百度问答-更新问题内容
     */
    public function changecityAction()
    {
        $questionID = $this->getParam('cQuestionID');
        $changedCity = $this->getParam('changedCity');
        $isBatch = intval($this->getParam('batch'));

        $error = 0;
        if(empty($isBatch)) {
            $error = Model_Question::updData(['iAutoID'=> $questionID, 'sChangedCity'=>$changedCity]);
        }else {
            $error = Model_Question::batchChangeCity($questionID, $changedCity);
        }

        $result = array('msg' => 'success');
        $status = true;
        $operate = Yaf_G::getConf('operate', null, 'question');
        $opc = $operate[1];
        if($isBatch) {
            $opc = $operate[2];
        }

        if($error) {
            Model_QuestionLog::writeLog($questionID, $opc, $changedCity);
        }else {
            $result['msg'] = 'fail';
            $status = false;
            Model_QuestionLog::writeLog($questionID, $opc, $changedCity, 0);
        }

        return $this->showMsg($result, true);
    }

    /**
     * 百度问答-隐藏问题
     */
    public function hideShowAction()
    {
        $questionID = $this->getParam('questionID');
        $showStatus = intval($this->getParam('showStatus'));

        $error = Model_Question::updData(['iAutoID'=> $questionID, 'iShowStatus'=>$showStatus, 'iShowHideTime'=> time()]);

        $result = array('msg' => 'success');
        $status = true;
        $operate = Yaf_G::getConf('operate', null, 'question');
        $opc = empty($showStatus) ? $operate[3] : $operate[4];


        if($error) {
            Model_QuestionLog::writeLog($questionID, $opc, $questionID);
        }else {
            $result['msg'] = 'fail';
            $status = false;
            Model_QuestionLog::writeLog($questionID, $opc, $questionID, 0);
        }

        return $this->showMsg($result, true);
    }

    public function exportAction() {
        $cityType = intval($this->getParam('cityType'));
        $cityDP = $this->getParam('cityDP');
        $cityFJ = $this->getParam('cityFJ');
        $sCreateTime = $this->getParam('sCreateTime');
        $eCreateTime = $this->getParam('eCreateTime');
        $sPushTime = $this->getParam('sPushTime');
        $ePushTime = $this->getParam('ePushTime');
        $questionID = $this->getParam('questionID');
        $answerStatus = intval($this->getParam('answerStatus'));
        $showStatus = intval($this->getParam('showStatus'));
        $title = $this->getParam('title');
        $key = $this->getParam('key');
        $type = intval($this->getParam('type'));

        $where = array();
        if(!empty($cityType)) {
            switch($cityType) {
                case 1:
                    $where['sChangedCity'] = '其它';
                    break;
                case 2:
                    if(!empty($cityDP)) {
                        $where['sChangedCity'] = $cityDP;
                    }
                    break;
                case 3:
                    if(!empty($cityFJ)) {
                        $where['sChangedCity'] = $cityFJ;
                    }
                    break;
                case 4:
                    $where['sChangedCity'] = '';
                    break;
                default:
                    break;
            }
        }

        if(!empty($sCreateTime)) {
            $where['iCreateTime >='] = $sCreateTime;
        }
        $where['iCreateTime <='] = empty($eCreateTime) ? time() : strtotime($eCreateTime."23:59:59");;

        $current = date('Y-m-d',time());

        $where['iPushTime >='] = empty($sPushTime) ? strtotime($current."0:0:0") : strtotime($sPushTime."0:0:0");
        $where['iPushTime <='] = empty($ePushTime) ? strtotime($current."23:59:59") : strtotime($ePushTime."23:59:59");;

        if(!empty($questionID)) {
            $where['sQuestionId'] = $questionID;
        }

        if(!empty($answerStatus) && 2 != $answerStatus) {
            $where['iIsAnswer'] = $answerStatus;
        }

        if(!empty($showStatus) && 2 != $showStatus) {
            $where['iShowStatus'] = $showStatus;
        }

        if(!empty($title)) {
            $where['sTitle'] = $title;
        }

        if(!empty($key)) {
            $where['sContent LIKE'] = $key;
        }

        if(!empty($type)) {
            $where['iQuestionType'] = $type;
        }

        $questions = Model_Question::getAll(['where'=>$where]);

        Util_phpCsv::export_csv('questions.csv');
        echo iconv("utf8","gb2312","问题编号,户名,城市,问题标题,问题内容,回答时间,提问日期,推送日期,回复状态,状态\r\n");
        if(!empty($questions)) {
            foreach($questions as $question) {
                $qid = $question['sQuestionId'];
                $name = $question['sUname'];
                $sChangedCity = $question['sChangedCity'];
                $sTitle = $question['sTitle'];
                $sContent = $question['sContent'];
                $iAnswerTime = date('Y-m-d h:i', $question['iAnswerTime']);
                $iCreateTime = date('Y-m-d h:i', $question['iCreateTime']);
                $iPushTime = date('Y-m-d h:i', $question['iPushTime']);
                $astatus = empty($question['iIsAnswer']) ? '未回答' : '已回答';
                $sStatus = empty($question['iShowStatus']) ? '隐藏' : '显示';

                echo iconv("utf8","gb2312","$qid,$name,$sChangedCity,$sTitle,$sContent,$iAnswerTime,$iCreateTime,$iPushTime,$astatus,$sStatus\r\n");
            }
        }
        exit;
    }

    /**
     * 百度问答-获取问题回答列表
     */
    public function getReplyAction() {
        $questionID = $this->getParam('questionID');
//        $iPage = $this->getParam('iPage');
//        $iPage = empty($iPage) ? 1 : $iPage;
//        $iRows = Yaf_G::getConf('rows', null, 'question');

        if(!empty($questionID)){
            $where = array(
                'sQuestionID' => $questionID,
                'iStatus' => 1,
            );
            $replys = Model_Reply::getAll(['where'=>$where]);
            $reAsk = Model_Question::getAll(array('where'=>array('sQuestionId' => $questionID, 'iQuestionType' => 2)));

            $data = array();
            if(!empty($replys)){
                foreach ($replys as $r) {
                    $data[] = array(
                        'uname' => $r['sAppUName'],
                        'content' => $r['sContent'],
                        'time' => date('Y-m-d h:i', $r['iCreateTime']),
                        'type' => '回答',
                        'replyID' => $r['sReplyID'],
                    );
                }
            }

            if(!empty($reAsk)){
                foreach ($reAsk as $r) {
                    $data[] = array(
                        'uname' => $r['sUname'],
                        'content' => $r['sContent'],
                        'time' => date('Y-m-d h:i', $r['iCreateTime']),
                        'type' => '追问',
                        'replyID' => $r['iReplyId'],
                    );
                }
            }

            if(!empty($data)){
                usort($data, function($a, $b) {
                    if ($a['time'] == $b['time'])
                        return 0;
                    return ($a['time'] > $b['time']) ? -1 : 1;
                });

                $result = array('data'=>$data, 'msg'=>'success');
                return $this->showMsg($result, true);
            }
        }

        $result = array('msg'=>'fail');
        return $this->showMsg($result, false);
    }

}