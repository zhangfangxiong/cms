<?php

class Controller_Index_City extends Controller_Base
{

    /**
     * 城市删除
     */
    public function delAction()
    {
        $iCityID = intval($this->getParam('id'));
        $iRet = Model_City::delData($iCityID);
        if ($iRet == 1) {
            return $this->showMsg('城市删除成功！', true);
        } else {
            return $this->showMsg('城市删除失败！', false);
        }
    }

    /**
     * 城市列表
     */
    public function listAction()
    {
        $iPage = intval($this->getParam('page'));
        $aWhere = array(
            'iStatus' => 1
        );
        $aList = Model_City::getList($aWhere, $iPage);
        $this->assign('aList', $aList);
    }

    /**
     * 城市修改
     */
    public function editAction()
    {
        if ($this->_request->isPost()) {
            $aCity = $this->_checkData('update');
            if (empty($aCity)) {
                return null;
            }
            $aCity['iCityID'] = intval($this->getParam('iCityID'));
            $aOldCity = Model_City::getDetail($aCity['iCityID']);
            if (empty($aOldCity)) {
                return $this->showMsg('城市不存在！', false);
            }
            if ($aOldCity['sCityName'] != $aCity['sCityName']) {
                if (Model_City::getCityByName($aCity['sCityName'])) {
                    return $this->showMsg('城市已经存在！', false);
                }
            }
            if (1 == Model_City::updData($aCity)) {
                return $this->showMsg('城市信息更新成功！', true);
            } else {
                return $this->showMsg('城市信息更新失败！', false);
            }
        } else {
            $iCityID = intval($this->getParam('id'));
            $aCity = Model_City::getDetail($iCityID);
            $this->assign('aCity', $aCity);
        }
    }

    /**
     * 增加城市
     */
    public function addAction()
    {
        if ($this->_request->isPost()) {
            $aCity = $this->_checkData('add');
            if (empty($aCity)) {
                return null;
            }
            if (Model_City::getCityByName($aCity['sCityName'])) {
                return $this->showMsg('城市已经存在！', false);
            }
            if (Model_City::addData($aCity) > 0) {
                return $this->showMsg('城市增加成功！', true);
            } else {
                return $this->showMsg('城市增加失败！', false);
            }
        }
    }

    /**
     * 更换城市
     */
    public function changeAction()
    {
        $iCityID = $this->getParam('id');
        $aCity = Model_City::getDetail($iCityID);
        if (empty($aCity) || $aCity['iShow'] == 0 || $aCity['iStatus'] == 0) {
            return $this->showMsg('城市不存在！', false);
        }
        $aUser = Model_User::getDetail($this->aCurrUser['iUserID']);
        $aCityID = explode(',', $aUser['sCityID']);
        if ($aUser['sCityID'] != '-1' && !in_array($iCityID, $aCityID)) {
            return $this->showMsg('您没有访问该城市的权限，请联系管理员！', false);
        }
        Util_Cookie::set('city', $iCityID);
        return $this->showMsg('城市切换成功!', true);
    }

    /**
     * 请求数据检测
     *
     * @return mixed
     */
    public function _checkData($sType = 'add')
    {
        $sCityName = $this->getParam('sCityName');
        $sPinyin = $this->getParam('sPinyin');
        $sFullPinyin = $this->getParam('sFullPinyin');
        $iShow = $this->getParam('iShow');

        if (!Util_Validate::isLength($sCityName, 2, 20)) {
            return $this->showMsg('城市名称长度范围为2到20个字！', false);
        }
        if (!Util_Validate::isLength($sPinyin, 2, 50)) {
            return $this->showMsg('城市简拼长度范围为2到50个字母！', false);
        }
        if (!Util_Validate::isLength($sFullPinyin, 2, 50)) {
            return $this->showMsg('城市全拼长度范围为2到50个字母！', false);
        }
        if (!Util_Validate::isRange($iShow, 0, 1)) {
            return $this->showMsg('城市是否使用输入不合法！', false);
        }
        $aRow = array(
            'sCityName' => $sCityName,
            'sPinyin' => $sPinyin,
            'sFullPinyin' => $sFullPinyin,
            'iShow' => $iShow
        );
        return $aRow;
    }
}