<?php
/**
 * Created by PhpStorm.
 * User: xcy
 * Date: 2015/5/20
 * Time: 10:18
 * 楼盘接口
 */


class Controller_Api_Analysts extends Controller_Api_Base {

	/**
     * 城市分析师列表
     */
    public function getAnalystsAction(){
        $cityId = $this->getParam('cityId');
        if(empty($cityId)){
            return null;
        }

        $aWhere = [
        	'iStatus' => 1,
        	'isDown'  => 1,
        ];

        $result = Model_Analysts::getAnalystsList($cityId, $aWhere);
        $this->showMsg($result, true);
    }

    public function getOpinionAction() {
    	$aIds = $this->getParam('aIds');
        $aUnitIds = $this->getParam('aUnitIds');
    	if(empty($aIds) || empty($aUnitIds)){
            return null;
        }

        $result = Model_AnalystsOpinion::getAnalystsOpinion($aIds, $aUnitIds);
        $this->showMsg($result, true);
    }

}