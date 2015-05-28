<?php

/**
 * 新房
 * Date: 2015/2/2
 * author:cjj
 * Time: 13:27
 */
class Controller_Analysts_Index extends Controller_Base {

	/**
	 * 城市分析师首页
	 */
	public function IndexAction() {
		$aList = [];
		$aCity = [];
		$aTel  = '4008106999';

		$cityId = $this->iCurrCityID;
		if(!$cityId) return $this->_404();

		$aList = Model_Analysts::analystsList($cityId);	
		$this->assign('aList', $aList);

		$aCity['code'] = $this->sCurrCityCode;
		$aCity['name'] = $this->sCurrCityName;
		$this->assign('aCity', $aCity);

		//当前城市楼盘
		$aUnitIds = $this->_setUnitID($this->sCurrCityCode);

		//分析师IDs
		$aIds = [];
		if($aList) {			
			$PhoneRegion = isset($aList[0]['PhoneRegion']) ? '-'.trim($aList[0]['PhoneRegion']) : '';
			$aTel .= $PhoneRegion;
			foreach ($aList as $key => $value) {
				$aIds[] = $value['ID'];
			}
		}

		//城市电话
		$this->assign('aTel', $aTel);

		//分析师动态
		$this->_Dynamic($aIds, $aUnitIds);
    }

    /**
     * 当前城市楼盘IDs
     */
    protected function _setUnitID($sCityCode) {
    	$aUnitIds = [];
		$aUnit = [];
		$aUnit = Sdk_Cms_Unit::getCurrentCityLoupanList($sCityCode);
		if($aUnit['status'] && $aUnit['data']) {
			$aUnit = $aUnit['data'];
			if($aUnit) {
				foreach ($aUnit as $key => $value) {
					$aUnitIds[] = "'".$value['UnitID']."'";
				}
			}			
		}
		return $aUnitIds;
    }

    /**
     * 分析师动态
     */
    protected function _Dynamic($p_aIds, $p_aUnitIds) {  
       	$aDynamic = [];
    	
    	if($p_aIds && $p_aUnitIds){
    		$aOpinion = Sdk_Cms_Analyst::analystsOpinion($p_aIds, $p_aUnitIds);	
    		if($aOpinion['status'] && $aOpinion['data']) {
	    		$aOpinion = $aOpinion['data'];
	    		$rand = array_rand($aOpinion, 3);
	    		for ($i=0; $i < count($rand); $i++) { 
	    			$aDynamic[] = $aOpinion[ $rand[$i] ];
	    		}

	    		foreach ($aDynamic as $key => $value) {
		    		$aDynamicIds[] = "'".$value['UnitID']."'";
	    		}
	    		$aUnit = Sdk_Cms_Unit::getUnitByIDs($aDynamicIds);
	    		if($aUnit['status'] && $aUnit['data']) {
					$aUnit = $aUnit['data'];		
				}
				
				foreach ($aDynamic as $key => $value) {
					foreach ($aUnit as $k => $val) {
						if($value['UnitID'] == $val['UnitID']) {
							$aDynamic[$key]['UnitName'] = $val['UnitName'];
				    		$aDynamic[$key]['RegionName'] = $val['RegionName'];
				    		$aDynamic[$key]['DistrictName'] = $val['DistrictName'];
						}
						continue;
					}
	    		}
	    	}
    	}

    	$this->assign('aDynamic', $aDynamic);    	
	}
	
}