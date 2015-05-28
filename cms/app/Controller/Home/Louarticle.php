<?php

/**
 * Created by PhpStorm.
 * User: zfx
 * Date: 2015/3/4
 * Time: 9:29
 */
class Controller_Home_Louarticle extends Controller_Base
{

    protected function _getModule()
    {
        return Model_Banner::MODULE_HOME_LOUARTICLE_AD;
    }

    protected function _getModuleType()
    {
        return Model_Banner::$aModuleType[$this->_getModule()];
    }

    /**
     * 首页广告位列表
     */
    public function indexAction()
    {
        $iCurrCityID = $this->iCurrCityID;
        $aCity = Model_City::getDetail($iCurrCityID);
        $sFullPinyin = $aCity['sFullPinyin'];
        $this->assign('sFullPinyin', $sFullPinyin);
        $aCategory = $this->_getModuleType();
        $this->assign('aCategory', $aCategory);
    }

    /**
     * 切换广告位
     */
    public function changeAction()
    {
        $iCurrCityID = $this->iCurrCityID;
        $aCity = Model_City::getDetail($iCurrCityID);
        $sFullPinyin = $aCity['sFullPinyin'];
        $this->assign('sFullPinyin', $sFullPinyin);
        if ($this->isPost()) {
            return $this->publicAd();
        } else {
            $iCategoryID = $this->getParam('cat_id');
            if ($iCategoryID) {
                $aWhere = [];
                $aWhere['iModuleID'] = $this->_getModule();
                $aWhere['iCityID'] = $this->iCurrCityID;
                $aWhere['iTypeID'] = $iCategoryID;
                $aData = Model_Banner::getBanner($aWhere);
                $aData = $aData ? $aData[0] : $aData;
                $this->assign('aData', $aData);
                $this->assign('cat_id', $iCategoryID);
            }
            $aCategory = $this->_getModuleType();
            $this->assign('aCategory', $aCategory);
        }

    }

    /**
     * 发布广告
     */
    private function publicAd()
    {
        $aType = $this->_getModuleType();
        $aData = $this->_checkData();
        if (empty($aData)) {
            return null;
        }
        $iAutoID = intval($this->getParam('iAutoID'));
        $sAction = '发布';
        if ($iAutoID > 0) {
            $aData['iAutoID'] = $iAutoID;
            $aData['iUpdateTime'] = time();
            $aData['iUpdateUserID'] = $this->aCurrUser['iUserID'];
            $bRet = Model_Banner::updData($aData);
            $sAction = '发布';
        } else {
            $aBanner = Model_Banner::getBanner(array('iCityID'=>$aData['iCityID'],'iModuleID'=>$aData['iModuleID'],'iTypeID'=>$aData['iTypeID']));
            if ( !empty($aBanner)) {
                return $this->showMsg('非法操作', false);
            }
            $aData['iPublishStatus'] = 1;
            $aData['iOrder'] = 0;
            $aData['iCreateTime'] = $aData['iUpdateTime'] = time();
            $aData['iCreateUserID'] = $aData['iUpdateUserID'] = $this->aCurrUser['iUserID'];
            $bRet = Model_Banner::addData($aData);
        }

        if ($bRet) {

            /**
            //初始化模块的模板
            $aTemplate = Model_Template::getTemlateData($aData['iModuleID'],$aData['iTypeID']);
            if (empty($aTemplate)) {
                $aParam = array('iModuleID'=>$aData['iModuleID'],'iTypeID'=>$aData['iTypeID'],'iCreateUserID'=>$this->aCurrUser['iUserID']);
                $iResult = Model_Template::initTmplate($aParam);
                if (!$iResult) {
                    return $this->showMsg($aType[$aData['iTypeID']]['sTitle'] . $sAction . '失败', false);
                }
            }
            */
            return $this->showMsg($aType[$aData['iTypeID']]['sTitle'] . $sAction . '成功', true);
        } else {
            return $this->showMsg($aType[$aData['iTypeID']]['sTitle'] . $sAction . '失败', false);
        }
    }

    protected function _checkData()
    {
        $iCategoryID = $this->getParam('iCategoryID');
        $iNewsID = intval($this->getParam('iNewsID'));
        $iPublishTime = $this->getParam('iPublishTime');
        $iModuleID = $this->_getModule();

        if (empty($iCategoryID)) {
            return $this->showMsg('请选择banner广告位置', false);
        }

        $aNews = Model_News::getNewsByID($iNewsID);
        if (empty($aNews)) {
            return $this->showMsg('该文章ID不存在', false);
        }

        $aRow = [
            'sImage' => $aNews['sImage'],
            'iNewsID' => $iNewsID,
            'iCityID' => $this->iCurrCityID,
            'iModuleID' => $iModuleID,
            'iPublishTime' => strtotime($iPublishTime),
            'iTypeID' => $iCategoryID
        ];

        return $aRow;
    }
}