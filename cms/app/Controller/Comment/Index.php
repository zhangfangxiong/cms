<?php

class Controller_Comment_Index extends Controller_Base
{
    private $_aCheckStatus = array(
        '0' => '待审核',
        '1' => '隐藏',
        '2' => '显示'
    );

    /**
     * 评论列表
     */
    public function indexAction()
    {
        $aParam = array();
        $aParam['iIsCheck'] = $this->getParam('iIsCheck');
        $aParam['sContent'] = $this->getParam('sContent');
        $aParam['iSTime'] = $this->getParam('iSTime');
        $aParam['iETime'] = $this->getParam('iETime');
        $aParam['sLoupanName'] = $this->getParam('sLoupanName');
        $aParam['iLoupanID'] = $this->getParam('iLoupanID') && !empty($aParam['sLoupanName']) ? $this->getParam('iLoupanID') : 0;
        $iPage = intval($this->getParam('page'));
        $sOrder = 'iCreateTime DESC';
        if (!empty($aParam['sOrder'])) {
            $sOrder = $aParam['sOrder'];
        }
        $aParam['sOrder'] = $sOrder;
        $aList = Model_Comment::getPageList($aParam, $iPage, $sOrder);
        $aParam['iSTime'] = strtotime($aParam['iSTime']);
        $aParam['iETime'] = strtotime($aParam['iETime']);
        $this->assign('aParam', $aParam);
        $this->assign('aList', $aList);
        $this->assign('aCheckStatus', $this->_aCheckStatus);

    }

    /**
     * 审核评论
     */
    public function checkAction()
    {
        if ($this->isPost()) {
            $iCommentID = intval($this->getParam('iCommentID'));
            $aComment = Model_Comment::getDetail($iCommentID);
            if (empty($aComment)) {
                return $this->showMsg('评论不存在!', false);
            }
            $data = array(
                'iCommentID' => $iCommentID,
                'iIsCheck' => intval($this->getParam('iIsCheck'))
            );
            if (Model_Comment::updData($data)) {
                return $this->showMsg('审核成功！', true);
            } else {
                return $this->showMsg('审核失败！', false);
            }
        } else {
            $iCommentID = intval($this->getParam('id'));
            if (!$iCommentID) {
                return $this->showMsg('非法操作！', false);
            }
            $aComment = Model_Comment::getDetail($iCommentID);
            if (empty($aComment)) {
                return $this->showMsg('评论不存在！', false);
            }
            $this->assign('aComment', $aComment);
            $this->assign('aCheckStatus', $this->_aCheckStatus);
        }
    }

    /**
     * 删除资讯
     *
     * @return boolean
     */
    public function delAction()
    {
        $iCommentID = $this->getParam('id');
        if (!$iCommentID) {
            return $this->showMsg('非法操作！', false);
        }
        $iRet = Model_Comment::delData($iCommentID);
        if ($iRet) {
            return $this->showMsg('删除成功！', true);
        }
        return $this->showMsg('删除失败！', false);
    }

    /**
     * 编辑资讯
     *
     * @return boolean
     */
    public function editAction()
    {
        if ($this->isPost()) {
            $iCommentID = intval($this->getParam('iCommentID'));
            $iID = intval($this->getParam('id'));
            $sContent = $this->getParam('sContent');
            if (empty($iCommentID) || empty($iID) || $iID != $iCommentID) {
                return $this->showMsg('非法操作', false);
            }
            if (empty($sContent)) {
                return $this->showMsg('内容不能为空', false);
            }
            $data = array(
                'iCommentID' => $iCommentID,
                'sContent' => $sContent
            );
            if (Model_Comment::updData($data)) {
                return $this->showMsg('编辑成功！', true);
            } else {
                return $this->showMsg('编辑失败！', false);
            }
        } else {
            $this->_response->setHeader('Access-Control-Allow-Origin', '*');

            $iCommentID = intval($this->getParam('id'));
            $aComment = Model_Comment::getDetail($iCommentID);
            if (empty($aComment)) {
                return $this->showMsg('评论不存在', false);
            }
            $this->assign('aComment', $aComment);
        }
    }

    /**
     * 增加资讯
     *
     * @return boolean
     */
    public function addAction()
    {
        if ($this->isPost()) {
            $aNews = $this->_checkData();
            if (empty($aNews)) {
                return null;
            }
            $sAction = '保存';
            if ($this->getParam('iOptype') > 0) {
                $aNews['iPublishStatus'] = 1;//发布需要将该字段改为1
                $sAction = '发布';
            }
            //增加需要加上当前添加人ID
            $aCurrUserInfo = $this->aCurrUser;
            $aNews['iUpdateUserID'] = $aCurrUserInfo['iUserID'];
            $aNews['iCreateUserID'] = $aCurrUserInfo['iUserID'];
            $iCommentID = Model_News::addData($aNews);

            if ($iCommentID > 0) {
                return $this->showMsg(['sMsg' => '资讯信息' . $sAction . '成功！', 'iNewsID' => $iCommentID], true);
            } else {
                return $this->showMsg('资讯信息' . $sAction . '失败！', false);
            }
        } else {
            $this->_response->setHeader('Access-Control-Allow-Origin', '*.*');
            $aCategory = Model_Category::getPairCategorys($this->_getTypeCategory());
            $aTag = $this->_getTagList();//Model_Tag::getPairTags($this->_getTypeTag());
            $this->assign('iTypeID', $this->_getTypeID());
            $this->assign('iCityID', $this->_getCityID());
            $this->assign('aCategory', $aCategory);
            $this->assign('aTag', $aTag);
            $this->assign('sUploadUrl', Yaf_G::getConf('upload', 'url'));
            $this->assign('sFileBaseUrl', 'http://' . Yaf_G::getConf('file', 'domain'));
        }
    }

    public function actionAfter()
    {
        parent::actionAfter();
        $this->_assignUrl();
    }

    protected function _assignUrl()
    {
        $this->assign('sListUrl', '/comment/');
        $this->assign('sAddUrl', '/comment/add/');
        $this->assign('sEditUrl', '/comment/edit/');
        $this->assign('sDelUrl', '/comment/del/');
        $this->assign('sCheckUrl', '/comment/check/');
    }

}