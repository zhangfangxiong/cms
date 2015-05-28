<?php

/**
 * Created by PhpStorm.
 * User: chasel
 * Date: 2015/3/20
 * Time: 15:40
 */
class Controller_Mapi_Common extends Controller_Mapi_Base
{
    /**
     * 贴身购房顾问列表
     */
    public function guwenAction()
    {
        $result = [
            [
                'adviser_name' => '岑海波',  //顾问名称
                'adviser_id' =>  '4',    //顾问id
                'adviser_pic' =>  Util_Uri::getCricViewURL('CRIC3c1fb4130ccd32750a8ba8fe67b56709', 300, 400),  //顾问头像（图片）
                'description' => '我是青岛克而瑞分析师刘彩霞'    //顾问介绍
            ]
        ];
        return $this->showMsg($result, true);
    }

    /**
     * 设置购房顾问
     */
    public function setGuwenAction()
    {
        $adviser_id = $this->getParam('adviser_id');

        $result = [
            [
                'code' => 0,  //顾问名称
                'msg' =>  'OK'    //顾问id
            ]
        ];
        return $this->showMsg($result, true);
    }

    /**
     * 检查版本更新
     */
    public function checkVersionAction()
    {
        $result = array(
            'force' => 1,
            'latest_version' => '1.0.01',
            'update_info' => '房价点评，是由易居中国旗下克而瑞信息集团团队精心打造的一款第三方房价指导app',
            'download_url' => 'http://count.liqucn.com/d.php?id=364981&ArticleOS=android&content_url=http=>//www.liqucn.com/rj/364981.shtml&down_url=kpa.moc.ncuqil_0.1.2_ecirpgnisuoh.circ.moc/ouhgnehs/4102/daolpu/moc.ncuqil.elif//=>ptth&is_cp=0&market_place_id=11'
        );

        return $this->showMsg($result, true);
    }

    /**
     * 获取首页信息
     */
    public function maininfosAction()
    {
        $result = array(
            'code' =>  0,
            'msg' => 'ok',
            'data' => array()
        );

        $cityID = intval($this->getParam('cityID'));
        $analystID = intval($this->getParam('analystID'));
        $lat = floatval($this->getParam('lat'));
        $lon = floatval($this->getParam('lon'));

        if(!empty($cityID)) {//获取周边均价，如果给定坐标，则找离坐标最近板块的均价，否则返回城市均价
            $avePrice = 0;

            if(empty($lat) || empty($lon)) {
                $avePrice = Model_CricCity::getCityByID($cityID);
                $avePrice = $avePrice['Price'];
            }else {
                $districtPrice = Model_CricDistrict::getDistrictPrice($cityID, $lon, $lat);
                if(!empty($districtPrice) && !empty($districtPrice['Price'])) {
                    $avePrice = $districtPrice['Price'];
                }else {
                    $avePrice = Model_CricCity::getCityByID($cityID);
                    $avePrice = $avePrice['Price'];
                }
            }

            if(!$avePrice) {
                $avePrice = '待定';
            }

            $CityImgUrl = Model_City::getDetail($cityID);
            //$CityImgUrl = $CityImgUrl['sCityImg'];//获取城市背景图

            $result['data']['ideaPhoneNumber'] = Yaf_G::getConf('idea_tel_400',null,'mapi');
            $result['data']['iRefreshOn'] = 1;
            $buildingList = Model_Loupan::getLoupanByCID($cityID);//获取新开盘楼盘列表

            $result['data']['sAvePrice'] = $avePrice;
            $result['data']['sBuildNum'] = sizeof($buildingList);
            $result['data']['sCityImgUrl'] = '';//$CityImgUrl;
            $result['data']['sBuildName'] = array();
            if(!empty($buildingList)){
                foreach($buildingList as $building) {
                    $result['data']['sBuildName'][] = $building['sName'];
                }
            }

            if(empty($analystID)) {  //首次进入，如果没有分析师id就随机给一个
                $city = Model_City::getDetail($cityID);
                if($city) {
                    $cityName = $city['sCityName'];
                    $aAnalys = Model_Analysts::getAll(array('where' => array('City'=> $cityName, 'State'=> 1)));

                    if(!empty($aAnalys)) {
                        $analystIDs = null;
                        foreach($aAnalys as $aAnaly) {
                            $analystIDs .= ",'".$aAnaly['AnalystsID']. "'";
                        }

                        $analystIDs = substr($analystIDs, 1);

                        $opnions = Model_CricAnalystsOpinion::getAll(array('where'=>array('AnalystsID IN' => $analystIDs), 'group' => 'AnalystsID'));
                        if(!empty($opnions)) {
                            $len = sizeof($opnions);
                            $analystIndex = mt_rand(0, $len - 1);

                            $sAnalystID = $opnions[$analystIndex]['AnalystsID'];

                            $aAnaly = Model_Analysts::getRow(array('where'=>array('AnalystsID' => $sAnalystID, 'State' => 1)));

                            if($aAnaly) {
                                $analystID = $aAnaly['ID'];
                            }
                        }
                    }
                }
            }

            $analys = Model_Analysts::getAnalystByID($analystID);
            if(!empty($analys)) {
                $sTel400 = Yaf_G::getConf('tel_400',null,'mapi');
                if(!empty($analys['PhoneRegion'])) {
                    $sTel400 .=','.$analys['PhoneRegion'];
                }

                $result['data']['analyst'] = array(
                    'iAnalystID' => $analys['ID'],
                    'sName' => $analys['AnalystsName'],
                    'sLevel' => $analys['AnalystLevel'],
                    'sTel' => $sTel400,
                    'sImgUrl' =>  empty($analys['AnalystHead'])? Util_Uri::getDefaultImg(2) : Util_Uri::getCricViewURL($analys['AnalystHead'], 120, 120, 0, 0, 1)
                );

                $where = array(
                    'iAnalystId' => $analys['ID'],
                    'iStatus' => 1
                );
                $followed = Model_AnalystFollowed::getCnt(array('where'=>$where));
                $fans = Model_AnalystFans::getCnt(['where'=>$where]);

                $result['data']['analyst']['sFollowedNum'] = $followed;
                $result['data']['analyst']['iFans'] = $fans;
            }

            $result['data']['hotCommentList'] = Model_Evaluation::getNewest($cityID);
        }else {
            $result = array(
                'code' =>  1,
                'msg' => '参数不全',
            );
        }

        return $this->showMsg($result, true);
    }

    /**
     * 获取定时刷新数据
     */
    public function maintime_infosAction()
    {
        $cityID = intval($this->getParam('cityID'));
        $lat = floatval($this->getParam('lat'));
        $lon = floatval($this->getParam('lon'));

        $result = array(
            'code' =>  0,
            'msg' => 'ok',
            'data' => array()
        );

        if(!empty($cityID)) {//获取周边均价，如果给定坐标，则找离坐标最近板块的均价，否则返回城市均价
            $avePrice = 0;
            if(empty($lat) || empty($lon)) {
                $avePrice = Model_CricCity::getCityByID($cityID);
                $avePrice = $avePrice['Price'];
            }else {
                $districtPrice = Model_CricDistrict::getDistrictPrice($cityID, $lon, $lat);
                if(!empty($districtPrice) && !empty($districtPrice['Price'])) {
                    $avePrice = $districtPrice['Price'];
                }else {
                    $avePrice = Model_CricCity::getCityByID($cityID);
                    $avePrice = $avePrice['Price'];
                }
            }

            if(!$avePrice) {
                $avePrice = '待定';
            }

            $result['data']['sAvePrice'] = $avePrice;
        }else {
            $result = array(
                'code' =>  1,
                'msg' => '参数不全',
            );
        }

        return $this->showMsg($result, true);
    }

    /**
     * 获取城市列表
     */
    public function cityAction()
    {
        $result = array(
            'code' => 0,
            'msg' => 'ok',
            'data' => array(
                'list' => array()
            )
        );

        $lat = floatval($this->getParam('lat'));
        $lon = floatval($this->getParam('lon'));

        $aCity = Model_CricCity::getCitys();
        $len = sizeof($aCity);

        if(!empty($lon) && !empty($lat)) {
            for($i = 0; $i < $len; $i ++) {
                $distance = $this->getdistance($lon, $lat, $aCity[$i]['X'], $aCity[$i]['Y']);
                $aCity[$i]['distance'] = $distance;
            }

            $sort = array(
                'direction' => 'SORT_ASC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
                'field'     => 'distance',       //排序字段
            );
            $arrSort = array();
            foreach($aCity AS $uniqid => $row){
                foreach($row AS $key=>$value){
                    $arrSort[$key][$uniqid] = $value;
                }
            }
            if($sort['direction']){
                array_multisort($arrSort[$sort['field']], constant($sort['direction']), $aCity);
            }
        }

        $withCode = intval($this->getParam('withCode'));
        if($withCode) {
            foreach($aCity as $city) {
                $result['data']['list'][$city['ID']] = array(
                    'sName' => $city['CityName'],          // 城市名称
                    'sPinyin' => $city['CityCode'],
                    'sPinyinShort' => $city['EJUCityCode'],
                    'sLat' => $city['Y'],   // 纬度
                    'sLon' => $city['X'],    // 经度
                    'iCodeID' => $city['ID'],        // 城市ID
                    'iEsfEnabled' => $city['EsfEnabled']        // 二手房是否显示
                );
            }
        }else {
            foreach($aCity as $city) {
                $result['data']['list'][] = array(
                    'sName' => $city['CityName'],          // 城市名称
                    'sPinyin' => $city['CityCode'],
                    'sPinyinShort' => $city['EJUCityCode'],
                    'sLat' => $city['Y'],   // 纬度
                    'sLon' => $city['X'],    // 经度
                    'iCodeID' => $city['ID'],        // 城市ID
                    'iEsfEnabled' => $city['EsfEnabled']        // 二手房是否显示
                );
            }
        }
        return $this->showMsg($result, true);
    }

    /**
     * 地图城市*/
    public function cityMapAction(){
        $aCity = Model_CricCityMap::getCityMaps();
        if(!empty($aCity)){
            $result = array(
                'code' => 0,
                'msg' => 'ok',
                'data' => array(
                    'list' => $aCity
                )
            );
        }
        else{
            $result = array(
                'code' => 1,
                'msg' => '没有数据',
                'data' => array(
                    'list' => $aCity
                )
            );
        }

        return $this->showMsg($result, true);
    }

    /**
     * 用户注册
     */
    public function registerAction()
    {
        $result = array(
            'code' => 0,
            'msg' => 'ok',
            'data' => array(
                'iSuccess' => 1
            )
        );

        $mobile = addslashes($this->getParam('mobile'));
        $vCode = addslashes($this->getParam('code'));
        $passwd = addslashes($this->getParam('pwd'));
        $city = addslashes($this->getParam('city'));
        $userID = addslashes($this->getParam('userID'));

        if( !empty($vCode) && !empty($mobile) && !empty($passwd) &&!empty($city)) {
            $oCricCricUser = new Model_CricUser();
            $isExist = $oCricCricUser->getUserByName($mobile);
            if(!empty($isExist)) {
                $result['code'] = 1;
                $result['msg'] = '该手机已经被注册';
            }else {
                $pre = Yaf_G::getConf('code_pre',null,'mapi');
                $key = $pre. '_'. $mobile. '_'. 1;
                $cache = Util_Common::getCache();
                $existCode = $cache->get($key);

                if(!empty($existCode) && $existCode == $vCode) {
                    $passwd = Model_CricUser::genPasswd($mobile, $passwd);

                    if($passwd) {
                        if(empty($userID)) {
                            $success = $oCricCricUser->addUser($mobile, $passwd, $city);
                        }else {
                            $success = $oCricCricUser->improve($mobile, $passwd, $city, $userID);
                        }
                    }else {
                        $result['code'] = 1;
                        $result['msg'] = '密码生成失败';
                    }

                    if($success) {
                        $cache->delete($key);
                    }else {
                        $result['code'] = 1;
                        $result['msg'] = '数据库错误';
                    }
                }else {
                    $result['code'] = 1;
                    $result['msg'] = '验证码错误或者已经过期';
                }
            }
        }else {
            $result['code'] = 1;
            $result['msg'] = '参数不全';
        }

        return $this->showMsg($result, true);
    }

    /**
     * 绑定手机
     */
    public function bindphoneAction()
    {
        $result = array(
            'code' => 0,
            'msg' => 'ok',
            'data' => array(
                'iSuccess' => 1
            )
        );

        $mobile = addslashes($this->getParam('mobile'));
        $vCode = addslashes($this->getParam('code'));
        $userID = addslashes($this->getParam('userID'));

        if( !empty($vCode) && !empty($mobile) && !empty($userID) ) {
            $oCricCricUser = new Model_CricUser();
            $isExist = $oCricCricUser->getUserByName($mobile);
            if(!empty($isExist)) {
                $result['code'] = 1;
                $result['msg'] = '该手机已经被注册';
            }else {
                $pre = Yaf_G::getConf('code_pre',null,'mapi');
                $key = $pre. '_'. $mobile. '_'. 5;
                $cache = Util_Common::getCache();
                $existCode = $cache->get($key);

                if(!empty($existCode) && $existCode == $vCode) {
                    $success = $oCricCricUser->changePhone($mobile, $userID);

                    if($success) {
                        $cache->delete($key);
                    }else {
                        $result['code'] = 1;
                        $result['msg'] = '数据库错误';
                    }
                }else {
                    $result['code'] = 1;
                    $result['msg'] = '验证码错误或者已经过期';
                }
            }
        }else {
            $result['code'] = 1;
            $result['msg'] = '参数不全';
        }

        return $this->showMsg($result, true);
    }

    /**
     * 登录
     */
    public function loginAction()
    {
        $mobile = $this->getParam('mobile');
//        $code = $this->getParam('code');
        $pwd = $this->getParam('pwd');

        $result = array(
            'code' => 0,
            'msg' => 'ok',
            'data' => array()
        );

//        if( !empty($code) && !empty($mobile) && !empty($pwd) ) {
        if( !empty($mobile) && !empty($pwd) ) {
            $oCricCricUser = new Model_CricUser();

            $pwd = Model_CricUser::genPasswd($mobile, $pwd);
            $data = $oCricCricUser->getOneChecked($mobile, $pwd);

            if(empty($data)) {
                $result['code'] = 1;
                $result['msg'] = '用户名或密码错误';
            } else {
                $key = Yaf_G::getConf('token',null,'mapi');
                //token由手机+密码+userid组成
                $strToken = $mobile. ','. $pwd. ','. $data['Id'];
                $token = Util_Crypt::encrypt($strToken, $key);

                $currentTime = time();
                $tokenData = array(
                    'sToken' => $token,
                    'iUserId' => $data['Id'],
                    'iCreateTime' => $currentTime,
                    'iUpdateTime' => $currentTime
                );

                Model_Token::addData($tokenData);

                $result['data']['iUserID'] = $data['Id'];
                $result['data']['sToken'] = $token;
                $result['data']['sUserName'] = $data['UserName'];
                $result['data']['sHeadImg'] = empty($data['HeadImg']) ? Util_Uri::getDefaultImg(2) : Util_Uri::getCricViewURL($data['HeadImg'], 300, 400);
            }
        }else {
            $result['code'] = 1;
            $result['msg'] = '参数不全';
        }
        return $this->showMsg($result, true);
    }

    /**
     * 登出
     */
    public function logoutAction()
    {
        $userId = intval($this->getParam('userID'));
        $token = $this->getParam('token');

        $tokenChange = Model_Token::checkToken($token, $userId);
        if (!$tokenChange) {
            $result['code'] = 1;
            $result['msg'] = '您还没有登录！';
            return $this->showMsg($result, true);
        }

        $result = array(
            'code' => 0,
            'msg' => 'ok',
            'data' => array(
                'iSuccess' => 1
            )
        );

        if(!empty($userId) && !empty($token)) {
            $tokenSaved = Model_Token::getToken($token);
            if(!empty($tokenSaved)) {
                $tokenChange = Model_Token::checkToken($token, $tokenSaved['iUserId']);

                if($tokenChange) {
                    Model_Token::changStatus($token, 0);
                }else {
                    $result['code'] = 1;
                    $result['msg'] = 'token验证错误';
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
     * 获取验证码
     */
    public function verifycodeAction()
    {
        $mobile = $this->getParam('mobile');
        $type = intval($this->getParam('type'));
        $type = max($type, 1);

        $result = array(
            'code' => 0,
            'msg' => 'ok',
        );

        if(!empty($mobile)) {
            $verifySms = Util_Common::getConf('', 'verifySms');
            $smsClient = new Sms_ItissmTrigClient($verifySms['url'].'?WSDL', $verifySms['account']['sUserCode'], $verifySms['account']['sUserPass'], $verifySms['channel']);
            $sms = new Sms_Factory($smsClient);

            $pre = Yaf_G::getConf('code_pre',null,'mapi');
            $key = $pre. '_'. $mobile. '_'. $type;
            $cache = Util_Common::getCache();
            $vcode = $cache->get($key);

            if(!$vcode) {
                $vcode = Util_Tools::passwdGen(6, Util_Tools::FLAG_NUMERIC);
            }

            $message = '房价点评网校验码：'.$vcode.'，请于30分钟内输入，感谢您的使用。【房价点评】';
            $message = '';
            switch($type) {
                case 1:
                    $message = '感谢您注册房价点评网！验证码：'.$vcode.'，请于30分钟内输入。【房价点评】';
                    break;
                case 2:
                    $message = '您正在找回房价点评网登陆密码，验证码：'.$vcode.'，请于30分钟内输入，请勿泄露。【房价点评】';
                    break;
                case 3:
                    $message = '感谢您加入理想家俱乐部！验证码：'.$vcode.'，请于30分钟内输入。【房价点评】';
                    break;
                case 4:
                    $message = '您正在修改房价点评网登陆密码，验证码：'.$vcode.'，请于30分钟内输入，请勿泄露。【房价点评】';
                    break;
                case 5:
                    $message = '您正在为房价点评网用户绑定手机，验证码：'.$vcode.'，请于30分钟内输入，请勿泄露。【房价点评】';
                    break;
                default:
                    break;
            }

            $data = $sms->sendMsg(['sMsg' => $message, 'sCellPhone' => $mobile]);

            if($data['iCode'] < 0) {
                $result['code'] = $data['iCode'];
                $result['msg'] = $data['sMsg'];
            }else {
                $life = Yaf_G::getConf('code_life',null,'mapi');
                $life *= 60;
                $cache->set($key, $vcode, $life);

                $result['data']['sVerifyCode'] = $vcode;
            }

        }else {
            $result['code'] = 1;
            $result['msg'] = '参数不全';
        }

        return $this->showMsg($result, true);
    }

    /**
     * 检验验证码
     */
    public function checkverifyAction()
    {

        $mobile = $this->getParam('mobile');
        $code = $this->getParam('code');
        $type = $this->getParam('type');
        $type = max($type, 1);

        $result = array(
            'code' => 0,
            'msg' => 'ok'
        );

        if(!empty($mobile) && !empty($code)) {
            $pre = Yaf_G::getConf('code_pre',null,'mapi');
            $key = $pre. '_'. $mobile. '_'. $type;
            $cache = Util_Common::getCache();
            $existCode = $cache->get($key);

            if( !empty($existCode) && $existCode == $code) {
//                $cache->delete($key);
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


    public function getdistance($lng1,$lat1,$lng2,$lat2){
        //将角度转为狐度
        $radLat1=deg2rad($lat1);//deg2rad()函数将角度转换为弧度
        $radLat2=deg2rad($lat2);
        $radLng1=deg2rad($lng1);
        $radLng2=deg2rad($lng2);
        $a=$radLat1-$radLat2;
        $b=$radLng1-$radLng2;
        $s=2*asin(sqrt(pow(sin($a/2),2)+cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)))*6378.137*1000;
        return $s;
    }

    /**
     * QQ登录
     * param $userName
     * param $sex 0:男，1：女
     * param $city
     * param $openId
     * param $currentUID(dpuser表ID字段)
     * param $source 'QQ', 'WeiBo'
     */
    public function thirdloginAction()
    {
        $result = array(
            'code' => 0,
            'msg' => 'ok'
        );

        $userName = $this->getParam('userName');
        $sex = intval($this->getParam('sex'));
        $city = $this->getParam('city');
        $openId = $this->getParam('openID');
        $source = $this->getParam('source');
        $currentUID = intval($this->getParam('userID'));

        if(empty($userName) || empty($city) || empty($openId)) {
            $result['code'] = 1;
            $result['msg'] = '参数不全';
        }else {
            $sex = empty($sex) ? 0 : 1;
            $userID = 0;
            $phone = '';
            if (empty($currentUID)) {
                $mUser = new Model_CricUser();
                $user = $mUser->getUserByOpenID($openId);

                if(empty($user)) {
                    $mUser->addThirdUser($userName, $sex, $city, $openId, $source);
                    $user = $mUser->getUserByOpenID($openId);
                }

                $uid = $user['UserId'];
                $userID = $user['Id'];
                $phone = $user['Phone'];
                $mSnsUser = new Model_CricSnsUser();
                $snsUser = $mSnsUser->GetSnsAccountWithIdAndSnsName($uid, $source);
                if(empty($snsUser)) {
                    $mSnsUser->addUser($source, $openId, $userName, $sex, $uid);
                }
            }else{
                $mUser = new Model_CricUser();
                $user = $mUser->getUserById($currentUID);
                $userID = $currentUID;
                if(!empty($user)) {
                    $uid = $user['UserId'];
                    $phone = $user['Phone'];
                    $mSnsUser = new Model_CricSnsUser();
                    $mSnsUser->addUser($source, $openId, $userName, $sex, $uid);
                }
            }

            if(!empty($userID)) {
                $key = Yaf_G::getConf('token',null,'mapi');
                //token由手机+密码+userid组成
                $strToken = 'mobile'. ','. '123456'. ','. $userID;
                $token = Util_Crypt::encrypt($strToken, $key);

                $currentTime = time();
                $tokenData = array(
                    'sToken' => $token,
                    'iUserId' => $userID,
                    'iCreateTime' => $currentTime,
                    'iUpdateTime' => $currentTime
                );

                Model_Token::addData($tokenData);

                $result['data']['iUserID'] = $userID;
                $result['data']['sToken'] = $token;
                $result['data']['sUserName'] = $userName;
                $result['data']['sPhone'] = $phone;
                $result['data']['sHeadImg'] = Util_Uri::getDefaultImg(2);
            }else{
                $result['code'] = 1;
                $result['msg'] = '系统异常';
            }
        }

        return $this->showMsg($result, true);
    }

    /**
     * 搜索框提示
     */
    public function searchtipAction()
    {
        $result = array(
            'code' => 0,
            'msg' => 'ok',
            'data' => array()
        );

        $keywords = $this->getParam('keywords');
        $cityCode = $this->getParam('cityCode');

        if(empty($keywords) || empty($cityCode)) {
            $result['code'] = 1;
            $result['msg'] = '参数不全';
        }else {
            $where = array(
                'Category' => 1,
                'State' => 1,
                'CityCode' => $cityCode,
                'sWhere' => "LOCATE('".$keywords. "',Keywords)"
            );

            $buildings = array();
            $buildings = Model_CricUnitSearch::getAll(['where' => $where]);

            if(!empty($buildings)) {
                foreach($buildings as $building) {
                    $result['data'][] = array(
                        'ID' => $building['ID'],
                        'unitID' => $building['UnitID'],
                        'region' => $building['Region'],
                        'address' => $building['OfficalAddr']
                    );
                }
            }
        }

        return $this->showMsg($result, true);
    }


}