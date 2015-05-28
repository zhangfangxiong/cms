<?php

class Controller_News_Tag extends Controller_Base
{

    /**
     * 标签列表
     */
    public function indexAction()
    {
        $iPage = intval($this->getParam('page'));
        $aWhere = array(
            'iTypeID' => $this->_getTypeID(),//Model_Tag::TAG_GUIDE_NEWS,
            'iCityID IN' => array(0, $this->iCurrCityID),
            'iStatus' => 1
        );
        $aList = Model_Tag::getList($aWhere, $iPage);
        foreach ($aList['aList'] as $key => $value) {
            if ($value['iCityID']) {
                $aCitys = Model_City::getCityByID($value['iCityID']);
                if (!empty($aCitys)) {
                    $aList['aList'][$key]['sCityName'] = $aCitys['sCityName'];
                }
            } elseif ($value['iCityID'] == '0') {
                $aList['aList'][$key]['sCityName'] = '全国';
            }
        }
        $this->assign('aList', $aList);
    }

    /**
     * 删除标签
     *
     * @return boolean
     */
    public function delAction()
    {
        $iTagID = intval($this->getParam('id'));
        if (Model_Tag::getTagNewsNumByID($iTagID)) {
            return $this->showMsg('该标签下有文章，不能删除', false);
        }
        $iRet = Model_Tag::delData($iTagID);
        if ($iRet == 1) {
            return $this->showMsg('标签删除成功！', true);
        } else {
            return $this->showMsg('标签删除失败！', false);
        }
    }

    /**
     * 编辑标签
     *
     * @return boolean
     */
    public function editAction()
    {
        if ($this->isPost()) {
            $aTag = $this->_checkData();
            if (empty($aTag)) {
                return null;
            }
            $aTag['iTagID'] = intval($this->getParam('iTagID'));
            $aOldTag = Model_Tag::getDetail($aTag['iTagID']);
            if (empty($aOldTag)) {
                return $this->showMsg('标签信息不存在！', false);
            }
            if ($aOldTag['sTagName'] != $aTag['sTagName']) {
                if (Model_Tag::getTagByName($aTag['iTypeID'], $aTag['sTagName'])) {
                    return $this->showMsg('标签信息已经存在！', false);
                }
            }
            if (1 == Model_Tag::updData($aTag)) {
                return $this->showMsg('标签信息更新成功！', true);
            } else {
                return $this->showMsg('标签信息更新失败！', false);
            }
        } else {
            $iTagID = intval($this->getParam('id'));
            $aTag = Model_Tag::getDetail($iTagID);
            $this->assign('iCurrCityID', $this->iCurrCityID);
            $this->assign('aTag', $aTag);
        }
    }

    /**
     * 增加标签
     *
     * @return boolean
     */
    public function addAction()
    {
        if ($this->isPost()) {
            $aTag = $this->_checkData();
            if (Model_Tag::getTagByName($aTag['iTypeID'], $aTag['sTagName'])) {
                return $this->showMsg('标签信息已经存在！', false);
            }
            if (Model_Tag::addData($aTag) > 0) {
                return $this->showMsg('标签增加成功！', true);
            } else {
                return $this->showMsg('标签增加失败！', false);
            }
        }
    }

    /**
     * 请求数据检测
     *
     * @return mixed
     */
    public function _checkData($sType = 'add')
    {
        $sTagName = $this->getParam('sTagName');
        $sDesc = $this->getParam('sDesc');
        $iCityID = $this->getParam('iCityID');
        $sPermissionApi = $this->getParam('sPermissionApi');
        if ($iCityID && $iCityID != $this->iCurrCityID) {
            return $this->showMsg('非法请求！', false);
        }
        if (!Util_Validate::isCLength($sTagName, 1, 6)) {
            return $this->showMsg('标签名长度范围为1到6个字！', false);
        }
        $aRow = array(
            'sTagName' => $sTagName,
            'sDesc' => $sDesc,
            'iCityID' => $iCityID,
            'iParentID' => 0,
            'iTypeID' => $this->_getTypeID()//Model_Tag::TAG_GUIDE_NEWS
        );
        return $aRow;
    }

    protected function _getTypeID()
    {
        return Model_Tag::TAG_GUIDE_NEWS;
    }

    public function actionAfter()
    {
        parent::actionAfter();
        $this->_assignUrl();
    }

    protected function _assignUrl()
    {
        $this->assign('sListUrl', '/news/tag/');
        $this->assign('sAddUrl', '/news/tag/add/');
        $this->assign('sEditUrl', '/news/tag/edit/');
        $this->assign('sDelUrl', '/news/tag/del/');
    }
}