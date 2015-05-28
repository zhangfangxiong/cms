<?php

/**
 * Created by PhpStorm.
 * User: zfx
 * Date: 2015/3/4
 * Time: 9:29
 */
class Controller_Template_Index extends Controller_Base
{
    protected function _getModuleType()
    {
        return Model_Banner::$aModuleType;
    }
    public function actionAfter()
    {
        parent::actionAfter();
        $this->_assignUrl();
    }

    protected function _assignUrl()
    {
        $this->assign('sListUrl', '/template/');
        $this->assign('sAddUrl', '/template/add/');
        $this->assign('sEditUrl', '/template/edit/');
        $this->assign('sDelUrl', '/template/del/');
        $this->assign('sFileBaseUrl', 'http://' . Yaf_G::getConf('file', 'domain'));
    }
    /**
     * 模块列表
     */
    public function indexAction()
    {
        $iPage = intval($this->getParam('page'));
        $aParam['sOrder'] = $this->getParam('sOrder');
        $sOrder = !$aParam['sOrder'] ? 'iUpdateTime DESC' : $aParam['sOrder'];
        $aTemplate = Model_Template::getPageList($aParam, $iPage, $sOrder);
        $this->assign('aTemplate', $aTemplate);

    }

    /**
     * 编辑模板
     */
    public function editAction()
    {
        if ($this->isPost()) {
            $aTemps = $this->_checkData('edit');
            if (empty($aTemps)) {
                return null;
            }
            $aTemps['iTempID'] = intval($this->getParam('id'));
            //修改需要加上当前修改人ID
            $aCurrUserInfo = $this->aCurrUser;
            $aTemps['iUpdateUserID'] = $aCurrUserInfo['iUserID'];
            if (1 == Model_Template::updData($aTemps)) {
                return $this->showMsg(['sMsg' => '模板修改成功！', 'iTempID' => $aTemps['iTempID']], true);
            } else {
                return $this->showMsg('模板修改失败！', false);
            }
        } else {
            $this->_response->setHeader('Access-Control-Allow-Origin', '*');
            $iTempID = intval($this->getParam('id'));
            $aTemps = Model_Template::getDetail($iTempID);
            $aTemps['sContent'] = htmlspecialchars_decode($aTemps['sContent']);
            $this->assign('aTemps', $aTemps);
        }
    }

    /**
     * 添加模板
     */
    public function addAction()
    {
        if ($this->isPost()) {
            $aTemps = $this->_checkData();
            if (empty($aTemps)) {
                return null;
            }
            //增加需要加上当前添加人ID
            $aTemps['iCreateTime'] = $aTemps['iUpdateTime'] = time();
            $aCurrUserInfo = $this->aCurrUser;
            $aTemps['iUpdateUserID'] = $aCurrUserInfo['iUserID'];
            $aTemps['iCreateUserID'] = $aCurrUserInfo['iUserID'];
            $aTemps['iPublishStatus'] = $aTemps['iStatus'] = 1;
            $iTempID = Model_Template::addData($aTemps);
            if ($iTempID > 0) {
                return $this->showMsg(['sMsg' => '模板添加成功', 'iTempID' => $iTempID], true);
            } else {
                return $this->showMsg('模板添加失败！', false);
            }
        }
    }

    /**
     * 删除模板
     *
     * @return boolean
     */
    public function delAction()
    {
        $iTempID = $this->getParam('id');
        if (!$iTempID) {
            return $this->showMsg('非法操作！', false);
        }
        $iRet = Model_Template::delData($iTempID);
        if ($iRet) {
            return $this->showMsg('模板删除成功！', true);
        }
        return $this->showMsg('模板删除失败！', false);
    }

    protected function _checkData()
    {
        $sTitle = $this->getParam('sTitle');
        $sDesc = $this->getParam('sDesc');
        $sContent = htmlspecialchars($this->getParam('sContent'));

        if (!Util_Validate::isCLength($sTitle, 4, 22)) {
            return $this->showMsg('资讯标题长度范围为4到22个字！', false);
        }

        $aRow = [
            'sTitle' => $sTitle,
            'sDesc' => $sDesc,
            'sContent' => $sContent
        ];

        return $aRow;
    }

    //格式化模块信息（有的模块没有进入模板库）
    protected function _checkModule()
    {
        $aModuleList = $this->_getModuleType();
        foreach ($aModuleList as $key => $value) {
            foreach ($value as $k => $v) {
                $aTmp['iModuleID'] = $key;
                $aTmp['iTypeID'] = $k;
                $aTmp['sDesc'] = $v['sTitle'];
                $aReturn[] = $aTmp;
            }
        }
        return $aReturn;
    }
}