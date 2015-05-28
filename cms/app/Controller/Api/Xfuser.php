<?php

/**
 * Created by PhpStorm.
 * User: ddc
 * Date: 2015/4/29
 * 房价点评新房API
 */
class Controller_API_Xfuser extends Controller_Api_Base
{
    /**
     * 检查用户有效性
     */
    public function checkUserAction(){
        $mobile = $this->getParam('mobile');
        if(empty($mobile)){
            return null;
        }
        $oCricCricUser = new Model_CricUser();
        $aList = $oCricCricUser->checkUser($mobile);
        return $this->showMsg($aList, true);
    }

    /**
     * 获取用户信息
     */
    public function getUserInfoAction(){
        $userName = $this->getParam('userName');
        if(empty($userName)){
            return null;
        }
        $oCricCricUser = new Model_CricUser();
        $aList = $oCricCricUser->getUserInfo($userName);
        return $this->showMsg($aList, true);
    }

    /**
     * 更新用户信息
     */
    public function updateUserInfoAction(){
        $array = array(
            'mobile'=>$this->getParam('userName'),
            'realName'=>$this->getParam('realName'),
            'sex'=>$this->getParam('sex'),
            'budget'=>$this->getParam('budget'),
            'buyInfo'=>$this->getParam('buyInfo'),
            'birthday'=>$this->getParam('birthday'),
        );

        if(empty($array)) {
            return null;
        }
        $oCricCricUser = new Model_CricUser();
        $aList = $oCricCricUser->updateUserInfo($array);
        return $this->showMsg($aList, true);
    }
}