<?php

/**
 * Created by PhpStorm.
 * User: chasel
 * Date:  2015/4/13
 * Time:  11:20
 */
class Controller_Mapi_Idea extends Controller_Mapi_Base
{

    /**
     * 理想家用户信息提交
     */
    public function commitAction()
    {
        $name = addslashes($this->getParam('name'));
        $phoneNum = addslashes($this->getParam('phoneNum'));
        $verifyCode = addslashes($this->getParam('verifyCode'));

        $result = array(
            'code' =>  0,
            'msg' => 'ok',
            'data' => array()
        );

        if(!empty($name) && !empty($phoneNum) && !empty($verifyCode)) {
            $pre = Yaf_G::getConf('code_pre',null,'mapi');
            $keyByPhone = $pre. '_'. $phoneNum. '_'. 3;
            $cache = Util_Common::getCache();
            $codeByPhone = $cache->get($keyByPhone);

            if(!empty($codeByPhone) && $codeByPhone == $verifyCode) {
                $idea = array(
                    'sName' => $name,
                    'sPhone' => $phoneNum,
                    'iCreateTime' => time(),
                    'iUpdateTime' => time()
                );
                Model_Idea::addData($idea);
                $cache->delete($keyByPhone);
                $result['data']['iSuccess'] = 1;
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
     * 楼盘单页中理想家用户信息提交
     */
    public function commitbuildingAction()
    {
        $name = addslashes($this->getParam('name'));
        $phoneNum = addslashes($this->getParam('phoneNum'));
        $buildingID = intval($this->getParam('buildingID'));
        $price = intval($this->getParam('price'));

        $result = array(
            'code' =>  0,
            'msg' => 'ok',
            'data' => array()
        );

        if(!empty($name) && !empty($phoneNum) && !empty($buildingID)) {
            $data = array(
                'sPhone' => $phoneNum,
                'sName' => $name,
                'iBuildingID' => $buildingID,
                'iCreateTime' => time(),
                'iUpdateTime' => time(),
            );

            if(!empty($price)) {
                $data['iPrice'] = $price;
            }

            Model_IdeaBuilding::addData($data);
        }else {
            $result['code'] = 1;
            $result['msg'] = '参数不全';
        }

        return $this->showMsg($result, true);
    }

}