<?php
/**
 * Created by PhpStorm
 * Date: 2015/5/8
 * Time: 13:27
 */

class Controller_User_Index extends Controller_Base
{
    /**
     * 用户登录
     */
    public function loginAction() {
        $this->assign('userInfo', Util_Cookie::get('userInfo'));
        $this->assign('sToken', Util_Cookie::get('sToken'));
        $this->assign('pwd', Util_Cookie::get('pwd'));
        $this->assign('remb', Util_Cookie::get('remb'));
        //$this->assign('Phone', Util_Cookie::get('Phone'));
    }

    public function loginUserAction() {
        $mobile = $this->getParam('mobile');
        $pwd = $this->getParam('pwd');
        $remb = $this->getParam('remb');
        $result = Sdk_Cms_XfUser::login($mobile,$pwd);
        $this->showMsg($result['data'], true);
        if($result['status']){
            $date = 24*30*3600;  //一个月
            //获取用户个人详细信息写入COOKIE
            $userInfo = Sdk_Cms_XfUser::getUserInfo($result['data']['sUserName']);
            //$userInfo['data']['sToken'] = $result['data']['sToken'];
            Util_Cookie::set('userInfo',$userInfo['data'],time()+$date);
            //登陆成功后写入COOKIE
            Util_Cookie::set('sToken',$result['data']['sToken'],time()+$date);
            Util_Cookie::set('dpUserName',$userInfo['data']['UserName'],time()+$date);
            Util_Cookie::set('Phone',$userInfo['data']['Phone'],time()+$date);
            Util_Cookie::set('remb',$remb,time()+$date);
            if($remb==1){
                Util_Cookie::set('pwd',$pwd,time()+$date);
            }
        }
    }

    /**
     * 登出
     */
    public function logoutAction(){
        $userID = $this->getParam('userID');
        $token = $this->getParam('token');
        if(!empty($userID) && !empty($token)){
            $result = Sdk_Cms_XfUser::logout($userID,$token);
            $this->showMsg($result['data'], true);
            if($result['status']){
                //退出成功后删除cookie
                Util_Cookie::delete('userInfo');
                Util_Cookie::delete('sToken');
            }
        }
    }
    /**
     * 第三方QQ登陆
     */
    public function qqloginAction(){
        //获取登陆参数
        $userName = $this->getParam('userName');
        $sex = $this->getParam('sex');
        $sex = $sex == "男" ? "0" : "1";
        $imgurl = $this->getParam('imgurl');
        $city = $this->getParam('city');
        $openId = $this->getParam('openId');
        $userID = $this->getParam('userID');
        $result = Sdk_Cms_XfUser::qqlogin($userName,$sex,$city,$openId,'QQ',$userID);
        $this->showMsg($result['data'], true);
        if($result['status']){
            //登陆成功后写入COOKIE，同时跳转到SNS注册页面
            $date = 24*3600*30;  //一个月
            $userInfo = Sdk_Cms_XfUser::getUserInfo($result['data']['sUserName']);
            Util_Cookie::set('sToken',$result['data']['sToken'],time()+$date);
            Util_Cookie::set('userInfo',$userInfo['data'],time()+$date);
        }
    }

    /**
     * 新浪微博登陆
     */
    public function webloginAction(){
        $userName = $this->getParam('userName');
        print_r($userName);
        die;
    }

    public function weiboauthorizationAction(){

    }

    /**
     * 第三方登陆后的跳转页面
     */
    public function snsloginsuccessAction(){
        $userInfo = Util_Cookie::get('userInfo');
        if(!empty($userInfo)){
            $this->assign('userInfo', $userInfo);
        }
    }

    public function checkUserAction() {
        $mobile = $this->getParam('account');
        $result = Sdk_Cms_XfUser::checkUser($mobile);
        if($result['data']['count'] <= 0){
            $data['code'] = 1;
        }else{
            $data['code'] = 0;
        }
        $this->showMsg($data, true);
    }

    /**
     * 绑定手机页面加载
     */
    public function bindingphoneAction(){
        $userName = Util_Cookie::get('userInfo');
        if(isset($userName['UserName']) && !empty($userName['UserName'])){
            //用于前端页面判断class
            $userName['bangding'] = 'current';
            $this->assign('userInfo', $userName);
            $this->assign('sToken', Util_Cookie::get('sToken'));
        }else{
            header("location:/user/index/login");
        }
    }

    /**
     * 绑定手机功能实现
     */
    public function bangdingphoneAction(){
        $userName = Util_Cookie::get('userInfo');
        $mobile = $this->getParam('tel');
        $code = $this->getParam('code');
        if(isset($userName['Id']) && !empty($userName['Id'])){
            $result = Sdk_Cms_XfUser::bangdingphone($mobile,$code,$userName['Id']);
            $this->showMsg($result['data'], true);
            if($result['status']){
                //登陆成功后写入COOKIE
                $date = 24*3600*30;  //一个月
                $userInfo = Sdk_Cms_XfUser::getUserInfo($userName['UserName']);
                Util_Cookie::set('userInfo',$userInfo['data'],time()+$date);
            }
        }else{
            header("location:/user/index/login");
        }
    }

    /**
     * 用户注册
     */
    public function registerAction() {

    }

    public function registerUserAction() {
        $mobile = $this->getParam('mobile');
        $code = $this->getParam('code');
        $pwd = $this->getParam('pwd');
        $userID = $this->getParam('userID');
        //$userID = isset($userID)?$userID:'';
        $city = $this->sCurrCityCode;
        $result = Sdk_Cms_XfUser::register($mobile,$code,$pwd,$city,$userID);

        //if($result['status']){
            $this->showMsg($result['data'], true);
        //}else{
           // $this->showMsg(null, false);
        //}

    }

    /**
     * 用户中心
     */
    public function userinfoAction() {
        $userName = Util_Cookie::get('userInfo');
        if(!empty($userName['UserName'])){
            //用于前端页面判断class
            $userName['userinfo'] = 'current';
            $this->assign('userInfo', $userName);
            $this->assign('sToken', Util_Cookie::get('sToken'));
        }else{
            header("location:/user/index/login");
        }
    }

    /**
     * 保存个人中心信息
     */
    public function saveUserInfoAction(){
        $userName = Util_Cookie::get('userInfo');
        $realName = $this->getParam('realName');
        $sex = $this->getParam('sex');
        $budget = $this->getParam('budget');
        $buyInfo = $this->getParam('buyInfo');
        $birthday = $this->getParam('birthday');
        if(!empty($userName['UserName'])) {
            $result = Sdk_Cms_XfUser::updateUserInfo($userName['UserName'], $realName, $sex, $budget, $buyInfo, $birthday);
            if ($result['status']) {
                $this->showMsg($result['data'], true);
                //获取更新之后的用户个人详细信息写入COOKIE
                $date = 24*3600*30;  //一个月
                $newUserInfo = Sdk_Cms_XfUser::getUserInfo($userName['UserName']);
                Util_Cookie::set('userInfo',$newUserInfo['data'],time()+$date);
            } else {
                $this->showMsg(null, false);
            }
        }else{
            header("location:/user/index/login");
        }

    }

    /**
     * 忘记密码
     */
    public function forgetpasswordAction(){
        $mobile = $this->getParam('mobile');
        $code = $this->getParam('code');
        $pwd = $this->getParam('pwd');
        if(!empty($mobile) && !empty($code) && !empty($pwd)){
            $result = Sdk_Cms_XfUser::forgetpwd($mobile,$pwd,$code);
            $this->showMsg($result['data'], true);
            if($result['status']){
                //获取更新之后的用户个人详细信息写入COOKIE
                $date = 24*3600*30;  //一个月
                $newUserInfo = Sdk_Cms_XfUser::getUserInfo($mobile);
                Util_Cookie::set('userInfo',$newUserInfo['data'],time()+$date);
            //}else{
                //$this->showMsg(null, false);
            }
        }
    }

    /**
     * 修改密码页面加载
     */
    public function changepasswordAction(){
        //获取个人信息
        $userInfo = Util_Cookie::get('userInfo');
        if(!empty($userInfo)) {
            //用于前端页面判断class
            $userInfo['changepassword'] = 'current';
            $this->assign('userInfo', $userInfo);
            $this->assign('sToken', Util_Cookie::get('sToken'));
        }else{
            header("location:/user/index/login");
        }
    }

    /**
     * 修改密码功能实现
     */
    public function updatePasswordAction(){
        $userInfo = Util_Cookie::get('userInfo');
        $newpwd = $this->getParam('pwd');
        $code = $this->getParam('code');
        //判断是否登陆
        if (!empty($userInfo['Id']) && !empty($userInfo['sToken'])) {
            $result = Sdk_Cms_XfUser::changepwd($userInfo['Id'], $newpwd, $code, $userInfo['sToken']);
            $this->showMsg($result['data'], true);
            if ($result['status'] && !empty($result['data'])) {
                //获取更新之后的用户个人详细信息写入COOKIE
                $date = 24*3600*30;  //一个月
                $newUserInfo = Sdk_Cms_XfUser::getUserInfo($userInfo['UserName']);
                Util_Cookie::set('userInfo',$newUserInfo['data'],time()+$date);
            //} else {
                //$this->showMsg(null, false);
            }
        }else{
            header("location:/user/index/login");
        }

    }

    /**
     * 编辑头像页显示
     */
    public function editavatarAction(){
        //获取个人信息
        $userInfo = Util_Cookie::get('userInfo');
        if(!empty($userInfo)) {
            //用于前端页面判断class
            $userInfo['editavatar'] = 'current';
            $this->assign('userInfo', $userInfo);
            $this->assign('sToken', Util_Cookie::get('sToken'));
        }else{
            header("location:/user/index/login");
        }
    }

    /**
     * 编辑头像方法实现
     */
    public function editUserAvatarAction(){
        $userInfo = Util_Cookie::get('userInfo');
        $img = $this->getParam('img');
        $token = $this->getParam('token');
        $userid = intval($this->getParam('userid'));
        if(isset($img) && isset($token) && isset($userid)){
            $result = Sdk_Cms_XfUser::setavatar($userid,$token,$img);
            $this->showMsg($result['data'], true);
            if ($result['status']) {
                //获取更新之后的用户个人详细信息写入COOKIE
                $date = 24*3600*30;  //一个月
                $newUserInfo = Sdk_Cms_XfUser::getUserInfo($userInfo['UserName']);
                Util_Cookie::set('userInfo',$newUserInfo['data'],time()+$date);
            }
        }else{
            header("location:/user/index/login");
        }
    }


    /**
     * 获取验证码
     */
    public function getCodeAction(){
        $mobile = $this->getParam('mobile');
        $type = $this->getParam('type');
        $result = Sdk_Cms_XfUser::smsCode($mobile,$type);
        $this->showMsg($result, true);
    }

    /**
     * 检验验证码
     */
    public function checkCodeAction(){
        $mobile = trim($this->getParam('mobile'));
        $code = trim($this->getParam('code'));
        $type = trim($this->getParam('type'));
        $result = Sdk_Cms_XfUser::checkCode($mobile,$code,$type);
        if($result['status']){
            $this->showMsg($result['data'], true);
        }else{
            $this->showMsg(null, false);
        }

    }
}