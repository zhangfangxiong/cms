<?php

/**
 * Created by PhpStorm.
 * User: cjj
 * Date: 15/4/7
 * Time: 下午1:44
 */
class Model_City
{
    // 城市相关信息
    public static function getCityInfo($sCurrCityCode)
    {
        // 当前城市相关信息
        $cityInfo = Sdk_Cms_XfCity::getCityPriceSection($sCurrCityCode);
        if ($cityInfo['status'] == 1) {
            return $cityInfo['data'];
        } else {
            return null;
        }
    }

    // 城市新房URL
    public static function getCityUrl($cityCode)
    {
        return '/' . $cityCode . '/NewHouse/index';

    }

    // 城市400
    public static function getCity400($cityName, $regionName)
    {
        $extNumber = '';
        $result = Sdk_Cms_XfCity::getCity400($cityName, $regionName);
        if ($result['status'] == 1) {
            $extNumber = $result['data'];
        }
        return array(
            '400' => Yaf_G::getConf('400Tel'),
            'ext' => $extNumber,
        );
    }

    // 区域价格
    public static function getRegionPrice($priceList)
    {
        $regionPrice = array();
        $arrTemRegionPrice = array();
        if (!empty($priceList)) {
            $arrTemRegionPrice = explode(',', $priceList);
        }
        for ($i = 0; $i < sizeof($arrTemRegionPrice); $i++) {
            if ($i % 4 == 0) {
                $regionPrice[] = $arrTemRegionPrice[$i];
            }
        }
        $temRegionPriceLen = sizeof($regionPrice);
        if ($temRegionPriceLen < 12) {
            for ($i = 0; $i < 12 - $temRegionPriceLen; $i++) {
                $regionPrice[] = 'null';
            }
        }
        return $regionPrice;
    }

    //区域相关信息
    public static  function getRegionInfo($cityCode,$regionName)
    {
       $regionInfo = array();
        $result = Sdk_Cms_XfCity::getCityRegion($cityCode);
        if ($result['status'] && !empty($result['data'])) {
            foreach( $result['data'] as $item) {
                if ($item['RegionName'] == $regionName) {
                    $regionInfo = $item;
                    break;
                }
            }
        }
        return $regionInfo;
    }
}