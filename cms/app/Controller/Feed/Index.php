<?php

class Controller_Feed_Index extends Controller_Base
{

    /**
     * 资讯列表
     */
    public function indexAction ()
    {
        $aParam = array();
        $aParam['iCategoryID'] = $this->getParam('iCategoryID');
        $aParam['iPublishStatus'] = $this->getParam('iPublishStatus');
        $aParam['sTitle'] = $this->getParam('sTitle');
        $aParam['sAuthor'] = $this->getParam('sAuthor');
        $aParam['sOrder'] = $this->getParam('sOrder');
        $this->assign('aParam', $aParam);
        
        $iPage = intval($this->getParam('page'));
        $aWhere = array(
        	'iCityID' => $this->iCurrCityID,
            'iStatus' => 1
        );
        if (isset($aParam['iCategoryID']) && $aParam['iCategoryID'] > 0) {
            $aWhere['iCategoryID'] = $aParam['iCategoryID'];
        }
        if (isset($aParam['iPublishStatus']) && $aParam['iPublishStatus'] != -1) {
            $aWhere['iPublishStatus'] = $aParam['iPublishStatus'];
        }
        if (!empty($aParam['sTitle'])) {
            $aWhere['sTitle LIKE'] = '%' . $aParam['sTitle'] . '%';
        }
        if (!empty($aParam['sAuthor'])) {
            $aWhere['sAuthor LIKE'] = '%' . $aParam['sAuthor'] . '%';
        }
        
        $sOrder = '';
        if (!empty($aParam['sOrder'])) {
            $sOrder = $aParam['sOrder'];
        }
             
        $aList = Model_Feed::getList($aWhere, $iPage, $sOrder);
        $this->assign('aList', $aList);

        $aCategory = Model_Category::getPairCategorys(Model_Category::CATEGORY_Feed);
        $this->assign('aCategory', $aCategory);
    }
    
    /**
     * 发布资讯
     */
    public function publishAction ()
    {
        $iFeedID = intval($this->getParam('id'));
        $aFeed = Model_Feed::getDetail($iFeedID);
        if (empty($aFeed)) {
            return $this->showMsg('发布的资讯不存在！', false);
        }
        
        $aRow = array(
            'iFeedID' => $iFeedID,
            'iPublishStatus' => $aFeed['iPublishStatus'] == 1 ? 0 : 1
        );
        $iRet = Model_Feed::updData($aRow);
        
        $sType = $aFeed['iPublishStatus'] == 1 ? '下架' : '上架';
        if ($iRet == 1) {
            return $this->showMsg('资讯' . $sType . '成功！', true);
        } else {
            return $this->showMsg('资讯' . $sType . '失败！', false);
        }
    }

    /**
     * 删除资讯
     * 
     * @return boolean
     */
    public function delAction ()
    {
        $iFeedID = intval($this->getParam('id'));
        $iRet = Model_Feed::delData($iFeedID);
        if ($iRet == 1) {
            return $this->showMsg('资讯删除成功！', true);
        } else {
            return $this->showMsg('资讯删除失败！', false);
        }
    }

    /**
     * 编辑资讯
     * 
     * @return boolean
     */
    public function editAction ()
    {
        if ($this->isPost()) {
            $aFeed = $this->_checkData();
            if (empty($aFeed)) {
                return null;
            }
            $aFeed['iFeedID'] = intval($this->getParam('iFeedID'));
            if (1 == Model_Feed::updData($aFeed)) {
                return $this->showMsg('资讯信息更新成功！', true);
            } else {
                return $this->showMsg('资讯信息更新失败！', false);
            }
        } else {
            $iFeedID = intval($this->getParam('id'));
            $aFeed = Model_Feed::getDetail($iFeedID);
            $aFeed['aTag'] = explode(',', $aFeed['sTag']);
            $this->assign('aFeed', $aFeed);
            
            $aCategory = Model_Category::getPairCategorys(Model_Category::CATEGORY_Feed);
            $aTag = Model_Tag::getPairTags(Model_Tag::TAG_Feed);
            $aCity = Model_City::getDetail($this->iCurrCityID);
            $aLoupan = Model_CricUnit::getLoupanNames($aFeed['sLoupanID']);
            
            $this->assign('aCity', $aCity);
            $this->assign('aCategory', $aCategory);
            $this->assign('aTag', $aTag);
            $this->assign('aLoupan', $aLoupan);
        }
    }

    /**
     * 增加资讯
     * 
     * @return boolean
     */
    public function addAction ()
    {
        if ($this->isPost()) {
            $aFeed = $this->_checkData();
            if (empty($aFeed)) {
                return null;
            }
            if (Model_Feed::addData($aFeed) > 0) {
                return $this->showMsg('资讯增加成功！', true);
            } else {
                return $this->showMsg('资讯增加失败！', false);
            }
        } else {
        	//$this->_response->setHeader('Access-Control-Allow-Origin', '*');
            $aCategory = Model_Category::getPairCategorys(Model_Category::CATEGORY_Feed);
            $aTag = Model_Tag::getPairTags(Model_Tag::TAG_Feed);
            $aCity = Model_City::getDetail($this->iCurrCityID);
            $this->assign('aCity', $aCity);  
            $this->assign('aCategory', $aCategory);
            $this->assign('aTag', $aTag);
        }
    }

    /**
     * 请求数据检测
     * 
     * @return mixed
     */
    public function _checkData ($sType = 'add')
    {
        $sTitle = $this->getParam('sTitle');
        $iCategoryID = intval($this->getParam('iCategoryID'));
        $sAuthor = $this->getParam('sAuthor');
        $sAbstract = $this->getParam('sAbstract');
        $sContent = $this->getParam('sContent');
        $sLoupanID = $this->getParam('sLoupanID');
        $sSource = $this->getParam('sSource');
        $sTag = $this->getParam('sTag');
        
        if (! Util_Validate::isLength($sTitle, 2, 100)) {
            return $this->showMsg('资讯标题长度范围为2到100个字！', false);
        }
        if (! Util_Validate::isLength($sAuthor, 2, 20)) {
            return $this->showMsg('资讯作者长度范围为2到20个字！', false);
        }
        if (! Util_Validate::isLength($sAbstract, 10, 10000)) {
            return $this->showMsg('资讯摘要长度范围为10到10000个字！', false);
        }
        if (! Util_Validate::isLength($sContent, 100, 16777215)) {
            return $this->showMsg('资讯内容长度范围为100到65535个字！', false);
        }
        if ($iCategoryID < 0) {
            return $this->showMsg('请选择一个资讯分类！', false);
        }
        $aRow = array(
            'sTitle' => $sTitle,
        	'iCityID' => $this->iCurrCityID,
            'iCategoryID' => $iCategoryID,
            'sAuthor' =>$sAuthor,
            'sAbstract' => $sAbstract,
            'sContent' => $sContent,
            'sLoupanID' => $sLoupanID,
            'sSource' => $sSource,
            'sTag' => $sTag
        );
        return $aRow;
    }
}