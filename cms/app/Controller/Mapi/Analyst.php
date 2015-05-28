<?php

/**
 * Created by PhpStorm.
 * User: chasel
 * Date:  2015/3/20
 * Time:  15:40
 */
class Controller_Mapi_Analyst extends Controller_Mapi_Base
{

    /**
     * 获取专属分析师
     */
//    public function getbelongAction()
//    {
//        $analystID = intval($this->getParam('analystID'));
//
//        $result = array(
//            'code' =>  0,
//            'msg' => 'ok',
//            'data' => array()
//        );
//
//        if(!empty($analystID)) {
//            $analys = Model_Analysts::getAnalystByID($analystID);
//            $where = array(
//                'iAnalystId' => $analystID
//            );
//            $followed = Model_AnalystFollowed::getCnt(array('where'=>$where));
//            $optionFollowed = Model_AnalystFollowed::getCnt(array('where'=>$where));
//
//            if(!empty($analys)) {
//                $result['data'] = array(
//                    'analystID' => $analys['ID'],
//                    'sName' => $analys['AnalystsName'],
//                    'sAvataImg' => Util_Uri::getCricViewURL($analys['AnalystHead'], 300, 400),
//                    'sTitle' => $analys['AnalystLevel'],
//                    'sMessage' => 'Hi,我是您的贴身专属分析师',
//                    'sTel' => $analys['Phone'],
//                    'sFollows' => $followed,
//                    'sZan' => $optionFollowed
//                );
//            }
//        }else {
//            $result['code'] = 1;
//            $result['msg'] = '参数不全';
//        }
//
//        return $this->showMsg($result, true);
//    }

    /**
     * 查看分析师详情
     */
    public function detailAction()
    {
        $analystID = intval($this->getParam('analystID'));

        $result = array(
            'code' =>  0,
            'msg' => 'ok',
            'data' => array()
        );

        if(!empty($analystID)) {
            $analys = Model_Analysts::getAnalystByID($analystID);
            $where = array(
                'iAnalystId' => $analystID,
                'iStatus' => 1
            );
            $followed = Model_AnalystFollowed::getCnt(array('where'=>$where));
//            $optionFollowed = Model_AnalystOptionFollowed::getCnt(array('where'=>$where));
            $fans = Model_AnalystFans::getCnt(array('where'=>$where));
            $message = Yaf_G::getConf('welcome_message',null,'mapi');

            if(!empty($analys)) {
                $sTel400 = Yaf_G::getConf('tel_400',null,'mapi');
                if(!empty($analys['PhoneRegion'])) {
                    $sTel400 .=','.$analys['PhoneRegion'];
                }

                $result['data'] = array(
                    'analystID' => $analys['ID'],
                    'sName' => $analys['AnalystsName'],
                    'sAvataImg' => empty($analys['AnalystHead'])? Util_Uri::getDefaultImg(2) : Util_Uri::getCricViewURL($analys['AnalystHead'], 120, 120, 0, 0, 1),
                    'sTitle' => $analys['AnalystLevel'],
                    'sDescribe' => $analys['BriefIntroduction'],
                    'sMessage' => empty($analys['BriefIntroduction']) ? $message : $analys['BriefIntroduction'],
                    'sTel' => $sTel400,
                    'sFollows' => $followed,
                    'sZanCount' => $fans
                );
            }
        }else {
            $result['code'] = 1;
            $result['msg'] = '参数不全';
        }

        return $this->showMsg($result, true);
    }

    /**
     * 查看分析师的楼盘点评列表
     */
    public function commentsAction()
    {
        $result = array(
            'code' =>  0,
            'msg' => 'ok',
            'data' => array()
        );

        $analystID = intval($this->getParam('analystID'));
        $page = intval($this->getParam('iPage'));
        $row = intval($this->getParam('iRows'));

        if(!empty($analystID)) {
            $page = empty($page) ? 1 : $page;
            $row = empty($row) ? 10 : $row;

            $options = Model_CricAnalystsOpinion::getOpinionListByID($analystID, $row, $page);

            $result['data'] = array(
                'iTotalNum' => $options['iTotal'],
                'iPage' => $page,
                'iRows' => $row,
                'list' => array()
            );
            if(!empty($options['aList'])) {
                foreach($options['aList'] as $op) {
                    $likeNum = Model_AnalystOptionFollowed::getCnt(array('where'=>array('iOptionId'=>$op['ID'], 'iStatus' => 1)));
//                    $likeNum = Model_AnalystFans::getCnt(array('where'=>array('iAnalystId' => $analystID,'iStatus' => 1)));
                    $result['data']['list'][] = array(
                        'iCommentID' => $op['ID'],
                        'iBuildingID' => $op['iBuildingID'],
                        'sBuildingTitle' => $op['sBuildingTitle'],
                        'sScore' => $op['sScore'],
                        'sComment' => $op['Opinion'],     // 点评内容
                        'images' => $op['images'],  // 至多三张图片/////来源不详
                        'sDate' => date('Y-m-d', strtotime($op['UpdateTime'])),         // 日期
                        'sLikeNum' => $likeNum               //点赞个数
                    );
                }
            }
        }else {
            $result['code'] = 1;
            $result['msg'] = '参数不全';
        }

        return $this->showMsg($result, true);
    }

    /**
     * 分析师在线咨询提交
     */
    public function sendquestionAction()
    {
        $result = array(
            'code' => 0,
            'msg' => 'ok',
            'data' => array()
        );

        $analystID = intval($this->getParam('analystID'));
        $content = $this->getParam('content');
        $userName = $this->getParam('userName');
        $mobile = $this->getParam('mobile');

        if(!empty($analystID) && !empty($content) && !empty($userName) && !empty($mobile)) {
            $data = array(
                'iAnalystId' => $analystID,
                'sContent' => $content,
                'sUserName' => $userName,
                'sPhone' => $mobile,
                'iCreateTime' => time(),
                'iUpdateTime' => time()
            );

            Model_Faq::addData($data);

        }else {
            $result['code'] = 1;
            $result['msg'] = '参数不全';
        }


        return $this->showMsg($result, true);
    }

    /**
     * 分析师增加客户
     */
    public function saveCustomerAction()
    {
        $result = array(
            'code' =>  0,
            'msg' => 'ok',
            'data' => array()
        );

        $analystID = intval($this->getParam('analystID'));
        $sAppIdfa = $this->getParam('uniqueID');

        if(!empty($analystID) && !empty($sAppIdfa)) {
            $data = array(
                'iAnalystId' => $analystID,
                'sAppIdfa' => $sAppIdfa
            );

            $customer = Model_AnalystFollowed::getRow(array('where' => $data));

            if(empty($customer)) {
                $data['iCreateTime'] = time();
                $data['iUpdateTime'] = time();

                Model_AnalystFollowed::addData($data);
            }else {
                $result['code'] = 1;
                $result['msg'] = '该客户已经存在';
            }

        }else {
            $result['code'] = 1;
            $result['msg'] = '参数不全';
        }

        return $this->showMsg($result, true);
    }

    /**
     * 对分析师评测点赞
     */
    public function zanAction()
    {
        $result = array(
            'code' =>  0,
            'msg' => 'ok',
            'data' => array()
        );

        $analystId = intval($this->getParam('analystID'));
        $optionId = intval($this->getParam('commentID'));
        $userId = intval($this->getParam('userID'));

        if(!empty($analystId) && !empty($optionId)) {
            $data = array(
                'iAnalystId' => $analystId,
                'iOptionId' => $optionId,
                'iCreateTime' => time(),
                'iUpdateTime' => time()
            );

            if(!empty($userId)) {
                $data['iUserId'] = $userId;
            }

            Model_AnalystOptionFollowed::addData($data);
        }else {
            $result['code'] = 1;
            $result['msg'] = '参数不全';
        }

        return $this->showMsg($result, true);
    }

    /**
     * 对分析师点赞
     */
    public function fansAction()
    {
        $result = array(
            'code' =>  0,
            'msg' => 'ok',
            'data' => array()
        );

        $analystId = intval($this->getParam('analystID'));
        $userId = intval($this->getParam('userID'));

        if(!empty($analystId)) {
            $data = array(
                'iAnalystId' => $analystId,
                'iCreateTime' => time(),
                'iUpdateTime' => time()
            );

            if(!empty($userId)) {
                $data['iUserId'] = $userId;
            }

            Model_AnalystFans::addData($data);
        }else {
            $result['code'] = 1;
            $result['msg'] = '参数不全';
        }

        return $this->showMsg($result, true);
    }

}