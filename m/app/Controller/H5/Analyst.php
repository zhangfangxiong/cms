<?php
/**
 * Created by Xcy.
 * Date: 2015/4/28
 * Time: 11:33
 */

class Controller_H5_Analyst extends Controller_Base {

	const PAGE = 1;
	const ROW = 5;

    /**
     * 分析师详情
     */
    public function detailAction () {
    	$aAnalyst = [];
    	$id = intval($this->getParam('id'));
        $page = $this->getParam('page') ? intval($this->getParam('page')) : self::PAGE ;

    	if($id) {
    		$aRet = Sdk_Cms_Analyst::getDetail($id);
    		$aAnalyst['detail'] = $aRet['status'] ? $aRet['data'] : [];
            $aAnalyst['comment'] = $this->_commentList();
    	}

        unset($aAnalyst['detail']['msg']);
        if(!$aAnalyst['detail']) {
            $this->_404();
            return false;
        }

    	$this->assign('aAnalyst', $aAnalyst);
        $this->assign('analystId', $id);
        $this->assign('currentpage', $page);
        $this->aMeta['title'] .= '专属分析师-分析列表';
    }

    /**
     * 分析师楼盘点评列表入口
     */
    public function commentAction () {
    	$aList = $this->_commentList();
    	$this->assign('aList', $aList);
    }

    /**
     * 分析师在线咨询页面
     */
    public function askAction () {
        $id = intval($this->getParam('id'));
        $this->assign('analystId', $id);

        $this->aMeta['title'] .= '专属分析师-在线咨询';
    }

    

    /**
     * 点评列表
     */
    protected function _commentList () {
    	$aList = [];
    	$aParam = [];
    	$aParam['analystID'] = $id = intval($this->getParam('id'));
    	$aParam['iPage'] = $this->getParam('page') ? intval($this->getParam('page')) : self::PAGE ;
    	$aParam['iRows'] = $this->getParam('row') ? intval($this->getParam('row')) : self::ROW ;
    	if($id) {
    		$aRet = Sdk_Cms_Analyst::getComment($aParam);
    		$aList = $aRet['status'] ? $aRet['data'] : [];
    	}
        $this->aMeta['title'] .= '专属分析师-点评列表';
    	return $aList;
    }

}