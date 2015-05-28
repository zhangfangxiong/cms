<?php
/**
 * Created by PhpStorm.
 * User: xiaoyao
 * Date: 2015/2/3
 * Time: 10:18
 * 楼盘接口
 */


class Controller_Api_Unit extends Controller_Api_Base
{


    /**
     * 资讯首页和列表页分析师评楼
     */
    public function analystrecommendunitAction()
    {

        $aType = array_unique(array_values(Model_CricUnit::getRecommendedLevelList()));
        //$aType = ['尽快入手', '推荐购买', '谨慎购买', '持币观望'];
        $sCityCode = $this->getParam('sCityCode');
        $aList = Model_CricUnitRecommended::getAnalystRecommendedUnit($aType, $sCityCode);
        $aList = $this->_formatAnalystreRecommendUnit($aList);
        return $this->showMsg($aList, true);
    }

    protected function _formatAnalystreRecommendUnit($aUnit)
    {
        if ($aUnit) {
            foreach ($aUnit as $key => $value) {
                unset($aUnit[$key]['ID']);
                unset($aUnit[$key]['City']);
                unset($aUnit[$key]['ListDate']);
                unset($aUnit[$key]['Rank']);
                //unset($aUnit[$key]['sHashCode']);
            }
        }
        return $aUnit;
    }


    /**
     * 资讯详情页
     * 推荐楼盘
     */
    public function newsrecommendunitAction()
    {
        $iNewsID = $this->getParam('iNewsID');
        $aNews = Model_News::getDetail($iNewsID);
        $aUnitList = [];
        if (!empty($aNews)) {
            if (!empty($aNews['sLoupanID'])) {
                $aLoupanID = explode(',', $aNews['sLoupanID']);
                $aUnit = Model_CricUnit::getAll(
                    [
                        'where' => ['ID IN' => $aLoupanID]
                    ]
                );
                $aUnitList = $this->_formatNewsRecommendUnit($aUnit);
            } else {
                $aCity = Model_City::getDetail($aNews['iCityID']);
                $sCityCode = $aCity['sFullPinyin'];
                $aType = array_unique(array_values(Model_CricUnit::getRecommendedLevelList()));
                $aUnitList = Model_CricUnitRecommended::getAnalystRecommendedUnit($aType, $sCityCode);
                $aUnitList = $this->_formatAnalystreRecommendUnit($aUnitList);
            }

        }
        return $this->showMsg($aUnitList, true);
    }

    protected function _formatNewsRecommendUnit($aUnit)
    {
        if ($aUnit) {
            foreach ($aUnit as $key => $value) {

                $aFormatList['UnitIndexID'] = $value['ID'];
                $aFormatList['UnitID'] = $value['UnitID'];
                $aFormatList['Region'] = $value['RegionName'];
                $aFormatList['District'] = $value['DistrictName'];
                $aFormatList['UnitName'] = $value['UnitName'];
                $aFormatList['CityCode'] = $value['CityCode'];

                $aFormatList['ZongPingXQ'] = $value['ZongPingXQ'];
                $aFormatList['OpinionSummary'] = $value['OpinionSummary'];
                $aFormatList['Price'] = intval($value['BidingPrice']);
                $aFormatList['CricPrice'] = intval($value['Price']);
                $aLevelList = Model_CricUnit::getRecommendedLevelList();
                $aFormatList['ListType'] = isset($aLevelList[$value['EJScore']]) ? $aLevelList[$value['EJScore']] : '';

                $aNewsBatchType = Model_CricUnit::getUnitNewestBatchType($value['UnitID']);
                if ($aNewsBatchType) {
                    $aFormatList['Price'] = $aNewsBatchType['SalePrice'];
                    $aFormatList['CricPrice'] = $aNewsBatchType['CricPrice'];
                    $aFormatList['ListType'] = isset($aLevelList[$aNewsBatchType['ManualLevelCode']]) ?
                        $aLevelList[$aNewsBatchType['ManualLevelCode']] : '';
                }
                //print_r($aFormatList);
                $aFormatList['sUrl'] = Model_CricUnit::getUnitUrl($value['CityCode'], $value['RegionName'], $value['DistrictName'], $value['ID']);
                $aUnit[$key] = $aFormatList;
            }
        }
        return $aUnit;
    }

    public function getCityCodeAction()
    {
        $unitID = $this->getParam('unitID');
        if (empty($unitID)) {
            $result = array();
        } else {
            $result = Model_CricUnit::getLoupanByID($unitID);
        }
        return $this->showMsg($result, true);
    }

    public function roomTypeDetailAction()
    {
        $buildingID = $this->getParam('buildingID');
        $sTypeCode = $this->getParam('typeCode');
        $unitID = Model_Loupan::BuildingIDConvert($buildingID);
        $roomType = Model_CricRoomType::getRoomTypeDetail($unitID, $sTypeCode);
        $result = [];
        if (!empty($roomType)) {
            $result['TypeName'] = $roomType['TypeName'];
            $result['TypeCode'] = $roomType['TypeCode'];
            $result['Image'] = !empty($roomType['ImageCode']) ? Util_Uri::getCricViewURL($roomType['ImageCode'], 400, 300) : Util_Uri::getDefaultImg(3);
            $result['MinArea'] = $roomType['MinArea'];
            $result['MaxArea'] = $roomType['MaxArea'];
            $result['MinTotalPrice'] = $roomType['MinTotalPrice'];
            $result['MaxTotalPrice'] = $roomType['MaxTotalPrice'];
            $result['Desc'] = $roomType['RecommendedRoomTypeRemark'];

        }
        $this->showMsg($result, true);
    }

    /**
     * 首页预开盘推荐
     */
    public function getAdvanceOpenAction(){
        $cityName = $this->getParam('cityName');
        $ListType = $this->getParam('ListType');
        $CustomType = $this->getParam('CustomType');
        if(empty($cityName)){
            return null;
        }
        //数组条件组装
        $arr = array('cityName'=>$cityName,'ListType'=>$ListType,'CustomType'=>$CustomType);

        $result = Model_CricUnitZT0114::getYuKaiPan($arr);
        $this->showMsg($result, true);
    }

    /**
     * 首页新开盘推荐
     */
    public function getNewTrayAction(){
        $cityName = $this->getParam('cityName');
        $ListType = $this->getParam('ListType');
        $CustomType = $this->getParam('CustomType');
        if(empty($cityName)){
            return null;
        }
        //数组条件组装
        $arr = array('cityName'=>$cityName,'ListType'=>$ListType,'CustomType'=>$CustomType);

        $result = Model_CricUnitZT0114::getXinKaiPan($arr);
        $this->showMsg($result, true);
    }

//    public function actionBefore()
//    {
//
//    }
    public function homeBangdanAction()
    {
        $cityName = $this->getParam('cityName');
        if(empty($cityName)){
            return null;
        }
        $typeList = Model_CricUnitZT0114::getCurrYearListType($cityName);
        foreach($typeList as $key => $value) {
            $typeUnitList = Model_CricUnitZT0114Detail::getAll(
                [
                    'where' => [
                        'ListID' => $value['sCode']
                    ],
                    'order' => 'rank asc'
                ]
            );
            foreach($typeUnitList as $uKey => $unit) {
                $unitInfo = Model_Loupan::getRow(
                    [
                        'where' => [
                            'sHomeID' => $unit['HomeID']
                        ]
                    ]
                );
                if(!empty($unitInfo)) {
                    $typeUnitList[$key] = array_merge($unit, $unitInfo);
                }
            }
            $typeList[$key]['unitList'] = $typeUnitList;
        }
        return $this->showMsg($typeList, true);
    }
    /**
     * 首页评级
     */
    public function pingJiUnitsAction(){
        $cityCode = $this->getParam('cityCode');
        $type = $this->getParam('type');
        if(empty($cityCode) || empty($type)){
            return null;
        }
        $result = Model_CricUnit::getPingJiList($cityCode,$type);
        $this->showMsg($result, true);
    }

    /**
     * 热销滞销榜
     */
    public function reXiaoZhiXiaoAction(){
        $cityName = $this->getParam('cityName');
        if(empty($cityName)){
            return null;
        }
        $result = Model_CricUnit::getZhiXiaoOrReXiao($cityName);
        $this->showMsg($result, true);
    }

    /**
     * 楼盘资讯->最新开盘
     */
    public function getZuiXinAction(){
        $cityCode = $this->getParam('cityCode');
        if(empty($cityCode)){
            return null;
        }
        $result = Model_Loupan::getNewOpen($cityCode);
        $this->showMsg($result, true);
    }

    /**
     * 首页分析师
     */
    public function getAnalystAction(){
        $cityCode = $this->getParam('cityCode');
        if(empty($cityCode)){
            return null;
        }
        $result = Model_Analysts::getAnalystByCityCode($cityCode);
        $this->showMsg($result, true);
    }

    /**
     * 获取首页分析师两条最新点评
     */
    public function getAnalystDPAction(){
        $cityName = $this->getParam('cityName');
        if(empty($cityName)){
            return null;
        }
        $result = Model_Analysts::getAnalysDianPin($cityName);
        $this->showMsg($result, true);
    }

    /**
     * 获取首页评测报告
     */
    public function getEvaluationListAction(){
        $cityCode = $this->getParam('cityCode');
        if(empty($cityCode)){
            return null;
        }
        $result = Model_CricUnit::getEvaluation($cityCode);
        $this->showMsg($result, true);
    }

    /**
     * 获取首页评级推荐
     */
    public function getPinJiListAction(){
        $cityCode = $this->getParam('cityCode');
        $type = $this->getParam('type');
        if(empty($cityCode) || empty($type)){
            return null;
        }
        $result = Model_CricUnit::getPingJi($cityCode,$type);
        $this->showMsg($result, true);
    }

    /**
     * 保存理想家数据
     */
    public function saveLiXiangJiaAction(){
        $array = array(
            'accountName' => $this->getParam('accountName'),
            'userName' => $this->getParam('UserName'),
            'unitName' => $this->getParam('UnitName'),
            'phone' => $this->getParam('Phone'),
            'miniPrice' => $this->getParam('MiniPrice'),
            'cityName' => $this->getParam('cityName'),
            'rowDate' => date('Y-m-d H:i:s',time())
        );
        $result = Model_CricLiXiangJia::saveLiXiangJia($array);
        $this->showMsg($result, true);
    }

    /**
     * 根据loupan表的ID获取unit表的unitid
     */
    public function getIdByUnitidAction(){
        $id = $this->getParam('id');
        if(empty($id)){
            return null;
        }
        $result = Model_CricUnit::getIDByUnitId($id);
        $this->showMsg($result, true);
    }

    /**
     * 根据unit表的unitid获取loupan表的ID
     */
    public function getUnitidByIdAction(){
        $unitId = $this->getParam('unitId');
        $type = $this->getParam('type');
        if(empty($unitId)){
            return null;
        }
        $result = Model_CricUnit::getUnitIdById($unitId,$type);
        $this->showMsg($result, true);
    }

    /**
     * 分析师点赞
     */
    public function analystsUpAction(){
        $AnalystsID = $this->getParam('AnalystsID');
        if(empty($AnalystsID)){
            return null;
        }
        $result = Model_Analysts::analysUp($AnalystsID);
        $this->showMsg($result, true);
    }

    /**
     * 当前城市所有楼盘
     */
    public function getLoupanListAction() {
        $citycode = $this->getParam('citycode');
        if(empty($citycode)){
            return null;
        }
        $result = Model_CricUnit::getCurrentCityLoupanList($citycode);
        $this->showMsg($result, true);
    }

    /**
     * 由ids获取楼盘信息
     */
    public function getUnitByIDsAction() {
        $aUnitIds = $this->getParam('aUnitIds');
        if(empty($aUnitIds)){
            return null;
        }
        $result = Model_CricUnit::getUnitByIDs($aUnitIds);
        $this->showMsg($result, true);
    }

    public function getListByLetterAction () {
        $letter = trim($this->getParam('iLetter'));
        if(empty($letter)){
            return null;
        }
        $result = Model_CricUnit::getListByLetter($letter);
        $this->showMsg($result, true);
    }
}