<?php
/**
 * 400电话管理
 * @author: xuchuyuan
 */
class Controller_Phone_Tel extends Controller_Base {

	const page = 1;
	const status_all = -1;
	const status_valid = 1;
	const status_unvalid = 0;

	/**
     *  400电话列表
     */
    public function listAction() {
    	$aWhere = [];
    	$sOrder = 'iUpdateTime desc';

    	$this->getParam('tel') ? $aWhere['sTel'] = trim($this->getParam('tel')) : '';
    	$this->getParam('ext') ? $aWhere['iExt'] = trim($this->getParam('ext')) : '';
    	$aWhere['iStatus'] = ($this->getParam('status') === null) ? -1 : intval($this->getParam('status')) ;
    	$iPage = $this->getParam('page') ? intval($this->getParam('page')) : self::page;   

    	if(self::status_all == $aWhere['iStatus'])	unset($aWhere['iStatus']);
    	$aList = Model_Tel::getList($aWhere, $iPage, $sOrder);
        $this->assign('aList', $aList);
        $this->assign('aWhere', $aWhere);
    }



    public function addAction() {
		$this->getParam('id') ? $pkid = intval($this->getParam('id')) : '';

		$aDetail = isset($pkid) ? Model_Tel::getDetail($pkid) : [];
		$this->assign('aDetail', $aDetail);
		$this->assign('sListUrl', '/phone/tel/list/');
    }



    public function saveAction() {
    	$aWhere = [];
    	$aTel= ['iStatus' => 1];

		$this->getParam('id') ? $aWhere['iAutoID'] = intval($this->getParam('id')) : '';
		$this->getParam('servid') ? $aWhere['sServID'] = intval($this->getParam('servid')) : '';
		$this->getParam('servtype') ? $aWhere['iSevType'] = intval($this->getParam('servtype')) : '';
		$this->getParam('tel') ? $aTel['sTel'] = $aWhere['sTel'] = intval($this->getParam('tel')) : '';
		$this->getParam('ext') ? $aTel['iExt'] = $aWhere['iExt'] = intval($this->getParam('ext')) : '';

		if(empty($aWhere['sServID']) || empty($aWhere['iSevType']) 
			|| empty($aWhere['sTel']) || empty($aWhere['iExt'])) {
			return $this->showMsg(['sMsg' => '请填写必填参数'], false);
		}

		$aParam['where'] = $aTel;
		if(isset($aWhere['iAutoID'])){
			$aDetail = isset($aTel['sTel']) && isset($aTel['iExt']) ? Model_Tel::getRow($aParam) : [];
			if($aDetail && $aDetail['iAutoID'] != $aWhere['iAutoID'])
				return $this->showMsg(['sMsg' => '电话和分机号已存在，添加失败！'], false);

			$updCnt = Model_Tel::updData($aWhere);
			return $updCnt ? $this->showMsg(['sMsg' => '修改400电话成功！'], true) 
							: $this->showMsg(['sMsg' => '修改400电话失败！'], false);
		}else{			
			$aCnt = (isset($aTel['sTel']) && isset($aTel['iExt'])) ? Model_Tel::getCnt($aParam) : 0;
			if(0 < $aCnt)
			return $this->showMsg(['sMsg' => '电话和分机号已存在，添加失败！'], false);

			$addID = Model_Tel::addData($aWhere);
			return $addID ? $this->showMsg(['sMsg' => '添加400电话成功！', 'iAutoID' => $addID ], true)
							: $this->showMsg(['sMsg' => '修改400电话失败！'], false);
		}
	}

	/**
	 * [updStatusAction description]
	 * 修改状态
	 * @return json
	 */
	public function updStatusAction() {
		$aWhere = [];
		$this->getParam('id') ? $aWhere['iAutoID'] = intval($this->getParam('id')) : '';
		$aWhere['iStatus'] = $this->getParam('status') ? intval($this->getParam('status')) : self::status_unvalid;

		if((self::status_valid != $aWhere['iStatus']) && (self::status_unvalid != $aWhere['iStatus']) ) {
			return $this->showMsg(['sMsg' => '传入状态无效'], false);
		}

		$aDetail = isset($aWhere['iAutoID']) ? Model_Tel::getDetail($aWhere['iAutoID']) : [];
		if(!$aDetail) {
			return $this->showMsg(['sMsg' => '无此id数据'], false);
		}
		if($aDetail && intval($aWhere['iStatus']) === intval($aDetail['iStatus'])) {
			return $this->showMsg(['sMsg' => '状态已存在'], false);
		}

		$updCnt = Model_Tel::updData($aWhere);
		return $updCnt ? $this->showMsg(['sMsg' => '修改状态成功！'], true) 
						: $this->showMsg(['sMsg' => '修改状态失败！'], false);
    }


}