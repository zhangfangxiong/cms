<?php
/**
 * Created by PhpStorm.
 * User: xiaoyao
 * Date: 2015/1/29
 * Time: 11:36
 */

/**
 * Class Controller_Home_Banner
 * 首页banner管理
 */

class Controller_Home_Vicepic extends Controller_Banner_Base
{


    protected function _getModule()
    {
        return Model_Banner::MODULE_HOME_CICEPIC_AD;
    }

    protected function _getModuleType()
    {
        return Model_Banner::$aModuleType[$this->_getModule()];
    }

    /**
     * 列表操作
     */
    public function indexAction()
    {
        $aParam = [];
        $aWhere = [];
        $iPublishStatus = $this->getParam('iPublishStatus');
        $aWhere['iPublishStatus'] = $aParam['iPublishStatus'] = $iPublishStatus;
        $aWhere['iModuleID'] = $this->_getModule();
        $aWhere['iCityID'] = $this->iCurrCityID;
        $aList = Model_Banner::getBanner($aWhere);
        if ($aList) {
            foreach ($aList as $key => $value) {
                $aPublishUser = Model_User::getDetail($value['iCreateUserID']);
                $aList[$key]['sPublishUserName'] = $aPublishUser['sUserName'];
            }
        }
        $this->assign('aPublishStatus', Model_Banner::$aPublishStatus);
        $this->assign('aList', $aList);
    }


    /**
     * 添加操作
     * @return null|void
     */
    public function addAction()
    {
        $aType = $this->_getModuleType();
        if ($this->isPost()) {
            $aData = $this->_checkData();
            if (empty($aData)) {
                return null;
            }
            $iOpType = $this->getParam('iOptype');
            if ($iOpType == 1) {
                $aData['iPublishStatus'] = 1;
            } else {
                $aData['iPublishStatus'] = 0;
            }

            $aData['iOrder'] = 0;
            $aData['iCreateTime'] = $aData['iUpdateTime'] = time();
            $aData['iCreateUserID'] = $aData['iUpdateUserID'] = $this->aCurrUser['iUserID'];
            $bRet = Model_Banner::addData($aData);
            if ($bRet) {
                return $this->showMsg($aType[$aData['iTypeID']]['sTitle'].'添加成功', true);
            } else {
                return $this->showMsg($aType[$aData['iTypeID']]['sTitle'].'添加失败', false);
            }
        }
        $aCity = Model_City::getDetail($this->iCurrCityID);
        $this->assign('sCityCode', $aCity['sFullPinyin']);
        $this->assign('aType', $aType);

    }


    /**
     * 编辑操作
     * @return null|void
     */
    public function editAction()
    {
        $aType = $this->_getModuleType();
        if ($this->isPost()) {
            $aData = $this->_checkData();
            if (empty($aData)) {
                return null;
            }
            $iOpType = $this->getParam('iOptype');
            if ($iOpType == 1) {
                $aData['iPublishStatus'] = 1;
            } else if ($iOpType == 2){
                $aData['iPublishStatus'] = 2;
            }

            $iAutoID = intval($this->getParam('iAutoID'));
            $aData['iAutoID'] = $iAutoID;
            $aData['iUpdateTime'] = time();
            $aData['iUpdateUserID'] = $this->aCurrUser['iUserID'];
            //var_dump($aData);exit;
            $iRet = Model_Banner::updData($aData);
            $aType = $this->_getModuleType();
            if ($iRet) {
                return $this->showMsg($aType[$aData['iTypeID']]['sTitle'].'信息更新成功！', true);
            } else {
                return $this->showMsg($aType[$aData['iTypeID']]['sTitle'].'信息更新失败！', false);
            }
        } else {
            $iAutoID = intval($this->getParam('id'));
            $aBanner = Model_Banner::getDetail($iAutoID);
            $aCity = Model_City::getDetail($this->iCurrCityID);
            $this->assign('sCityCode', $aCity['sFullPinyin']);
            $this->assign('aType', $aType);
            $this->assign('aBanner', $aBanner);
        }

    }

    /**
     * 预览操作
     */
    public function previewAction()
    {

    }

    /**
     * 下架操作
     */
    public function offlineAction()
    {
        $iAutoID = intval($this->getParam('id'));
        $aDetail = Model_Banner::getDetail($iAutoID);
        if ($aDetail) {
            $aType = $this->_getModuleType();
            $iModuleID = $this->_getModule();
            $iCnt = Model_Banner::getCnt(['where' => ['iStatus' => 1,'iCityID'=>$this->iCurrCityID, 'iPublishStatus' => 1, 'iModuleID' => $iModuleID, 'iTypeID' => $aDetail['iTypeID']]]);
            if ($iCnt <= $aType[$aDetail['iTypeID']]['iMin']) {
                return $this->showMsg('至少保留' . $aType[$aDetail['iTypeID']]['iMin'] . '条'.$aType[$aDetail['iTypeID']]['sTitle'], false);
            }

            $aData = [];
            $aData['iAutoID'] = $iAutoID;
            $aData['iUpdateTime'] = time();
            $aData['iPublishStatus'] = 0;
            $aData['iUpdateUserID'] = $this->aCurrUser['iUserID'];
            $iRet = Model_Banner::updData($aData);
            if ($iRet) {
                return $this->showMsg('下架成功', true);
            }
        }
        return $this->showMsg('下架失败', true);
    }

    /**
     * 上架操作
     */
    public function onlineAction()
    {
        $iAutoID = intval($this->getParam('id'));
        $aDetail = Model_Banner::getDetail($iAutoID);
        if ($aDetail) {
            $aData = [];
            $aData['iAutoID'] = $iAutoID;
            $aData['iUpdateTime'] = time();
            $aData['iPublishStatus'] = 1;
            $aData['iUpdateUserID'] = $this->aCurrUser['iUserID'];
            $iRet = Model_Banner::updData($aData);
            if ($iRet) {
                return $this->showMsg('上架成功', true);
            }
        }
        return $this->showMsg('上架失败', true);
    }


    protected function _checkData()
    {
        $sImage = $this->getParam('sImage');
        $sUrl = $this->getParam('sUrl');
        $iAutoID = $this->getParam('iAutoID');
        $sDesc = $this->getParam('sDesc');
        $iPublishTime = $this->getParam('iPublishTime');
        $iModuleID = $this->_getModule();
        $aType = $this->_getModuleType();
        $aKey = array_keys($aType);
        $iTypeID = $aKey[0];

        if (empty($sImage)) {
            return $this->showMsg('请上传轮播图片', false);
        }

        if (empty($sDesc)) {
            return $this->showMsg('请添加图片描述', false);
        }

        if (empty($sUrl)) {
            return $this->showMsg('请填写广告位URL', false);
        }

        if ($iAutoID > 0) {
            $iCnt = Model_Banner::getCnt(['where' => ['iStatus' => 1, 'iPublishStatus' => 1, 'iCityID' => $this->iCurrCityID, 'iAutoID NOT IN' => [$iAutoID], 'iModuleID' => $iModuleID, 'iTypeID' => $iTypeID]]);
        } else {
            $iCnt = Model_Banner::getCnt(['where' => ['iStatus' => 1, 'iPublishStatus' => 1, 'iCityID' => $this->iCurrCityID, 'iModuleID' => $iModuleID, 'iTypeID' => $iTypeID]]);
        }

        if ($aType[$iTypeID]['iMax'] <= $iCnt) {
            return $this->showMsg('最多添加' . $aType[$iTypeID]['iMax'] . '条', false);
        }

        $aRow = [
            'sImage' => $sImage,
            'iCityID' => $this->iCurrCityID,
            'iModuleID' => $iModuleID,
            'iPublishTime' => strtotime($iPublishTime),
            'iTypeID' => $iTypeID,
            'sUrl' => substr($sUrl,0,4) == 'http' ? $sUrl : 'http://'.$sUrl,
            'sDesc' => $sDesc
        ];

        return $aRow;
    }


    protected function _assignUrl()
    {
        $this->assign('sListUrl', '/Home/Vicepic/');
        $this->assign('sOfflineUrl', '/Home/Vicepic/offline/');
        $this->assign('sOnlineUrl', '/Home/Vicepic/online/');
        $this->assign('sAddUrl', '/Home/Vicepic/add/');
        $this->assign('sEditUrl', '/Home/Vicepic/edit/');
        $this->assign('sPreviewUrl', '/Home/Vicepic/preview/');
    }

}