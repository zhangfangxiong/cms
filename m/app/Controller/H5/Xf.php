<?php
/**
 * Created by Xcy.
 * Date: 2015/4/28
 * Time: 11:33
 */

class Controller_H5_Xf extends Controller_Base {

	const PAGE = 1;
	const ROW = 5;
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
		$aList['xflist']  = $aXf['status'] ? $aXf['data'] : [];
    	$aList['filter']  = $cityId ? $this->_filter($cityId)  : [];
    	$aList['feature'] = $cityId ? $this->_feature($cityId) : [];

    	$this->assign('aList', $aList);
    }

    /**
     * 新房单页
     */
    public function xfDetailAction () {
    	$aDetail = [];
    	$id = intval($this->getParam('id'));
    	if($id) {
    		$aRet = Sdk_Cms_Xf::getDetail($id);
    		$aDetail = $aRet['status'] ? $aRet['data'] : [];
    	}

        if(isset($aDetail['msg'])) unset($aDetail['msg']);
        if(!$aDetail) {
            $this->_404();
            return false;
        }

        $current = date('Y-n', time());
        $aCurrent = explode('-', $current);
        $this->assign('aCurrent', $aCurrent);

        $aCity = self::getCurrCity();
        $city = $aCity['sName'];

        $aType = [];
        $aType['total'] = $this->getParam('total') ? intval($this->getParam('total')) : ' ';
        $aType['area'] = $this->getParam('area');
        $aType['type'] = $this->getParam('type');
        $aType['typecode'] = $this->getParam('typecode');
        $this->assign('aType', $aType);

        $sRootPhone = '400-810-6999';
        $sString = ' 转 ';
        $aPhone = [];
        $sPhone = '';
        $aTel = [];
        $aAnalyst = Util_Cookie::get('analyst'.$aCity['iCodeID']);
        if (!empty($aAnalyst)) {
            $aPhone = explode(',', $aAnalyst['sTel']);
            $sPhone = isset($aPhone[1]) ? $aPhone[1] : '';
        }

        $aTel['sTelHref'] = isset($aAnalyst['sTel']) ? $aAnalyst['sTel'] : $sRootPhone;
        $aTel['sTelphone'] = !empty($sPhone) ? $sRootPhone.$sString.$sPhone : $sRootPhone;
        $this->assign('aTel', $aTel);

    	$this->assign('aDetail', $aDetail);
        $this->assign('aEva', self::$aEva);

        $this->assign('city', $city);
        $this->assign('hasRoot', 1);

        $this->aMeta['title'] .= '新房-新房单页';
        $this->assign('aMeta', $this->aMeta);
    }

    /**
     * 楼盘相册
     */
    public function xfImageAction () {
    	$aImage = [];
    	$id = intval($this->getParam('id'));
    	if($id) {
    		$aRet = Sdk_Cms_Xf::getImage($id);
    		$aImage = $aRet['status'] ? $aRet['data'] : [];
    	}

    	$this->assign('aImage', $aImage);

        $this->aMeta['title'] .= '相册';
        $this->assign('aMeta', $this->aMeta);
    }

    /**
     * 周边配套
     */
    public function slideAction () {
        $aLoupan = [];
        $aLoupan['type'] = $this->getParam('type') ? $this->getParam('type') : '';
        $aLoupan['city'] = $this->getParam('city');
        $aLoupan['lat']  = $this->getParam('lat');
        $aLoupan['long'] = $this->getParam('long');
        $aLoupan['name'] = $this->getParam('name');
        $this->assign('aLoupan', $aLoupan);

        $this->aMeta['title'] .= '周边配套';
        $this->assign('aMeta', $this->aMeta);
    }

    /**
     * 获取户型列表
     */
    public function houseTypeAction () {
    	$aHouseType = [];
    	$id = intval($this->getParam('id'));
    	if($id) {
    		$aRet = Sdk_Cms_Xf::getHouseType($id);
    		$aHouseType = $aRet['status'] ? $aRet['data'] : [];
    	}
        $this->assign('loupanId', $id);
    	$this->assign('aHouseType', $aHouseType);
    }

    /**
     * 选择户型列表
     */
    public function houseTypeSelectAction () {
        return self::houseTypeAction();
    }

    /**
     * 获取详情
     */
    public function unitAction () {
    	$aUnit = [];
    	$id   = intval($this->getParam('id'));
    	$type = $this->getParam('type');
    	if($id) {
    		$aRet = Sdk_Cms_Xf::getHouseUnit($id);
    		$aUnit = $aRet['status'] ? $aRet['data'] : [];
    	}

    	$this->assign('aUnit', $aUnit);
    }

    /**
     * 房贷计算
     */
    public function loanAction () {
    	$aLoan = [];
    	$id   = intval($this->getParam('id'));
    	$roomId = $this->getParam('roomId');
    	if($id) {
    		$aRet = Sdk_Cms_Xf::getLoan($id, $roomId);
    		$aLoan = $aRet['status'] ? $aRet['data'] : [];
    	}

    	$this->assign('aLoan', $aLoan);
    }

    /**
     * 分析师点评列表
     */
    public function analystListAction () {
    	$aAnalyst = [];
    	$id  = intval($this->getParam('id'));
    	if($id) {
    		$aRet = Sdk_Cms_Xf::getAnalyst($id);
    		$aAnalyst = $aRet['status'] ? $aRet['data'] : [];
    	}

    	$this->assign('aAnalyst', $aAnalyst);

        $this->aMeta['title'] .= '房价点评';
        $this->assign('aMeta', $this->aMeta);
    }

    /**
     * 网友点评列表
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
    	}

        $iUserID = 0;
        $bRet = $this->checkHasLogin();

        if($bRet){
            $aRet = json_decode($bRet, true);
            $iUserID = isset($aRet['data']['iUserID']) ? $aRet['data']['iUserID'] : 0;
        }

        $this->assign('userId', $iUserID);

    	$this->assign('aComments', $aComments);
        $this->assign('buildingID', $id);

        $this->aMeta['title'] .= '房价点评';
        $this->assign('aMeta', $this->aMeta);
    }

    /**
     * 评测列表
     */
    public function evaluationAction () {
        $aList = [];
        $aParam = [];
        $regionId = intval($this->getParam('regionId'));
        $blockId = intval($this->getParam('blockId'));
        $aParam['cityID'] = $cityId = $this->_cityId;
        $aParam['iPage'] = $this->getParam('page') ? intval($this->getParam('page')) : self::PAGE ;
        $aParam['iRows'] = $this->getParam('row') ? intval($this->getParam('row')) : self::ROW ;
        $aParam['priceID'] = intval($this->getParam('priceId'));
        $aParam['layoutID'] = intval($this->getParam('layoutId'));
        $aParam['regionID'] = $blockId ? $blockId : $regionId;

        if($cityId) {
            $aEvaluation = Sdk_Cms_Analyst::getEvaluationList($aParam);
            $aList['evaluation'] = $aEvaluation['status'] ? $aEvaluation['data'] : [];
            $aList['filter'] = $this->_filter($cityId);
        }

        $this->assign('aList', $aList);
        $this->assign('aEva', self::$aEva);

        $this->aMeta['title'] .= '评测列表';
        $this->assign('aMeta', $this->aMeta);
    }

    /**
     * 热销楼盘
     */
    public function hotAction () {
        $iCurrCityID = $this->_cityId;
        $aFilter = Sdk_Cms_Xfsearch::getFeatures(array('cityID' => $iCurrCityID));
        $aFilter = $aFilter['data'];

        if(empty($aFilter['features'])) {
            return false;
        }

        $key = $aFilter['features'][0]['sCode'];

        $aParam=$this->getParams ();
        $iPage=!isset($aParam["iPage"])?1:$aParam["iPage"];
        $iFeatureID = $this->getParam('iFeatureID') ? $this->getParam('iFeatureID') : $key;
        $aWhere = array(
            'cityID' => $iCurrCityID,
            'iPage' => $iPage,
            'iRows' => 5,
            'featureID' => $iFeatureID,
        );

        $aList = Sdk_Cms_Xfsearch::getList($aWhere);

        $this->assign('list', $aList);
        $this->assign('where', $aWhere);

        $this->assign('features', $aFilter["features"]);

        $this->assign('aEva', self::$aEva);

        $this->aMeta['title'] .= '热销楼盘';
        $this->assign('aMeta', $this->aMeta);
    }

    /**
     * 新房搜索结果页
     */
    public function xfSearchListAction()
    {
        #region 参数初始化
        $aParam = $this->getParams ();
        $iPage = !isset($aParam["iPage"]) ? 1 : $aParam["iPage"];
        $iFeatureID = $aParam["iFeatureID"];
        $iCurrCityID = $this->_cityId;
        #endregion

        #region 新房搜索数据处理
        $aWhere = array(
            'cityID' => $iCurrCityID,
            'iPage' => $iPage,
            'iRows' => 6,
            'featureID' => $iFeatureID
        );
        $aList = Sdk_Cms_Xfsearch::getList($aWhere);

        $this->showMsg($aList,1);
        #endregion
    }

    /**
     * 理想家
     */
    public function dreamerAction () {
        $aDreamer = [];
        $id = intval($this->getParam('id'));
        $houseName = $this->getParam('houseName');
        if($id) {
            $aDreamer['id'] = $id;
            $aDreamer['houseName'] = $houseName;
        }

        $this->assign('aDreamer', $aDreamer);

        $this->aMeta['title'] .= '房价点评';
        $this->assign('aMeta', $this->aMeta);
    }

    /**
     * 获取筛选列表
     */
    protected function _filter ($p_iCityId) {
    	$aFilter = Sdk_Cms_Xf::getFilter($p_iCityId);
    	return $aFilter['status'] ? $aFilter['data'] : [];
    }

    /**
     * 获取筛选列表
     */
    protected function _feature ($p_iCityId) {
    	$aFeature = Sdk_Cms_Xf::getFeature($p_iCityId);
    	return $aFeature['status'] ? $aFeature['data'] : [];
    }

    /**
     * 数组内查找*/
    protected function searchArray($array,$key,$value){
        foreach($array as $keyp=>$valuep){
            if($valuep[$key]==$value){
                return $valuep;
            }
        }
        return $array;
    }

}