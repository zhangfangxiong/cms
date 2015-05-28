<?php

/**
 * Created by PhpStorm.
 * User: cjj
 * Date: 15/4/7
 * Time: 下午1:44
 */
class Model_Unit
{

    public static $cityCode ;

    public static function  setCityCode($sCurrCityCode)
    {
        self::$cityCode = $sCurrCityCode;
    }
   // 楼盘推荐
    public static  function getUnitRecommended()
    {
        $unitRecommendedResult = Sdk_Cms_Newhouse::unitRecommended(self::$cityCode);
        if (!empty($unitRecommendedResult['data']) && $unitRecommendedResult['status'] == 1) {
            return self::formatUnitRecommended($unitRecommendedResult['data']);
        } else {
            return array();
        }
    }

    // 楼盘列表
    public static function getUnits($searchParam,$pageSize)
    {
        $unitsResult = Sdk_Cms_Newhouse::Search($searchParam, self::$cityCode, $pageSize);
        if (!empty($unitsResult['data']['units']) && $unitsResult['status'] == 1) {
            if (!empty($unitsResult['data']['units'])) {
                Model_Util::setCityCode(self::$cityCode);
                foreach ($unitsResult['data']['units'] as &$item) {
                    $item['Url'] = Model_Util::getDetailUrl($item['ID']);
                }
            }
            return array(
                'units' => json_encode($unitsResult['data']['units']),
                'count' => $unitsResult['data']['count'],
                'resultCode' => $unitsResult['data']['resultCode']
            );
        } else {
            return array('units' => "''", 'count' => 0,'resultCode' => 0);
        }
    }

    public static function formatUnitRecommended($data)
    {
        $pStr = '待定';
        if (!empty($data)) {
            foreach ($data as $k => &$item) {
                if (empty($item)) {
                    continue;
                }
                if ($item['Price'] > 0) {
                    $item['newPrice'] = number_format($item['Price']);
                } else {
                    $item['newPrice'] = $pStr;
                }
                if ($item['CricPrice'] > 0) {
                    $item['newCricPrice'] = number_format($item['CricPrice']);
                } else {
                    $item['newCricPrice'] = $pStr;
                }
                $item['image'] = Model_Util::getFormatImag($item['ZongPingXQ'], 208, 151);
            }
        }
        return $data;
    }

    //处理楼盘资讯->最新开盘
    public static  function getZuiXinKaiPan(){
        $arr = array();
        $newOpen = Sdk_Cms_Unit::getZuiXinKaiPan(self::$cityCode);
        if(!empty($newOpen['data'])){
            foreach($newOpen['data'] as $key=>$value){
                if($value['sOpenTime'] == $value['sOpenTime']){
                    $arr[$value['sOpenTime']][] = $value;
                }
            }
            return $arr;
        }
    }

    //楼盘相关信息
    public static  function getUnitInfo($unitID)
    {
        $result = Sdk_Cms_Newhouse::unitDetail($unitID);
        if ($result['status'] == 1) {
            return $result['data'];
        }
        return null;

    }

    // 楼盘优惠信息
    public static function unitSales($unitID)
    {
        $result = Sdk_Cms_Newhouse::unitSales($unitID);
        if ($result['status'] == 1) {
            return $result['data'];
        }
        return null;
    }

    // 楼盘优惠信息
    public static function getFilterUnitSales($unitID)
    {
        $result = self::unitSales($unitID);
        if (empty($result)) {
            return null;
        }
        $newArr = array();
        foreach($result as $key => $item) {
            if (strtotime($item['SaleEndTime']) > time() || date('Y-m-d',strtotime($item['SaleEndTime'])) == '1970-01-01') {
                $newArr[] = $item;
            }
        }
        return $newArr;
    }


    // cric 楼盘图片
    public static function getUnitPictures($unitID)
    {
        $data = array();
        $result = Sdk_Cms_Newhouse::unitPictures($unitID);
        if ($result['status'] == 1) {
            foreach ($result['data'] as $key => $item) {
                // 社区实景
                if ($item['PictureType'] == 'sjt') {
                    $data['sjt'][] = $item;
                }
                // 效果图
                if ($item['PictureType'] == 'xgt') {
                    $data['xgt'][] = $item;
                }
                // 总平图
                if ($item['PictureType'] == 'zpt') {
                    $data['zpt'][] = $item;
                }
                // 楼盘周边
                if ($item['PictureType'] == 'ybft') {
                    $data['ybft'][] = $item;
                }
            }
        }
        return $data;
    }
    // 购房建议
    public static function getSuggest()
    {
        $data = array(
            0 => '尽快入手',
            1 => '推荐购买',
            2 => '谨慎购买',
            3 => '持币观望'
        );
        return $data;
    }

    // 排序方式
    public static function getOrderType()
    {
        $data = array(
            3 => '最优户型',
            4 => '装修适宜',
            5 => '物业无忧',
            6 => '车位充足',
            7 => '人气区位',
        );
        return $data;
    }

    // 按户型1
    public static function getRoomOne()
    {
        $data = array(
            0 => '一房',
            1 => '二房',
            2 => '三房',
            3 => '独栋',
            4 => '联排'
        );
        return $data;
    }

    //按户型2
    public static function getRoomTwo()
    {
        $data = array(
            0 => '一房',
            1 => '二房',
            2 => '三房',
            3 => '四房',
            4 => '五房',
            5 => '复式',
            6 => '独栋',
            7 => '叠加',
            8 => '双拼',
            9 => '联排',
            10 => '花园洋房'
        );
        return $data;
    }

    // 附近新房信息
    public static function aroundXf($where)
    {
        $result = Sdk_Cms_Newhouse::aroundXf($where);
        if ($result['status'] == 1) {
            return $result['data'];
        }
        return null;
    }

    // 推荐楼盘
    public static function getRecommendedUnit($cityCode,$homeId)
    {
        $result = Sdk_Cms_Newhouse::getRecommendedUnit($cityCode,$homeId);
        if ($result['status'] && !empty($result['data'])) {
            return $result['data'];
        }
        return null;
    }
}