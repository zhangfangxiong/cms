<?php

class Controller_Index_Permission extends Controller_Base
{

    /**
     * 权限点列表
     * 
     * @return boolean
     */
    public function listAction ()
    {
        $sKey = $this->getParam('qkey');
        $iPage = intval($this->getParam('page'));
        $aWhere = array(
            'iStatus' => 1
        );
        if (!empty($sKey)) {
            $aWhere['sPath LIKE'] = '%' . $sKey . '%'; 
        }
        $aList = Model_Permission::getList($aWhere, $iPage);
        $aMenuID = array();
        foreach ($aList['aList'] as $v) {
            $aMenuID[] = $v['iModuleID'];
        }
        $aMenuList = Model_Menu::getPKIDList($aMenuID, true);
        $this->assign('aList', $aList);
        $this->assign('aMenuList', $aMenuList);
    }

    /**
     * 删除权限点
     * 
     * @return boolean
     */
    public function delAction ()
    {
        $iPermissionID = intval($this->getParam('id'));
        $iRet = Model_Permission::delData($iPermissionID);
        if ($iRet == 1) {
            return $this->showMsg('权限点删除成功！', true);
        } else {
            return $this->showMsg('权限点删除失败！', false);
        }
    }

    /**
     * 编辑权限点
     * 
     * @return boolean
     */
    public function editAction ()
    {
        if ($this->isPost()) {
            $aPermission = $this->_checkData();
            if (empty($aPermission)) {
                return null;
            }
            $aPermission['iPermissionID'] = intval($this->getParam('iPermissionID'));
            if (1 == Model_Permission::updData($aPermission)) {
                return $this->showMsg('权限点信息更新成功！', true);
            } else {
                return $this->showMsg('权限点信息更新失败！', false);
            }
        } else {
            $iPermissionID = intval($this->getParam('id'));
            $aPermission = Model_Permission::getDetail($iPermissionID);
            $this->assign('aPermission', $aPermission);
            $this->assign('aMenuTree', Model_Menu::getMenus($this->aCurrProject['id']));
        }
    }

    /**
     * 增加权限点
     * 
     * @return NULL
     */
    public function addAction ()
    {
        if ($this->isPost()) {
            $aPermission = $this->_checkData();
            if (empty($aPermission)) {
                return null;
            }
            if (Model_Permission::addData($aPermission) > 0) {
                return $this->showMsg('权限点增加成功！', true);
            } else {
                return $this->showMsg('权限点增加失败！', false);
            }
        } else {
            $this->assign('aMenuTree', Model_Menu::getMenus($this->aCurrProject['id']));
        }
    }

    /**
     * 生成权限点
     */
    public function makeAction ()
    {
        $aProject = Model_Project::getDetail($this->aCurrProject['id']);
        $aData = Util_Curl::fileGetContents($aProject['sPermissionApi']);
        $aData = json_decode($aData, true);
        if ($aData['status'] == 0) {
            return $this->showMsg($aData['data'], false);
        }
        $iCnt = 0;
        foreach ($aData['data'] as $v) {
            $aRow = Model_Permission::getRow(array(
                'where' => array(
                    'iProjectID' => $this->aCurrProject['id'],
                    'sPath' => $v[1]
                )
            ));
            if (empty($aRow)) {
                $aRow = array(
                    'iProjectID' => $this->aCurrProject['id'],
                    'iModuleID' => $v[0],
                    'sPermissionName' => $v[2],
                    'sPath' => $v[1]
                );
                Model_Permission::addData($aRow);
                $iCnt++;
            } else {
                $aRow['iModuleID'] = $v[0];
                $aRow['sPermissionName'] = $v[2];
                Model_Permission::updData($aRow);
                $iCnt++;
            }
        }
        return $this->showMsg('本次生成权限点【' . $iCnt . '】个', true);
    }

    /**
     * 请求数据检测
     * 
     * @return mixed
     */
    public function _checkData ($sType = 'add')
    {
        $sPermissionName = $this->getParam('sPermissionName');
        $sPath = $this->getParam('sPath');
        $iModuleID = $this->getParam('iModuleID');
        
        if (! Util_Validate::isLength($sPermissionName, 2, 30)) {
            return $this->showMsg('权限名长度范围为2到30个字！', false);
        }
        if (! Util_Validate::isLength($sPath, 2, 50)) {
            return $this->showMsg('权限点长度范围为2到30个字符！', false);
        }
        if (empty($iModuleID)) {
            return $this->showMsg('请选择权限点的归属模块！', false);
        }
        $iCnt = Model_Menu::getCnt(array('where' => array('iParentID' => $iModuleID)));
        if ($iCnt > 0) {
            return $this->showMsg('归属模块只能选择最底层的模块！', false);
        }
        $aRow = array(
            'sPermissionName' => $sPermissionName,
            'sPath' => $sPath,
            'iModuleID' => $iModuleID,
        );
        return $aRow;
    }
}