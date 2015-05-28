<?php

/**
 * Created by PhpStorm.
 * User: xiaoyao
 * Date: 2015/1/27
 * Time: 9:29
 */
class Controller_News_Author extends Controller_Base
{

    /**
     * 作者列表
     */
    public function indexAction()
    {
        $aParam = $this->getParams();
        $iPage = isset($aParam['page']) ? intval($aParam['page']) : 1;
        $aAuthorList = Model_Author::getAuthorList($aParam, $iPage, $this->_getTypeID());//Model_Author::AUTHOR_GUIDE_NEWS
        //print_r($aAuthorList);
        $this->assign('aParam', $aParam);
        $this->assign('aAuthorList', $aAuthorList);
    }

    /**
     * 作者添加
     */
    public function addAction()
    {
        if ($this->isPost()) {
            $aAuthor = $this->_checkData();
            if (empty($aAuthor)) {
                return null;
            }
            $iRet = Model_Author::addData($aAuthor);
            if ($iRet) {
                return $this->showMsg('作者添加成功！', true);
            } else {
                return $this->showMsg('作者添加失败！', false);
            }
        }
    }

    public function editAction()
    {
        $iAuthorID = intval($this->getParam('iAuthorID'));
        $aAuthor = [];
        if ($iAuthorID > 0) {
            $aAuthor = Model_Author::getDetail($iAuthorID);
        }

        if ($this->isPost()) {
            $aAuthorData = $this->_checkData();
            if (empty($aAuthorData)) {
                return null;
            }
            if ($iAuthorID <= 0) {
                return $this->showMsg('作者ID参数错误，请退回列表页，重新编辑', true);
            }

            $aAuthorData['iAuthorID'] = $iAuthorID;
            //var_dump($aAuthorData);exit;
            $iRet = Model_Author::updData($aAuthorData);
            if ($iRet) {
                return $this->showMsg('作者编辑成功！', true);
            } else {
                return $this->showMsg('作者编辑失败！', false);
            }
        }
        if ($aAuthor) {
            $aCity = Model_City::getDetail($aAuthor['iCityID']);
            $aAuthor['sCityName'] = $aCity['sCityName'];
        }
        $this->assign('aAuthor', $aAuthor);

    }

    protected function _checkData()
    {
        $sAuthorName = $this->getParam('sAuthorName');
        $iCityID = intval($this->getParam('iCityID'));
        $sPosition = $this->getParam('sPosition');
        $sSignature = $this->getParam('sSignature');
        $iStatus = $this->getParam('iStatus');
        $sHead = $this->getParam('sHead');
        if (empty($sAuthorName)) {
            return $this->showMsg('请填写作者', false);
        }

        $iLenth = Util_Tools::strlen($sAuthorName);
        if (!Util_Validate::isCLength($sAuthorName, 1, 10)) {
            return $this->showMsg('作者长度在1~10个字符', false);
        }

        if (empty($iCityID)) {
            return $this->showMsg('请选择地区', false);
        }
        if ($iStatus != 1) {
            $iStatus = 0;
        } else {
            $iStatus = 1;
        }
        $aRow =  [
            'sAuthorName' => Util_Tools::substr($sAuthorName,0, 50),
            'iCityID' => $iCityID,
            'sPosition' => Util_Tools::substr($sPosition,0, 50),
            'sSignature' => Util_Tools::substr($sSignature,0, 500),
            'sHead' => $sHead,
            'iStatus' => $iStatus,
            'iTypeID' => $this->_getTypeID() //Model_Author::AUTHOR_GUIDE_NEWS
        ];
        return $aRow;
    }

    /**
     * 作者置为无效
     */
    public function delAction()
    {
        $iAuthorID = intval($this->getParam('id'));
        if ($iAuthorID) {
            Model_Author::delData($iAuthorID);
            return $this->showMsg('作者置为无效成功',true);
        } else {
            return $this->showMsg('作者置为无效失败',false);
        }
    }

    public function actionAfter()
    {
        parent::actionAfter();
        $this->_assignUrl();
    }

    protected function _getTypeID()
    {
        return Model_Author::AUTHOR_GUIDE_NEWS;
    }

    protected function _assignUrl()
    {
        $this->assign('sListUrl', '/news/author/');
        $this->assign('sAddUrl', '/news/author/add/');
        $this->assign('sDelUrl', '/news/author/del/');
        $this->assign('sEditUrl', '/news/author/edit/');
    }
}