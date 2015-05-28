<?php

/**
 * 新房优惠价格
 * Date: 2015/5/19
 * author:wsf
 */
class Controller_Newhouse_Discount extends Controller_NewHouseBase
{
    /**
     * 新房优惠价格*/
    public function IndexAction()
    {
        $aSales= Model_Unit::unitSales($this->unitInfo["unit"]["UnitID"]);

        if (empty($aSales)) {
            return $this->_404();
        }

        #region 周边楼盘数据

        $aWhere = array(
            'buildingID' => $this->unitInfo["unit"]["ID"],
            'cityCode' => $this->unitInfo["unit"]["CityCode"],
            'regionName' => $this->unitInfo["unit"]["RegionName"],
            'num' => 8
        );
        $aXf= Model_Unit::aroundXf($aWhere);

        $this->assign('aAroundXf', $aXf);
        #endregion
        $this->assign("aUnit",$this->unitInfo["unit"]);
        $this->assign('iMenuIndex',5);
        $this->assign('aSales', $aSales);
    }
}