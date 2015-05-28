<?php

/**
 * Created by PhpStorm.
 * User: xcy
 * Date: 15/5/19
 * Time: 下午5:44
 */
class Model_Analysts {

	/**
	 * 分析师列表[按级别排序]
	 */
	public static function analystsList($p_iCityId) {
		$aList = Sdk_Cms_Analyst::analystList($p_iCityId);
		if(1 == $aList['status'] && $aList['data']) {
			$aList = $aList['data'];
			foreach ($aList as $key => $value) {
				switch ($value['AnalystLevel']) {
					case '首席分析师':
						$aList[$key]['iLevel'] = 3;
						break;
					case '高级分析师':
						$aList[$key]['iLevel'] = 2;
						break;
					case '分析师': 
						$aList[$key]['iLevel'] = 1;
						break;
					default:
						$aList[$key]['iLevel'] = 1;
						break;
				}
			}

			foreach ($aList as $k => $v) {
				$level[] = $v['iLevel'];
			}
			array_multisort($level, SORT_DESC, $aList);
		}else {
			$aList = [];
		}
		return $aList;		
	}

}