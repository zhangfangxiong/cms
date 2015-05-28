<?php

/**
 * Created by PhpStorm.
 * User: len
 * Date: 2015/1/27
 * Time: 11:17
 */
class Controller_API_Loupan extends Controller_Api_Base
{
    /**
     * 获取楼盘图表数据
     */
    public function getChartAction()
    {
        $buildingID = $this->getParam('buildingID');
        $unitID = Model_Loupan::BuildingIDConvert($buildingID);

        $priceTrendHouse = array();
        $priceTrendCottage = array();
        $priceTrendRegion = array();

        if(!empty($unitID)) {
            $priceTrendHouse = Model_CricBatchTypeFull::getPriceTrendData($unitID, '普通住宅');
            $priceTrendCottage = Model_CricBatchTypeFull::getPriceTrendData($unitID, '别墅');

            $unit = Model_CricUnit::getByUnitID($unitID);
            if(!empty($unit)) {
                $regionName = $unit['RegionName'];
                $region = Model_CricRegion::getRow(array('where'=>array('RegionName'=>$regionName, 'State'=>1)));

                if(!empty($region)) {
                    $priceList = $region['PriceList'];
                    $priceList = explode(',', $priceList);
                    if(!empty($priceList)) {
                        $len = sizeof($priceList);

                        for ($i = 0; $i < $len; $i++) {
                            if ($i % 4 == 0) {
                                $priceTrendRegion[] = $priceList[$i];
                            }
                        }
                    }
                }

            }
        }

        $showMonth = 7;

        $current = date('Y-n', time());
        $current = explode('-', $current);
        $year = $current[0];
        $month = $current[1];

        $trendHouse = array();
        $trendCottage = array();

        if(!empty($priceTrendHouse)) {
            $priceTrendHouse = array_slice($priceTrendHouse['price'], (-1)*$showMonth, $showMonth);
            foreach($priceTrendHouse as $key => $value){
                if($value) {
                    $trendHouse[] = $value;
                }else {
                    $trendHouse[] = 'null';
                }
            }
        }

        if(!empty($priceTrendCottage)) {
            $priceTrendCottage = array_slice($priceTrendCottage['price'], (-1)*$showMonth, $showMonth);
            foreach($priceTrendCottage as $key => $value){
                if($value) {
                    $trendCottage[] = $value;
                }else {
                    $trendCottage[] = 'null';
                }
            }
        }

        if(empty($trendHouse)) {
            $trendHouse = array_pad($trendHouse, $showMonth, 'null');
        }

        if(empty($trendCottage)) {
            $trendCottage = array_pad($trendCottage, $showMonth, 'null');
        }


        if(!empty($priceTrendRegion) && sizeof($priceTrendRegion) >= $showMonth) {
            $priceTrendRegion = array_slice($priceTrendRegion, (-1)*$showMonth, $showMonth);
        }else {
            $regionLen = sizeof($priceTrendRegion);
            //$left = $showMonth - $regionLen;
            $priceTrendRegion = array_pad($priceTrendRegion, (-1)*$showMonth, 'null');
        }

        $result = array(
            'priceTrendHouse' => $trendHouse,
            'priceTrendCottage' => $trendCottage,
            'priceTrendRegion' => $priceTrendRegion,
            'year' => $year,
            'month' => $month
        );
        return $this->showMsg($result, true);
    }

}