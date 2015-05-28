<?php
/**
 * Created by PhpStorm.
 * User: xiaoyao
 * Date: 2015/2/4
 * Time: 18:48
 */

class Controller_Banner_Advertisement extends Controller_Banner_Base
{



    protected function _getModule()
    {
        return Model_Banner::MODULE_NEWS_HOME_AD;
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

        $aType = $this->_getModuleType();
        if ($this->isPost()) {
            $aData = $this->_checkData();

            if (empty($aData)) {
                return null;
            }
            $row = Model_Banner::getRow(
                [
                    'where' => [
                        'iTypeID' => $aData['iTypeID'],
                        'iCityID' => $this->iCurrCityID,
                        'iModuleID' => $aData['iModuleID'],
                    ]
                ]
            );

            $iAutoID = intval($this->getParam('iAutoID'));
            if ($iAutoID == 0 && $row) {
                $iAutoID = $row['iAutoID'];
            }
            $sAction = '发布';
            if ($iAutoID > 0) {
                $aData['iAutoID'] = $iAutoID;
                $aData['iUpdateTime'] = time();
                $aData['iUpdateUserID'] = $this->aCurrUser['iUserID'];
                $bRet = Model_Banner::updData($aData);
                $sAction = '发布';
            } else {
                $aData['iPublishStatus'] = 1;
                $aData['iOrder'] = 0;
                $aData['iCreateTime'] = $aData['iUpdateTime'] = time();
                $aData['iCreateUserID'] = $aData['iUpdateUserID'] = $this->aCurrUser['iUserID'];
                $bRet = Model_Banner::addData($aData);
            }

            if ($bRet) {
                return $this->showMsg($aType[$aData['iTypeID']]['sTitle'].$sAction.'成功', true);
            } else {
                return $this->showMsg($aType[$aData['iTypeID']]['sTitle'].$sAction.'失败', false);
            }
        } else {

            $aKey = array_keys($aType);
            $iTypeID = $aKey[0];
            $aWhere = [];
            $aWhere['iModuleID'] = $this->_getModule();
            $aWhere['iCityID'] = $this->iCurrCityID;
            $aWhere['iTypeID'] = $iTypeID;
            $aList = Model_Banner::getBanner($aWhere);
            $aData = [];
            if ($aList) {
                $aData = $aList[0];
            }
            $aCity = Model_City::getDetail($this->iCurrCityID);
            $this->assign('sCityCode', $aCity['sFullPinyin']);
            $this->assign('aData', $aData);
        }
    }

    protected function _checkData()
    {
        $sImage = $this->getParam('sImage');
        $sUrl = $this->getParam('sUrl');
        $iPublishTime = $this->getParam('iPublishTime');
        $sDesc = $this->getParam('sDesc');
        $iModuleID = $this->_getModule();
        $aType = $this->_getModuleType();
        $aKey = array_keys($aType);
        $iTypeID = $aKey[0];

        if (empty($sImage)) {
            return $this->showMsg('请上传轮播图片', false);
        }

        $aRow = [
            'sImage' => $sImage,
            'sUrl' => $sUrl,
            'iCityID' => $this->iCurrCityID,
            'iModuleID' => $iModuleID,
            'sDesc' => $sDesc,
            'iPublishTime' => strtotime($iPublishTime),
            'iTypeID' => $iTypeID
        ];

        return $aRow;
    }
}