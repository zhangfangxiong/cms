<?php

/**
 * Created by PhpStorm.
 * User: len
 * Date: 2015/4/10
 * Time: 11:33
 */
class Controller_H5_Index extends Controller_Base
{
    /**
     * 执行Action前执行
     * @see Yaf_Controller::actionBefore()
     */
    public function actionBefore()
    {
        parent::actionBefore();
        $this->assign('hasRoot', 1);
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
        $aCurrCity = $this->getCurrCity();
        $aParam['cityID'] = $aCurrCity['iCodeID'];

        $aAnalyst = Util_Cookie::get('analyst'.$aParam['cityID']);
        if (!empty($aAnalyst)) {
            $aParam['analystID'] = $aAnalyst['iAnalystID'];
        }

        $aData = Sdk_Cms_Index::maininfos($aParam);

        if (isset($aData['data']['analyst']) && !empty($aData['data']['analyst'])) {
            Util_Cookie::set('analyst'.$aParam['cityID'], $aData['data']['analyst']);
        }
        $aAnalyst = $aData['data']['analyst'];

        $aUserInfo = Controller_H5_Ucenter::checkHasLogin();
        if ($aUserInfo) {
            $this->assign('aUserInfo', json_decode($aUserInfo, true));
        }
        $this->assign('aCurrCity', $aCurrCity);
        $this->assign('aData', $aData);
        $this->assign('aMouduleEntrenceInfo', $this->_MouduleEntrenceUrl());
        $this->assign('aAnalyst', $aAnalyst);
        $this->aMeta['title'] .= '首页';
    }

    public function cityAction()
    {
        //获取城市列表
        $aCityList = Sdk_Cms_Ucenter::city(array('withCode' => 1));
        $aCurrCity = $this->getCurrCity();
        $this->assign('aCityList', $aCityList['data']['list']);
        $this->assign('aCurrCity', $aCurrCity);
        $this->aMeta['title'] .= '城市列表';
    }

    public function zanAction()
    {
        $aParam['analystID'] = $this->getParam('analystID');
        $aParam['userID'] = $this->getParam('userID');
        $aData = Sdk_Cms_Index::zan($aParam);
        if (!$aData['status']) {
            return $this->showMsg($aData['data']['msg'], false);
        } else {
            return $this->showMsg($aData, true);
        }
    }

    public function lixiangjiaAction()
    {
        $this->assign('hasRoot', 0);
        if ($this->isPost()) {
            $aParam['name'] = $this->getParam('name');
            $aParam['phoneNum'] = $this->getParam('phoneNum');
            $aParam['verifyCode'] = $this->getParam('verifyCode');
            $aData = Sdk_Cms_Index::lixiangjia($aParam);
            if (!$aData['status']) {
                return $this->showMsg($aData['data']['msg'], false);
            } else {
                return $this->showMsg($aData, true);
            }
        }
        $this->aMeta['title'] .= '理想家';
    }

    //首页6个的模块的url和信息入口
    private function _MouduleEntrenceUrl()
    {
        return array(
            array('name' => '理想家', 'css' => 'tool_lxj', 'desc' => '立即加入拿底价', 'url' => '/h5/lixiangjia'),
            array('name' => '新房', 'css' => 'tool_zxf', 'desc' => '评测报告量身定做', 'url' => self::$sTouchwebRoot . '/h5/search/xfsearchindex'),
            array('name' => '二手房', 'css' => 'tool_cesf', 'desc' => '一房一价数据真实', 'url' => '/h5/Secondfang'),
            array('name' => '楼盘评测', 'css' => 'tool_lppc', 'desc' => '专业分析找好房', 'url' => self::$sTouchwebRoot . '/h5/xf/evaluation'),
            array('name' => '热销楼盘', 'css' => 'tool_rxlp', 'desc' => '第三方权威评价', 'url' => self::$sTouchwebRoot . '/h5/xf/hot'),
            array('name' => '最新导购', 'css' => 'tool_zxdg', 'desc' => '开盘讯息全知道', 'url' => self::$sTouchwebRoot . '/h5/guide')
        );
    }
}