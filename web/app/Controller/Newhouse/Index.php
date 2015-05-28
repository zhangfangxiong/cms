<?php

/**
 * 新房
 * Date: 2015/2/2
 * author:cjj
 * Time: 13:27
 */
class Controller_Newhouse_Index extends Controller_Base
{

    const PAGE_SIZE = 15;

    public function IndexAction()
    {
        // 当前城市相关信息
        Model_Unit::setCityCode($this->sCurrCityCode);
        $cityInfo = Model_City::getCityInfo($this->sCurrCityCode);
        $this->assign('cityInfo', $cityInfo);
        // 搜索条件
        $result = Sdk_Cms_Newhouse::getSearchFilter($this->iCurrCityID);
        // 预算
        if (!empty($cityInfo)) {
            $result['data']['PriceSection'] = explode(',', $cityInfo['PriceSection']);
        }
        //环比上月
        $baifen = null;
        if (!empty($cityInfo['PriceList'])) {
            $baifen = Model_Pricecompare::CompareLastMonth($cityInfo['PriceList']);
        }
        $this->assign('baifen', $baifen);
        // 购房建议
        $result['data']['Suggest'] = Model_Unit::getSuggest();
        // 搜索参数
        $searchParam = Model_Util::getSearchParam();
        $this->assign('searchParam', $searchParam);
        // 楼盘列表
        $unitsResult = Model_Unit::getUnits($searchParam,self::PAGE_SIZE);
        $this->assign('units', $unitsResult['units']);
        // 分析师评楼
        $this->assign('unitRecommended',  Model_Unit::getUnitRecommended());
        // 排序方式
        $this->assign('orderType', Model_Unit::getOrderType());
        // 价格曲线图数据
        $this->assign('newPriceList', $this->getNewPriceList($cityInfo['PriceList']));
        $this->assign('searchfilter', $result['status'] == 1 ? $result['data'] : null);
        //近期开盘
        $newOpen = Model_Unit::getZuiXinKaiPan();
        $this->assign('newOpen', $newOpen);
        // 分页信息
        $this->assign('pageSize', self::PAGE_SIZE);
        $this->assign('unitsCount', $unitsResult['count']);
        $this->assign('resultCode', $unitsResult['resultCode']);
        $this->assign('totalPage', ceil($unitsResult['count'] / self::PAGE_SIZE));
        $this -> setShareUrl($cityInfo);
    }


    public function setShareUrl($cityInfo)
    {
        $arr = array(
            $cityInfo['CityName'] => Model_City::getCityUrl($cityInfo['CityCode']),
            '新房' => '',
        );
        $this->generateBreadcrumbs($arr);
    }


    // 用于页面曲线图使用
    public function getNewPriceList($priceList)
    {
        if (empty($priceList)) {
            return '';
        }
        $arrCityPrice = explode(',', $priceList);
        $arrCityPriceCount = sizeof($arrCityPrice);
        $newPriceList = '';
        if ($arrCityPriceCount  > 12) {
            if ($arrCityPriceCount <= 24) {
                $newPriceList = $priceList;
            } else {
                for ($i = ($arrCityPriceCount - 24); $i < $arrCityPriceCount; $i++) {
                    $newPriceList = $newPriceList . "," . $arrCityPrice[$i];
                }
                if (strlen($newPriceList) > 0) {
                    $newPriceList = substr($newPriceList, 1, strlen($newPriceList) - 1);
                }
            }
        }
        return $newPriceList;
    }


}