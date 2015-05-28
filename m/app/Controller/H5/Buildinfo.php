<?php
/**
 * Created by PhpStorm.
 * User: len
 * Date: 2015/4/10
 * Time: 11:33
 */

class Controller_H5_Buildinfo extends Controller_Base
{

	private $_typeCode;
	private $_buildingID;

	const priceunit = 10000;

	/**
     * 执行动作之前
     */
    public function actionBefore () {
    	parent::actionBefore();
    }

    public function hxinfoaction() {
    	$this->_typeCode = $this->getParam('typeCode');
    	$this->_buildingID = intval($this->getParam('buildingID'));
    	
        $aHxInfo = Sdk_Cms_Unit::roomTypeDetail($this->_buildingID, $this->_typeCode);
        if (empty($aHxInfo['data'])) {
            return false;
        }

        $aHxInfo['data']['MinTotalPrice'] = ($aHxInfo['data']['MinTotalPrice'] / self::priceunit);
        $aHxInfo['data']['MaxTotalPrice'] = ($aHxInfo['data']['MaxTotalPrice'] / self::priceunit);
        $this->assign('aHxInfo', $aHxInfo['data']);
    }

}