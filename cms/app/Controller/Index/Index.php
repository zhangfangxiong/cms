<?php

class Controller_Index_Index extends Controller_Base
{
    protected $bCheckLogin = false;

    public function indexAction ()
    {
        $aCookie = Util_Cookie::get(Yaf_G::getConf('authkey', 'cookie'));
        $sUrl = '/login';

        if ($aCookie) {
            $aPermissions = Model_Permission::getUserPermissions($aCookie['iUserID']);
            if (is_array($aPermissions) && !empty($aPermissions)) {
                $aTmp = array_keys($aPermissions);
                $sUrl = $aTmp[0];
            } else {
                $sUrl = '/news';
            }
        }
        return $this->redirect($sUrl);
    }

    public function loginAction ()
    {}

    public function logoutAction ()
    {
        Util_Cookie::delete(Yaf_G::getConf('authkey', 'cookie'));
        $this->redirect('/login');
    }

    public function signinAction ()
    {
        $sUserName = $this->getParam('username');
        $sPassword = $this->getParam('password');
        $bRemember = $this->getParam('remember');
        $aUser = Model_User::getUserByName($sUserName);
        if (empty($aUser)) {
            return $this->showMsg('帐号不存在！', false);
        }
        if ($aUser['iStatus'] == 0) {
            return $this->showMsg('帐号被禁用！', false);
        }
        if ($aUser['sPassword'] != md5(Yaf_G::getConf('cryptkey', 'cookie') . $sPassword)) {
            return $this->showMsg('密码不正确！', false);
        }
        $aCookie = array(
            'iUserID' => $aUser['iUserID'],
            'iCityID' => $aUser['iCityID'],
            'sUserName' => $aUser['sUserName'],
            'sRealName' => $aUser['sRealName']
        );
        Util_Cookie::set(Yaf_G::getConf('authkey', 'cookie'), $aCookie);

        $aPermissions = Model_Permission::getUserPermissions($aCookie['iUserID']);

        $sUrl = '/news';
        if (is_array($aPermissions) && !empty($aPermissions)) {
            $aTmp = array_keys($aPermissions);
            $sUrl = $aTmp[0];
        }
        return $this->showMsg(['msg' => '登录成功！', 'sUrl' => $sUrl], true);
    }

    public function permissionAction ()
    {
        $iProjectID = $this->aCurrProject['id'];
        $aMenuList = Model_Menu::getMenus($iProjectID);

        $aCtrClass = array();
        $aMenuAction = array();
        foreach ($aMenuList as $aMenu) {
            if ($aMenu['bIsLeaf']) {
                $aRoute = Yaf_G::getRoute($aMenu['sUrl']);
                $aMenuAction[$aRoute['module'] . '_' . $aRoute['controller'] . '_' . $aRoute['action'] ] = $aMenu['sMenuName'];
                $aCtrClass[$aRoute['module'] . '_' . $aRoute['controller']] = array(
                    'iMenuID' => $aMenu['iMenuID'],
                    'sMenuName' => $aMenu['sMenuName'],
                    'sUrl' => $aMenu['sUrl']
                );
            }
        }

        $aPermission = array();
        foreach ($aCtrClass as $sCtrClass => $aMenu) {
            try {
                $sCtrClass = 'Controller_' . $sCtrClass;
                if (class_exists($sCtrClass)) {
                    $oCtr = new ReflectionClass($sCtrClass);
                    $aMethod = $oCtr->getMethods();
                    foreach ($aMethod as $oMethod) {
                        $sAction = $oMethod->getName();
                        if (substr($sAction, - 6) === 'Action') {
                            $sAction = substr($sAction, 0, -6);
                            $aRow = array($aMenu['iMenuID']);
                            $aRow[] = Yaf_G::routeToUrl($sCtrClass.'_'.$sAction);
                            $sDoc = $oMethod->getDocComment();
                            $matches = null;
                            if (preg_match('/\s+\*\s+(.+)/i', $sDoc, $matches)) {
                                $aRow[] = $matches[1];
                            } elseif (isset($aMenuAction[$sCtrClass.'_'.$sAction])) {
                                $aRow[] = $aMenuAction[$sCtrClass.'_'.$sAction];
                            } else {
                                $aRow[] = $aMenu['sMenuName'] . '::' . $sAction;
                            }
                            $aPermission[] = $aRow;
                        }
                    }
                }
            } catch (Exception $e) {
                $aPermission[] = array(
                    $aMenu['iMenuID'],
                    Yaf_G::getUrl($aMenu['sUrl']),
                    $aMenu['sMenuName']
                );
            }
        }
        $this->showMsg($aPermission, true);
    }
}