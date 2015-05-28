<?php

/**
 * 评测报告分析师接口
 * User: xcy
 * Date: 2015/5/5
 * Time: 11:17
 */
class Controller_Ajax_Xf extends Controller_Ajax_Base {

	const PAGE = 1;
	const ROW = 5;
    const KEY = '33BF29E2-A974-401F-8F6A-09A82E25A79D';
    private $_cityId = 0;

    static public $aEva = [    
                        1 => [
                                'pre' => 'hx_',
                                'class' => 'hx_analyze',
                                'name'  => '户型分析', 
                            ],
                        2 => [
                                'pre' => 'zx_',
                                'class' => 'zx_stander',
                                'name'  => '装修标准', 
                            ],
                        3 => [
                                'pre' => 'sq_',
                                'class' => 'sq_sever',
                                'name'  => '社区品质', 
                            ],
                        4 => [
                                'pre' => 'wy_',
                                'class' => 'wy_sever',
                                'name'  => '物业服务', 
                            ],
                        5 => [
                                'pre' => 'jt_',
                                'class' => 'jt_traffic',
                                'name'  => '交通出行', 
                            ],
                        6 => [
                                'pre' => 'sq_',
                                'class' => 'sq_Supporting',
                                'name'  => '周边配套', 
                            ],
                        7 => [
                                'pre' => 'bl_',
                                'class' => 'bad_factor',
                                'name'  => '不利因素', 
                            ]
                ];

    /**
     * 执行动作之前
     */
    public function actionBefore () {
        parent::actionBefore();

        $aCity = self::getCurrCity();
        $this->_cityId = $aCity['iCodeID'];

        $this->setFrame('newsh5frame.phtml');
    }

    /**
     * 新房列表页
     */
    public function xfListAction () {
        $aList = [];
        $regionId = intval($this->getParam('regionId'));
        $blockId = intval($this->getParam('blockId'));
        
        $aParam = [];       
        $aParam['cityID'] = $cityId = $this->_cityId;       
        $aParam['iPage'] = $this->getParam('page') ? intval($this->getParam('page')) : self::PAGE ;
        $aParam['iRows'] = $this->getParam('row') ? intval($this->getParam('row')) : self::ROW ;
        $this->getParam('priceId') ? $aParam['priceID'] = intval($this->getParam('priceId')) : '';
        $this->getParam('layoutId') ? $aParam['layoutID'] = intval($this->getParam('layoutId')) : '';
        $blockId ? $aParam['regionID'] = $blockId : ($regionId ? $aParam['regionID'] = $regionId : '');
        $this->getParam('featureID') ? $aParam['featureID'] = intval($this->getParam('featureID')) : '';
        $this->getParam('keyword') ? $aParam['keyword'] = addslashes($this->getParam('keyword')) : '';

        $aXf = Sdk_Cms_Xf::getXfList($aParam);
        $aList = $aXf['status'] ? $aXf['data'] : [];
        return $this->showMsg($aList, true);
    }

	/**
     * 热销楼盘
     */
    public function hotAction () {
        $aList = [];
        $aParam = [];       
        $aParam['cityID'] = $cityId = $this->_cityId;   
        $aParam['iPage'] = $this->getParam('page') ? intval($this->getParam('page')) : self::PAGE ;
        $aParam['iRows'] = $this->getParam('row') ? intval($this->getParam('row')) : self::ROW ;        
        $this->getParam('featureID') ? $aParam['featureID'] = intval($this->getParam('featureID')) : '';

        if($cityId) {
            $aHot = Sdk_Cms_Xf::getXfList($aParam);
            $aList['hotlist']  = $aHot['status'] ? $aHot['data']['list'] : [];
            return $this->showMsg($aList, true);
        }

        return $this->showMsg('数据有误', false);
    }

    /**
     * 评测列表
     */
    public function evaluationAction () {
        $aList['aEva'] = self::$aEva;
        $aParam = [];
        $regionId = intval($this->getParam('regionId'));
        $blockId = intval($this->getParam('blockId'));
        $aParam['cityID'] = $cityId = $this->_cityId;
        $aParam['iPage'] = $this->getParam('page') ? intval($this->getParam('page')) : self::PAGE ;
        $aParam['iRows'] = $this->getParam('row') ? intval($this->getParam('row')) : self::ROW ;
        $aParam['priceID'] = intval($this->getParam('priceId'));
        $aParam['regionID'] = $blockId ? $blockId : $regionId;

        if($cityId) {
            $aEvaluation = Sdk_Cms_Analyst::getEvaluationList($aParam);
            $aList['evaluation'] = $aEvaluation['status'] ? $aEvaluation['data']['list'] : [];
            return $this->showMsg($aList, true);
        }

        return $this->showMsg('数据有误', false);
    }

    /**
     * 获取楼栋信息
     */
    public function unitInfoAction () {
        $aList = [];
        $id = intval($this->getParam('id'));
        $code = $this->getParam('code') ? $this->getParam('code') : 'A';
        if($id) {
            $aUnitInfo = Sdk_Cms_Xf::getHouseUnit($id, $code);
            $aList = $aUnitInfo['status'] ? $aUnitInfo['data'] : [];
            return $this->showMsg($aList, true);
        }
        return $this->showMsg('数据有误', false);
    }

    /**
     * 获取贷款信息
     */
    public function loanInfoAction () {
        $aList = [];
        $id = intval($this->getParam('id'));
        $roomId = $this->getParam('roomId');
        if($id && $roomId) {
            $aUnitInfo = Sdk_Cms_Xf::getLoan($id, $roomId);  
            $aList = $aUnitInfo['status'] ? $aUnitInfo['data'] : [];
            return $this->showMsg($aList, true);
        }
        return $this->showMsg('数据有误', false);
    }

    /**
     * 网友点评列表翻页
     */
    public function commentListAction () {
        $aComments = [];
        $aParam = [];
        $aParam['buildingID'] = $id = intval($this->getParam('id'));
        $aParam['iPage'] = $this->getParam('page') ? intval($this->getParam('page')) : self::PAGE ;
        $aParam['iRows'] = $this->getParam('row') ? intval($this->getParam('row')) : 10 ;
        if($id) {
            $aRet = Sdk_Cms_Xf::getComment($aParam);
            $aComments = $aRet['status'] ? $aRet['data'] : [];
            return $this->showMsg($aComments, true);
        }
        return $this->showMsg('数据有误', false);
    }

    /**
     * 提交评论
     */
    public function addCommentAction () {
        $aParam['userID'] = $this->getParam('uid') ? intval($this->getParam('uid')) : 0;
        $aParam['buildingID'] = $id = intval($this->getParam('id'));
        $aParam['content'] = $content = addslashes($this->getParam('content'));
        if(50 < mb_strlen($content)) {
            return $this->showMsg('评论超长', false);
        }
        if($id && !empty($content)) {
            $aRet = Sdk_Cms_Xf::addComment($aParam);
            $aAdd = $aRet['status'] ? $aRet['data'] : [];
            return $this->showMsg($aAdd, true);
        }
        return $this->showMsg('数据有误', false);
    }

    /**
     * 理想家
     */
    public function addDreamerAction () {
        $aParam['buildingID'] = $id = intval($this->getParam('buildId'));
        $aParam['name'] = $name = addslashes($this->getParam('userName'));
        $aParam['phoneNum'] = $phone = addslashes($this->getParam('userPhone'));
        if($id && !empty($phone) && !empty($name)) {
            $aRet = Sdk_Cms_Xf::addDreamer($aParam);
            $aAdd = $aRet['status'] ? $aRet['data'] : [];
            return $this->showMsg($aAdd, true);
        }
        return $this->showMsg('数据有误', false);
    }   

}