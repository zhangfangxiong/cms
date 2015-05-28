<?php

class Model_Permission extends Model_Base
{

    const DB_NAME = 'permission';

    const TABLE_NAME = 't_permission';

    /**
     * 删除权限点
     * @param int $iPermissionID
     * @return int
     */
    public static function delData ($iPermissionID)
    {
        $oOrm = self::getOrm();
        $oOrm->setPKIDValue($iPermissionID);
        return $oOrm->delData();
    }

    /**
     * 取一个项目的所有权限点
     * @param int $iProjectID
     * @return array
     */
    public static function getProjectPermissions ($iProjectID)
    {
        $aRet = array();
        $aList = self::getAll(array('where' => array('iProjectID' => $iProjectID)), true);
        foreach ($aList as $v) {
            $aRet[$v['iModuleID']][] = $v;
        }
        
        return $aRet;
    }
    
    /**
     * 取得用户所有菜单的权限
     * @param array $aUser
     * @return array
     */
    public static function getMenuPermissions ($iUserID)
    {
        $aUser = Model_User::getDetail($iUserID);
        if ($aUser['sRoleID'] === '-1') {
            return -1;
        }
        $aRoleList = Model_Role::getPKIDList($aUser['sRoleID']);
        $aModuleID = array();
        foreach ($aRoleList as $aRole) {
            if (!empty($aRole['sModule'])) {
                $aModuleID = array_merge($aModuleID, explode(',', $aRole['sModule']));
            }
        }
        return array_flip($aModuleID);       
    }
    
    /**
     * 取得用户所有访问权限
     * @param array $aUser
     * @return array
     */
    public static function getUserPermissions ($iUserID)
    {
        $aUser = Model_User::getDetail($iUserID);
        if ($aUser['sRoleID'] === '-1') {
            return -1;
        }
        $aRoleList = Model_Role::getPKIDList($aUser['sRoleID']);
        $aPermissionID = array();
        foreach ($aRoleList as $aRole) {
            if (!empty($aRole['sPermission'])) {
                $aPermissionID = array_merge($aPermissionID, explode(',', $aRole['sPermission']));
            }
        }
        $aPermission = array();
        $aList = self::getPKIDList($aPermissionID, true);
        foreach ($aList as $v) {
            $aPermission[$v['sPath']] = 1;
        }
        return $aPermission;
    }
}