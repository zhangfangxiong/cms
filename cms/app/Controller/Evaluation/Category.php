<?php

class Controller_Feed_Category extends Controller_Base
{

    /**
     * 分类列表
     */
    public function indexAction ()
    {
        $iPage = intval($this->getParam('page'));
        $aWhere = array(
            'iTypeID' => Model_Category::CATEGORY_Feed,
            'iStatus' => 1
        );
        $aList = Model_Category::getList($aWhere, $iPage);
        $this->assign('aList', $aList);
    }

    /**
     * 删除分类
     * 
     * @return boolean
     */
    public function delAction ()
    {
        $iCategoryID = intval($this->getParam('id'));
        $iRet = Model_Category::delData($iCategoryID);
        if ($iRet == 1) {
            return $this->showMsg('分类删除成功！', true);
        } else {
            return $this->showMsg('分类删除失败！', false);
        }
    }

    /**
     * 编辑分类
     * 
     * @return boolean
     */
    public function editAction ()
    {
        if ($this->isPost()) {
            $aCategory = $this->_checkData();
            if (empty($aCategory)) {
                return null;
            }
            $aCategory['iCategoryID'] = intval($this->getParam('iCategoryID'));
            $aOldCategory = Model_Category::getDetail($aCategory['iCategoryID']);
            if (empty($aOldCategory)) {
                return $this->showMsg('分类信息不存在！', false);
            }
            if ($aOldCategory['sCategoryName'] != $aCategory['sCategoryName']) {
                if (Model_Category::getCategoryByName($aCategory['iTypeID'], $aCategory['sCategoryName'])) {
                    return $this->showMsg('分类信息已经存在！', false);
                }
            }
            if (1 == Model_Category::updData($aCategory)) {
                return $this->showMsg('分类信息更新成功！', true);
            } else {
                return $this->showMsg('分类信息更新失败！', false);
            }
        } else {
            $iCategoryID = intval($this->getParam('id'));
            $aCategory = Model_Category::getDetail($iCategoryID);
            $this->assign('aCategory', $aCategory);
        }
    }

    /**
     * 增加分类
     * 
     * @return boolean
     */
    public function addAction ()
    {
        if ($this->isPost()) {
            $aCategory = $this->_checkData();
            if (Model_Category::getCategoryByName($aCategory['iTypeID'], $aCategory['sCategoryName'])) {
                return $this->showMsg('分类信息已经存在！', false);
            }
            if (Model_Category::addData($aCategory) > 0) {
                return $this->showMsg('分类增加成功！', true);
            } else {
                return $this->showMsg('分类增加失败！', false);
            }
        }
    }

    /**
     * 请求数据检测
     * 
     * @return mixed
     */
    public function _checkData ($sType = 'add')
    {
        $sCategoryName = $this->getParam('sCategoryName');
        $sDesc = $this->getParam('sDesc');
        $sPermissionApi = $this->getParam('sPermissionApi');
        
        if (! Util_Validate::isLength($sCategoryName, 2, 30)) {
            return $this->showMsg('分类名长度范围为2到30个字！', false);
        }
        $aRow = array(
            'sCategoryName' => $sCategoryName,
            'sDesc' => $sDesc,
            'iParentID' => 0,
            'iTypeID' => Model_Category::CATEGORY_FEED
        );
        return $aRow;
    }
}