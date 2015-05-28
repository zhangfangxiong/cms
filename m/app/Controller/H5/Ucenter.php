<?php

/**
 * Created by PhpStorm.
 * User: len
 * Date: 2015/4/10
 * Time: 11:33
 */
class Controller_H5_Ucenter extends Controller_Base
{
    static public $aEva = [
        1 => [
            'class' => 'rushou',
            'name'  => '尽快入手',
        ],
        2 => [
            'class' => 'tuijian',
            'name'  => '推荐购买',
        ],
        3 => [
            'class' => 'jinshen',
            'name'  => '谨慎购买',
        ],
        4 => [
            'class' => 'guanwang',
            'name'  => '持币观望',
        ]
    ];

    /**
     * 执行Action前执行
     * @see Yaf_Controller::actionBefore()
     */
    public function actionBefore()
    {
        parent::actionBefore();
    }

    /**
     * 执行Action后的操作
     * @see Yaf_Controller::actionAfter()
     */
    public function actionAfter()
    {
        parent::actionAfter();
    }

    /**
     * 首页地址
     */
    public function indexAction()
    {
        header('location:more');
    }

    /**
     * 注册
     */
    public function pRegisterAction()
    {
        if ($this->isPost()) {
            $aParam['mobile'] = addslashes($this->getParam('mobile'));
            $aParam['code'] = addslashes($this->getParam('code'));
            $aParam['pwd'] = addslashes($this->getParam('pwd'));
            $aCity = $this->getCurrCity();
            $aParam['city'] = addslashes($aCity['sPinyin']);
            if (empty($aParam['mobile'])) {
                return $this->showMsg('请填写手机号', false);
            }
            if (empty($aParam['code'])) {
                return $this->showMsg('请填写验证码', false);
            }
            if (empty($aParam['pwd'])) {
                return $this->showMsg('请填写密码', false);
            }
            $aData = Sdk_Cms_Ucenter::register($aParam);
            if (!$aData['status']) {
                return $this->showMsg($aData['data']['msg'], false);
            } else {
                //注册成功
                return $this->showMsg($aData, true);
            }
        }
        //$verifycodeUrl = 'http://'.Yaf_G::getConf('touchweb', 'domain').'/h5/ucenter/verifycode';
        $verifycodeUrl = 'verifycode';
        $this->aMeta['title'] .= '注册';
        $this->assign('verifycodeUrl', $verifycodeUrl);
    }

    /**
     * 登陆
     */
    public function pLoginAction()
    {
        $has_login = $this->checkHasLogin();
        if ($has_login) {
            header('location:more');
        }
        $sLocation = $this->getParam('location');
        if ($this->isPost()) {
            $aParam['mobile'] = addslashes($this->getParam('mobile'));
            $aParam['pwd'] = addslashes($this->getParam('pwd'));
            if (empty($aParam['mobile'])) {
                return $this->showMsg('请填写手机号', false);
            }
            if (empty($aParam['pwd'])) {
                return $this->showMsg('请填写密码', false);
            }
            $aData = Sdk_Cms_Ucenter::login($aParam);
            if (!$aData['status']) {
                return $this->showMsg($aData['data']['msg'], false);
            } else {
                Util_Cookie::set('touchWebUserInfo', json_encode($aData));//用户信息存入cookie
                //登陆成功
                return $this->showMsg($aData, true);
            }
        }
        if ($sLocation) {
            $this->assign('sLocation', urldecode($sLocation));
        }
        $this->aMeta['title'] .= '登陆';
    }

    /**
     * 登出
     */
    public function logoutAction()
    {
        $has_login = $this->checkHasLogin();
        if ($has_login) {
            $has_login = json_decode($has_login, true);
            Util_Cookie::delete('touchWebUserInfo');
            $aParam['userID'] = $has_login['data']['iUserID'];
            $aParam['token'] = $has_login['data']['sToken'];
            $aData = Sdk_Cms_Ucenter::logout($aParam);
            if ($aData['data']['iSuccess']) {
                return $this->showMsg('登出成功', true);
            }
        } else {
            return $this->showMsg('你当前未登陆，非法操作', false);
        }
    }

    /**
     * 忘记密码
     */
    public function pForgetPasswordAction()
    {
        if ($this->isPost()) {
            $aParam['mobile'] = addslashes($this->getParam('mobile'));
            $aParam['verifyCode'] = addslashes($this->getParam('verifyCode'));
            $aParam['newPwd'] = addslashes($this->getParam('newPwd'));
            if (empty($aParam['mobile'])) {
                return $this->showMsg('请填写手机号', false);
            }
            if (empty($aParam['verifyCode'])) {
                return $this->showMsg('请填写验证码', false);
            }
            if (empty($aParam['newPwd'])) {
                return $this->showMsg('请填写密码', false);
            }
            $aData = Sdk_Cms_Ucenter::forgetpwd($aParam);
            if (!$aData['status']) {
                return $this->showMsg($aData['data']['msg'], false);
            } else {
                //修改成功
                return $this->showMsg($aData, true);
            }
        }
        //$verifycodeUrl = 'http://'.Yaf_G::getConf('touchweb', 'domain').'/h5/ucenter/verifycode';
        $verifycodeUrl = 'verifycode';
        $this->assign('verifycodeUrl', $verifycodeUrl);
        $this->aMeta['title'] .= '忘记密码';
    }

    /**
     * 修改密码
     */
    public function pamendAction()
    {
        $has_login = $this->checkHasLogin();
        if ($has_login) {
            $has_login = json_decode($has_login, true);
            if ($this->isPost()) {
                $aParam['userid'] = $has_login['data']['iUserID'];
                $aParam['token'] = $has_login['data']['sToken'];
                $aParam['verifyCode'] = addslashes($this->getParam('verifyCode'));
                $aParam['newPwd'] = addslashes($this->getParam('newPwd'));
                if (empty($aParam['verifyCode'])) {
                    return $this->showMsg('请填写验证码', false);
                }
                if (empty($aParam['newPwd'])) {
                    return $this->showMsg('请填写密码', false);
                }
                $aData = Sdk_Cms_Ucenter::changepwd($aParam);
                if (!$aData['status']) {
                    return $this->showMsg($aData['data']['msg'], false);
                } else {
                    //修改成功
                    return $this->showMsg($aData, true);
                }
            }
            $verifycodeUrl = 'verifycode';
            $phoneNum = $has_login['data']['sUserName'];
            $this->assign('phoneNum', $phoneNum);
            $this->assign('verifycodeUrl', $verifycodeUrl);
        } else {
            return $this->showMsg('你当前未登陆，非法操作', false);
        }
        $this->aMeta['title'] .= '忘记密码';
    }

    /**
     * 用户协议
     */
    public function pagreementAction()
    {
        $this->aMeta['title'] .= '用户协议';
    }

    /**
     * 我的收藏的新房
     */
    public function myfavoritesAction()
    {
        $iRows = 10;//每页显示的数目
        $userInfo = $this->checkHasLogin();
        if ($userInfo) {
            $userInfo = json_decode($userInfo, true);
            $aParam['userID'] = $userInfo['data']['iUserID'];
            $aParam['token'] = $userInfo['data']['sToken'];
            $aParam['type'] = 1;
            $aParam['iPage'] = $this->getParam('iPage') ? addslashes($this->getParam('iPage')) : 1;
            $aParam['iRows'] = $iRows;
            $aData = Sdk_Cms_Ucenter::favbuildings($aParam);

            if (!$aData['status']) {
                return $this->showMsg($aData['data']['msg'], false);
            } else {
                $this->assign('aData', $aData);
                $this->assign('aEva', self::$aEva);
            }
        } else {
            return $this->showMsg('请先登陆', false);
        }
        $this->aMeta['title'] .= '我的收藏-新房';
    }

    /**
     * 我的收藏的新房(翻页)
     */
    public function myfavoriteslistAction()
    {
        $iRows = 10;//每页显示的数目
        $userInfo = $this->checkHasLogin();
        if ($userInfo) {
            $userInfo = json_decode($userInfo, true);
            $aParam['userID'] = $userInfo['data']['iUserID'];
            $aParam['token'] = $userInfo['data']['sToken'];
            $aParam['type'] = 1;
            $aParam['iPage'] = $this->getParam('iPage') ? addslashes($this->getParam('iPage')) : 1;
            $aParam['iRows'] = $iRows;
            $aData = Sdk_Cms_Ucenter::favbuildings($aParam);

            if (!$aData['status']) {
                return $this->showMsg($aData['data']['msg'], false);
            } else {
                $aData["aFlagType"]=self::$aEva;
                return $this->showMsg($aData, false);
            }
        } else {
            return $this->showMsg('请先登陆', false);
        }
    }

    /**
     * 我收藏的评测
     */
    public function myevaluationAction()
    {
        $iRows = 10;//每页显示的数目
        $userInfo = $this->checkHasLogin();
        if ($userInfo) {
            $userInfo = json_decode($userInfo, true);
            $aParam['userID'] = $userInfo['data']['iUserID'];
            $aParam['token'] = $userInfo['data']['sToken'];
            $aParam['type'] = 3;
            $aParam['iPage'] = $this->getParam('iPage') ? addslashes($this->getParam('iPage')) : 1;
            $aParam['iRows'] = $iRows;
            $aData = Sdk_Cms_Ucenter::favbuildings($aParam);
            if (!$aData['status']) {
                return $this->showMsg($aData['data']['msg'], false);
            } else {
                $aLouInfo = array(
                    '1' => array(
                        'class' => 'hx_analyze',
                        'name'=>'户型分析',
                    ),
                    '2' => array(
                        'class' => 'zx_stander',
                        'name'=>'装修标准',
                    ),
                    '3' => array(
                        'class' => 'sq_sever',
                        'name'=>'社区服务',
                    ),
                    '4' => array(
                        'class' => 'wy_sever',
                        'name'=>'物业服务',
                    ),
                    '6' => array(
                        'class' => 'sq_Supporting',
                        'name'=>'生活配套',
                    ),
                    '7' => array(
                        'class' => 'bad_factor',
                        'name'=>'不利因素',
                    ),
                );
                $this->assign('aLouInfo',$aLouInfo);
                $this->assign('aData', $aData);
            }
        } else {
            return $this->showMsg('请先登陆', false);
        }
        $this->aMeta['title'] .= '我的收藏-评测';
    }

    /**
     * 我收藏的评测(翻页)
     */
    public function myevaluationlistAction()
    {
        $iRows = 10;//每页显示的数目
        $userInfo = $this->checkHasLogin();
        if ($userInfo) {
            $userInfo = json_decode($userInfo, true);
            $aParam['userID'] = $userInfo['data']['iUserID'];
            $aParam['token'] = $userInfo['data']['sToken'];
            $aParam['type'] = 3;
            $aParam['iPage'] = $this->getParam('iPage') ? addslashes($this->getParam('iPage')) : 1;
            /*$aParam['iPage'] = 1;*/
            $aParam['iRows'] = $iRows;
            $aData = Sdk_Cms_Ucenter::favbuildings($aParam);

            if (!$aData['status']) {
                return $this->showMsg($aData['data']['msg'], false);
            } else {
                $aLouInfo = array(
                    '1' => array(
                        'class' => 'hx_analyze',
                        'name'=>'户型分析',
                    ),
                    '2' => array(
                        'class' => 'zx_stander',
                        'name'=>'装修标准',
                    ),
                    '3' => array(
                        'class' => 'sq_sever',
                        'name'=>'社区服务',
                    ),
                    '4' => array(
                        'class' => 'wy_sever',
                        'name'=>'物业服务',
                    ),
                    '6' => array(
                        'class' => 'sq_Supporting',
                        'name'=>'生活配套',
                    ),
                    '7' => array(
                        'class' => 'bad_factor',
                        'name'=>'不利因素',
                    ),
                );
                $aData['aLouInfo']=$aLouInfo;

                return $this->showMsg($aData, false);
            }
        } else {
            return $this->showMsg('请先登陆', false);
        }
    }

    /**
     * 个人中心
     */
    public function moreAction()
    {
        $userInfo = $this->checkHasLogin();
        if ($userInfo) {
            $userInfo = json_decode($userInfo, true);
            $this->assign('userInfo', $userInfo);
        }
        $this->aMeta['title'] .= '个人中心';
    }

    /**
     * 关于我们
     */
    public function aboutusAction()
    {
        $this->aMeta['title'] .= '关于我们';
    }

    /**
     * 收藏楼盘
     */
    public function addLouAction()
    {
        $userInfo = $this->checkHasLogin();
        if ($userInfo) {
            $userInfo = json_decode($userInfo, true);
            $aParam['userID'] = $userInfo['data']['iUserID'];
            $aParam['token'] = $userInfo['data']['sToken'];
            $aParam['type'] = 1;
            $aParam['targetID'] = $this->getParam('targetID') ? addslashes($this->getParam('targetID')) : '';
            $aData = Sdk_Cms_Ucenter::addfav($aParam);
            if (!$aData['status']) {
                return $this->showMsg($aData['data']['msg'], false);
            } else {
                //修改成功
                return $this->showMsg($aData, true);
            }
        } else {
            return $this->showMsg('请先登陆', false);
        }
    }

    /**
     *收藏评测
     */
    public function addEvaluationAction()
    {
        $userInfo = $this->checkHasLogin();
        if ($userInfo) {
            $userInfo = json_decode($userInfo, true);
            $aParam['userID'] = $userInfo['data']['iUserID'];
            $aParam['token'] = $userInfo['data']['sToken'];
            $aParam['type'] = 3;
            $aParam['targetID'] = $this->getParam('targetID') ? addslashes($this->getParam('targetID')) : '';
            $aData = Sdk_Cms_Ucenter::addfav($aParam);
            if (!$aData['status']) {
                return $this->showMsg($aData['data']['msg'], false);
            } else {
                //修改成功
                return $this->showMsg($aData, true);
            }
        } else {
            return $this->showMsg('请先登陆', false);
        }
    }

    /**
     * 获取楼盘列表
     */
    public function getLouListAction()
    {
        $aCity = $this->getCurrCity();
        $aParam['cityID'] = $aCity['iCodeID'];
        $aData = Sdk_Cms_Ucenter::louList($aParam);
        print_r($aData);
        die;
    }

    /**
     * 获取评测列表
     */
    public function getevaluationListAction()
    {
        $iRows = 10;
        $aCity = $this->getCurrCity();
        $aParam['cityID'] = 1;//$aCity['iCodeID'];
        $aParam['iPage'] = $this->getParam('iPage') ? addslashes($this->getParam('iPage')) : 1;
        $aParam['iRows'] = $iRows;
        $aData = Sdk_Cms_Ucenter::evaluationList($aParam);
        print_r($aData);
        die;
    }

    /**
     * 删除选中收藏楼盘
     */
    public function removefavAction()
    {
        $userInfo = $this->checkHasLogin();
        if ($userInfo) {
            $userInfo = json_decode($userInfo, true);
            $aParam['userID'] = $userInfo['data']['iUserID'];
            $aParam['token'] = $userInfo['data']['sToken'];
            $aParam['type'] = 1;
            $aParam['targetID'] = $this->getParam('targetID') ? addslashes($this->getParam('targetID')) : '';
            if (!$aParam['targetID']) {
                return $this->showMsg('请选择要删除的楼盘', false);
            }
            $aTargetIDs = explode(',', $aParam['targetID']);
            foreach ($aTargetIDs as $key => $value) {
                $aParam['targetID'] = $value;
                $aData[] = Sdk_Cms_Ucenter::removefav($aParam);
            }
            return $this->showMsg($aData, true);
        } else {
            return $this->showMsg('请先登陆', false);
        }
    }

    /**
     * 删除所有收藏楼盘
     */
    public function removeallfavAction()
    {
        $userInfo = $this->checkHasLogin();
        if ($userInfo) {
            $userInfo = json_decode($userInfo, true);
            $aParam['userID'] = $userInfo['data']['iUserID'];
            $aParam['token'] = $userInfo['data']['sToken'];
            $aParam['type'] = 1;
            $aData = Sdk_Cms_Ucenter::removeallfav($aParam);
            if (!$aData['status']) {
                return $this->showMsg($aData['data']['msg'], false);
            } else {
                //修改成功
                return $this->showMsg($aData, true);
            }
        } else {
            return $this->showMsg('请先登陆', false);
        }
    }

    /**
     * 删除选中收藏评测
     */
    public function removeEvaluationAction()
    {
        $userInfo = $this->checkHasLogin();
        if ($userInfo) {
            $userInfo = json_decode($userInfo, true);
            $aParam['userID'] = $userInfo['data']['iUserID'];
            $aParam['token'] = $userInfo['data']['sToken'];
            $aParam['type'] = 3;
            $aParam['targetID'] = $this->getParam('targetID') ? addslashes($this->getParam('targetID')) : '';
            if (!$aParam['targetID']) {
                return $this->showMsg('请选择要删除的评测', false);
            }
            $aTargetIDs = explode(',', $aParam['targetID']);
            foreach ($aTargetIDs as $key => $value) {
                $aParam['targetID'] = $value;
                $aData[] = Sdk_Cms_Ucenter::removefav($aParam);
            }
            return $this->showMsg($aData, true);
        } else {
            return $this->showMsg('请先登陆', false);
        }
    }

    /**
     * 删除所有收藏评测
     */
    public function removeallEvaluationAction()
    {
        $userInfo = $this->checkHasLogin();
        if ($userInfo) {
            $userInfo = json_decode($userInfo, true);
            $aParam['userID'] = $userInfo['data']['iUserID'];
            $aParam['token'] = $userInfo['data']['sToken'];
            $aParam['type'] = 3;
            $aData = Sdk_Cms_Ucenter::removeallfav($aParam);
            if (!$aData['status']) {
                return $this->showMsg($aData['data']['msg'], false);
            } else {
                //修改成功
                return $this->showMsg($aData, true);
            }
        } else {
            return $this->showMsg('请先登陆', false);
        }
    }

    /**
     * 获取验证码
     * @return bool
     */
    public function verifycodeAction()
    {
        //访问URL为
        //http://touchweb.zfx.dev.ipo.com/h5/ucenter/verifycode?mobile=15026490504&type=1
        $aParam['mobile'] = addslashes($this->getParam('mobile'));
        $aParam['type'] = addslashes($this->getParam('type'));
        //var_dump($aParam);
        $aData = Sdk_Cms_Ucenter::verifycode($aParam);
        if ($aData['status'] == 1) {
            return $this->showMsg($aData, true);
        }
        return $this->showMsg($aData['data']['msg'], false);
    }

}