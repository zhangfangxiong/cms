<?php

class Controller_Index_Project extends Controller_Base
{
    
    /**
     * 删除项目
     * @return boolean
     */
    public function delAction ()
    {
        $iProjectID = intval($this->getParam('id'));
        $iRet = Model_Project::delData($iProjectID);
        if ($iRet == 1) {
            return $this->showMsg('项目删除成功！', true);
        } else {
            return $this->showMsg('项目删除失败！', false);
        }
    }
    
    /**
     * 项目列表
     */
    public function listAction ()
    {
        $iPage = intval($this->getParam('page'));
        $aWhere = array('iStatus' => 1);
        $aList = Model_Project::getList($aWhere, $iPage);
        $this->assign('aList', $aList);
    }
    
    /**
     * 编辑项目
     * @return boolean
     */
    public function editAction ()
    {
        if ($this->isPost()) {
            $aProject = $this->_checkData();
            if (empty($aProject)) {
                return null;
            }
            $aProject['iProjectID'] = intval($this->getParam('iProjectID'));
            if (1 == Model_Project::updData($aProject)) {
                return $this->showMsg('项目信息更新成功！', true);
            } else {
                return $this->showMsg('项目信息更新失败！', false);
            }
        } else {
            $iProjectID = intval($this->getParam('id'));
            $aProject = Model_Project::getDetail($iProjectID);
            $this->assign('aProject', $aProject);
        }
    }
    
    /**
     * 增加项目
     * @return boolean
     */
    public function addAction ()
    {
        if ($this->isPost()) {
            $aProject = $this->_checkData();
            if (Model_Project::addData($aProject) > 0) {
                return $this->showMsg('项目增加成功！', true);
            } else {
                return $this->showMsg('项目增加失败！', false);
            }
        }
    }
    
    /**
     * 请求数据检测
     * @return mixed
     */
    public function _checkData ($sType = 'add')
    {
        $sProjectName = $this->getParam('sProjectName');
        $sDesc = $this->getParam('sDesc');
        $sPermissionApi = $this->getParam('sPermissionApi');
        
        if (! Util_Validate::isLength($sProjectName, 3, 30)) {
            return $this->showMsg('项目名长度范围为3到30个字！', false);
        }
        if (! Util_Validate::isLength($sDesc, 5, 255)) {
            return $this->showMsg('项目名长度范围为5到255个字！', false);
        }
        if (! Util_Validate::isLength($sPermissionApi, 5, 255)) {
            return $this->showMsg('项目权限API长度范围为5到255个字！', false);
        }
        $aRow = array(
            'sProjectName' => $sProjectName,
            'sDesc' => $sDesc,
            'sPermissionApi' => $sPermissionApi
        );
        return $aRow;
    }
}