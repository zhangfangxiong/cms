<?php

/**
 * Created by PhpStorm.
 * User: chasel
 * Date: 2015/3/20
 * Time: 15:40
 */
class Controller_Mapi_User extends Controller_Mapi_Base
{
    /**
     * 我的收藏
     */
    public function favlistAction()
    {
        $userID = intval($this->getParam('userID'));
        $token = $this->getParam('token');

        $tokenChange = Model_Token::checkToken($token, $userID);
        if (!$tokenChange) {
            $result['code'] = 1;
            $result['msg'] = '您还没有登录！';
            return $this->showMsg($result, true);
        }

        $result = array(
            'code' => 0,
            'msg' => 'ok',
            'data' => array()
        );

        if(!empty($userID) && !empty($token)) {
            $change = Model_Token::checkToken($token, $userID);
            if($change) {
                $where = array(
                    'iUserID' => $userID,
                    'iStatus' => 1
                );
                $favs = Model_Favorite::getAll(['where' => $where]);
                if(!empty($favs)) {
                    foreach($favs as $fav) {
                        $result['data'][] = $fav['iContentID'];
                    }
                }
            }else {
                $result['code'] = 1;
                $result['msg'] = 'token验证错误';
            }
        }else {
            $result['code'] = 1;
            $result['msg'] = '参数不全';
        }

        return $this->showMsg($result, true);
    }

    /**
     * 我的收藏楼盘
     */
    public function favbuildingsAction()
    {
        $userID = intval($this->getParam('userID'));
        $token = $this->getParam('token');

        $tokenChange = Model_Token::checkToken($token, $userID);
        if (!$tokenChange) {
            $result['code'] = 1;
            $result['msg'] = '您还没有登录！';
            return $this->showMsg($result, true);
        }

        $iPage = intval($this->getParam('iPage'));
        $iRows = intval($this->getParam('iRows'));
        $iType = intval($this->getParam('type'));

        $result = array(
            'code' => 0,
            'msg' => 'ok',
            'data' => array()
        );

        if(!empty($userID) && !empty($token) && !empty($iType) && !empty($iPage) && !empty($iRows)) {

            $change = Model_Token::checkToken($token, $userID);
            if($change) {
                $result['data']['iTotalNum'] = 0;
                $result['data']['iPage'] = $iPage;
                $result['data']['iRows'] = $iRows;
                $result['data']['list'] = array();

                $favs = Model_Favorite::getByuID($userID, $iType, $iPage, $iRows);
                $targetIDs = array();

                if(!empty($favs['aList'])) {
                    foreach($favs['aList'] as $fav) {
                        $targetIDs[] = $fav['iContentID'];
                    }
                }

                $contents = array();
                if(!empty($targetIDs)) {
                    switch($iType) {
                        case 1://获取收藏的新房楼盘
                            $contents = Model_Loupan::getListByIds($targetIDs);
                            break;
                        case 2://获取收藏的二手房楼盘(暂无操作)
                            break;
                        case 3://获取收藏的评测报告
                            foreach($targetIDs as $id) {
                                $eva = Model_Evaluation::getDetail($id);

                                $analystImg = null;
                                $analystName = null;
                                if(!empty($eva)) {
                                    $buildingID = $eva['iUnitID'];
                                    $unit = Model_CricUnit::getRow(array('where'=>array('ID'=>$buildingID, 'State'=>1)));

                                    $aChapter = Model_Evaluation::getEvaluationInfo($buildingID, $this->isApp);

                                    if(!empty($unit)) {
                                        $buildingID = $unit['UnitID'];
                                        $unit = Model_Loupan::getRow(array('where'=>array('sMapUnitID'=>$buildingID, 'iStatus'=>1)));
                                    }

                                    $analystID = $eva['iAnalysisID'];
                                    $analyst = Model_Analysts::getDetail($analystID);
                                    if(!empty($unit) && !empty($analyst)) {
                                        $unit['analystsName'] = $analyst['AnalystsName'];
                                        $unit['analystHead'] = empty($analyst['AnalystHead']) ? Util_Uri::getDefaultImg(2) : Util_Uri::getCricViewURL($analyst['AnalystHead'], 120, 120, 0, 0, 1);
                                        $unit['iEvaluateID'] = $eva['iEvaluationID'];
                                        $unit['sCompletePageUrl'] = $aChapter['sCompletePageUrl'];
                                        $unit['chapter'] = $aChapter['chapter'];
                                        $contents[] = $unit;
                                    }
                                }
                            }
                            break;
                        default:
                            break;
                    }
                }

                $result['data']['iTotalNum'] = $favs['iTotal'];

                if(!empty($contents)) {
                    $color = Yaf_G::getConf('recommend_color',null,'mapi');
                    $recommendText = Yaf_G::getConf('recommend_text',null,'mapi');
                    $recommend = Yaf_G::getConf('recommend',null,'mapi');
                    foreach($contents as $lp) {
                        $sEJScore = $lp['sEJScore'];

                        $lpDetail = Model_LoupanDetail::getDetail($lp['iAutoID']);

                        $sPrice = empty($lp['iBidingPrice']) ? '待定' : number_format($lp['iBidingPrice']);
                        $sUnit = empty($lp['iBidingPrice']) ? '' : '元/平';

                        $data = array(
                            'iBuildingID' => $lp['iAutoID'],    // 楼盘ID
                            'sImgUrl' => empty($lp['sZongPinPic']) ? Util_Uri::getDefaultImg() : Util_Uri::getCricViewURL($lp['sZongPinPic'], 213, 160),
                            'sName' => $lp['sName'],
                            'sPrice' => $sPrice,
                            'sUnit' => $sUnit,
                            'sRegion' => $lp['sRegionName']. '-'. $lp['sZoneName'],
                            'sRate' => $lp['iTotalScore'],
                            'sFlagText' => Model_Loupan::getRecommendText($sEJScore),
                            'sFlagColor' => Model_Loupan::getRecommendColor($sEJScore),
                            'iFlagID' => Model_Loupan::getRecommend($sEJScore),
                            'sListName' => '',      // 榜单
                            'sAdvantages' => empty($lpDetail) ? '' : $lpDetail['sAdvantageLabel']
                        );
                        if(3 == $iType) {
                            $data['analystsName'] = $lp['analystsName'];
                            $data['analystHead'] = $lp['analystHead'];
                            $data['iEvaluationID'] = $lp['iEvaluateID'];
                            $data['sCompletePageUrl'] = $lp['sCompletePageUrl'];
                            $data['iCssNum'] = Model_Evaluation::_GetCssBysRateNum($lp['iTotalScore']);
                            $data['chapter'] = $lp['chapter'];
                        }

                        $result['data']['list'][] = $data;
                    }
                }
            }else {
                $result['code'] = 1;
                $result['msg'] = 'token验证错误';
            }
        }else {
            $result['code'] = 1;
            $result['msg'] = '参数不全';
        }

        return $this->showMsg($result, true);
    }

    /**
     * 我的通知
     */
    public function mynewsAction()
    {
        $userID = intval($this->getParam('userID'));
        $token = $this->getParam('token');

        $result = array(
            'code' => 0,
            'msg' => 'ok',
            'data' => array(
                'iTotalNum' => 1,
                'iPage' => 1,
                'iRows' => 1,
                'list' => array(
                    [
                        'iNewsID' => 1235,    // 楼盘ID
                        'sText' => '别让孩子活在雾霾之中上海空气清新楼盘推荐',
                        'sTime' => '2015-03-05',
                        'sImgUrl' => Util_Uri::getCricViewURL('CRICdcd6270e732b600c300e4cc77f45fed0', 400, 300),
                        'sJumpToUrl' => 'http://wap.fangjiadp.com/news/1',
                    ]
                )
            )
        );

        return $this->showMsg($result, true);
    }

    /**
     * 分析师在线资讯提交
     */
    public function sendquestionAction()
    {
        $userID = intval($this->getParam('userID'));
        $token = $this->getParam('token');

        $tokenChange = Model_Token::checkToken($token, $userID);
        if (!$tokenChange) {
            $result['code'] = 1;
            $result['msg'] = '您还没有登录！';
            return $this->showMsg($result, true);
        }

        $type = intval($this->getParam('type'));
        $buildingID = intval($this->getParam('buildingID'));

        $result = array(
            'code' => 0,
            'msg' => 'ok',
            'data' => array(
                'iSuccess' => 1,
                'sMessage' => '添加成功'
            )
        );
        return $this->showMsg($result, true);
    }

    /**
     * 添加收藏楼盘
     */
    public function addfavAction()
    {
        $userID = intval($this->getParam('userID'));
        $token = $this->getParam('token');

        $tokenChange = Model_Token::checkToken($token, $userID);
        if (!$tokenChange) {
            $result['code'] = 1;
            $result['msg'] = '您还没有登录！';
            return $this->showMsg($result, true);
        }

        $type = intval($this->getParam('type'));
        $targetID = intval($this->getParam('targetID'));

        $result = array(
            'code' => 0,
            'msg' => 'ok',
            'data' => array()
        );

        if(!empty($userID) && !empty($token) && !empty($type) && !empty($targetID) ) {
            $tokenChange = Model_Token::checkToken($token, $userID);
            if($tokenChange) {
                $data = array(
                    'iUserID' => $userID,
                    'iContentID' => $targetID,
                    'iType' => $type,
                    'iCreateTime' => time(),
                    'iUpdateTime' => time()
                );

                if(Model_Favorite::exist($targetID, $userID, $type)) {
                    $result['code'] = 1;
                    $result['msg'] = '已经收藏过本内容';
                }else {
                    Model_Favorite::addData($data);
                }

            }else {
                $result['code'] = 1;
                $result['msg'] = 'token验证错误';
            }
        }else {
            $result['code'] = 1;
            $result['msg'] = '参数不全';
        }

        return $this->showMsg($result, true);
    }

    /**
     * 取消收藏楼盘
     */
    public function removefavAction()
    {
        $userID = intval($this->getParam('userID'));
        $token = $this->getParam('token');

        $tokenChange = Model_Token::checkToken($token, $userID);
        if (!$tokenChange) {
            $result['code'] = 1;
            $result['msg'] = '您还没有登录！';
            return $this->showMsg($result, true);
        }

        $type = intval($this->getParam('type'));
        $targetID = intval($this->getParam('targetID'));

        $result = array(
            'code' => 0,
            'msg' => 'ok',
            'data' => array()
        );

        if(!empty($userID) && !empty($token) && !empty($type) && !empty($targetID) ) {
            $tokenChange = Model_Token::checkToken($token, $userID);
            if($tokenChange) {
                Model_Favorite::delFavorite($targetID, $userID, $type);
            }else {
                $result['code'] = 1;
                $result['msg'] = 'token验证错误';
            }
        }else {
            $result['code'] = 1;
            $result['msg'] = '参数不全';
        }

        return $this->showMsg($result, true);
    }

    /**
     * 取消全部收藏楼盘
     */
    public function removeallfavAction()
    {
        $userID = intval($this->getParam('userID'));
        $token = $this->getParam('token');

        $tokenChange = Model_Token::checkToken($token, $userID);
        if (!$tokenChange) {
            $result['code'] = 1;
            $result['msg'] = '您还没有登录！';
            return $this->showMsg($result, true);
        }

        $type = intval($this->getParam('type'));

        $result = array(
            'code' => 0,
            'msg' => 'ok',
            'data' => array()
        );

        if(!empty($userID) && !empty($token) && !empty($type) ) {
            $tokenChange = Model_Token::checkToken($token, $userID);
            if($tokenChange) {
                Model_Favorite::delAllFavorites($userID, $type);
            }else {
                $result['code'] = 1;
                $result['msg'] = 'token验证错误';
            }
        }else {
            $result['code'] = 1;
            $result['msg'] = '参数不全';
        }

        return $this->showMsg($result, true);
    }

    /**
     * 修改密码
     */
    public function changepwdAction()
    {
        $userID = intval($this->getParam('userid'));
        $token = $this->getParam('token');

        $tokenChange = Model_Token::checkToken($token, $userID);
        if (!$tokenChange) {
            $result['code'] = 1;
            $result['msg'] = '您还没有登录！';
            return $this->showMsg($result, true);
        }

        $newPwd = $this->getParam('newPwd');
        $verifyCode = $this->getParam('verifyCode');

        $result = array(
            'code' => 0,
            'msg' => 'ok',
            'data' => array()
        );

        if(!empty($userID) && !empty($token) &&!empty($verifyCode) &&!empty($newPwd) ) {
            $tokenChange = Model_Token::checkToken($token, $userID);

            if($tokenChange) {
                $oCricCricUser = new Model_CricUser();
                $user = $oCricCricUser->getUserById($userID);
                if(!empty($user)) {
                    $userName = $user['UserName'];
                    $phone = $user['Phone'];

                    $pre = Yaf_G::getConf('code_pre',null,'mapi');
                    $keyByName = $pre. '_'. $userName. '_'. 4;
                    $keyByPhone = $pre. '_'. $phone. '_'. 4;

                    $cache = Util_Common::getCache();
                    $codeByName = $cache->get($keyByName);
                    $codeByPhone = $cache->get($keyByPhone);

                    $newPwd = Model_CricUser::genPasswd($userName, $newPwd);

                    if( !empty($codeByName) && $codeByName == $verifyCode) {
                        if($newPwd) {
                            $oCricCricUser->changePwdByID($userID, $newPwd);
                            $cache->delete($keyByName);
                        }else {
                            $result['code'] = 1;
                            $result['msg'] = '密码生成失败';
                        }
                    }else if(!empty($codeByPhone) && $codeByPhone == $verifyCode){
                        if($newPwd) {
                            $oCricCricUser->changePwdByID($userID, $newPwd);
                            $cache->delete($keyByPhone);
                        }else {
                            $result['code'] = 1;
                            $result['msg'] = '密码生成失败';
                        }
                    }else {
                        $result['code'] = 1;
                        $result['msg'] = '验证码错误或者已经过期';
                    }
                }else {
                    $result['code'] = 1;
                    $result['msg'] = '用户不存在';
                }
            }else {
                $result['code'] = 1;
                $result['msg'] = 'token验证错误';
            }
        }else {
            $result['code'] = 1;
            $result['msg'] = '参数不全';
        }

        return $this->showMsg($result, true);
    }

    /**
     * 找回密码
     */
    public function forgetpwdAction()
    {
        $mobile = $this->getParam('mobile');
        $newPwd = $this->getParam('newPwd');
        $verifyCode = $this->getParam('verifyCode');

        $result = array(
            'code' => 0,
            'msg' => 'ok',
            'data' => array()
        );

        if(!empty($mobile) && !empty($newPwd) &&!empty($verifyCode) ) {
            $oCricCricUser = new Model_CricUser();

            $pre = Yaf_G::getConf('code_pre',null,'mapi');
            $key = $pre. '_'. $mobile. '_'. 2;
            $cache = Util_Common::getCache();
            $existCode = $cache->get($key);

            if( !empty($existCode) && $existCode == $verifyCode) {
                $newPwd = Model_CricUser::genPasswd($mobile, $newPwd);
                if($newPwd) {
                    $oCricCricUser->changePwd($mobile, $newPwd);
                    $cache->delete($key);
                }else {
                    $result['code'] = 1;
                    $result['msg'] = '密码生成失败';
                }
            }else {
                $result['code'] = 1;
                $result['msg'] = '验证码错误或者已经过期';
            }
        }else {
            $result['code'] = 1;
            $result['msg'] = '参数不全';
        }
        return $this->showMsg($result, true);
    }

    /**
     * 设置用户头像
     */
    public function setavatarAction()
    {
        $userID = intval($this->getParam('userID'));
        $token = $this->getParam('token');
        $avatar = $this->getParam('avatar');

        $tokenChange = Model_Token::checkToken($token, $userID);
        if (!$tokenChange) {
            $result['code'] = 1;
            $result['msg'] = '您还没有登录！';
            return $this->showMsg($result, true);
        }

        $result = array(
            'code' => 0,
            'msg' => 'ok',
            'data' => array()
        );

        if(!empty($userID) && !empty($token) && !empty($avatar) ) {
            $tokenChange = Model_Token::checkToken($token, $userID);
            if($tokenChange) {
                $mUser = new Model_CricUser();
                $mUser->setAvatar($userID, $avatar);
            }else {
                $result['code'] = 1;
                $result['msg'] = 'token验证错误';
            }
        }else {
            $result['code'] = 1;
            $result['msg'] = '参数不全';
        }

        return $this->showMsg($result, true);
    }

}