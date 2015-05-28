<?php
/**
 * Created by PhpStorm
 * Date: 2015/2/2
 * Time: 13:27
 */

class Controller_Index_Index extends Controller_Base
{
    public function actionBefore ()
    {
        $aCity = $this->getCurrCityInfo();
        if (empty($aCity)) {
            return $this->getResponse()->setRedirect('/welcome');
        }
        $this->iCurrCityID = $aCity['iCityID'];
        $this->sCurrCityCode = $aCity['sFullPinyin'];
        $this->sCurrCityName = $aCity['sCityName'];

        $this->assign('iCurrentCityID', $this->iCurrCityID);
        $this->assign('sCurrCityCode', $this->sCurrCityCode);
        $this->assign('sCurrCityName', $this->sCurrCityName);
        $this->assign('sCurrController',$this->getRequest()->getControllerName());
        $this->assign('sStaticRoot', 'http://' . Yaf_G::getConf('static', 'domain'));

        $this->assign('sCurUrl',$this->getCurUrl());
        $this->assign('newsUrl', $this -> getNewsUrl());
    }

    public function IndexAction()
    {
        //获取城市列表
        $citylist = Sdk_Cms_XfCity::getCity();
        //获取城市区域
        $reigonlist = Sdk_Cms_XfCity::getCityRegion($this->sCurrCityCode);
        //获取城市价格搜索区间
        $cityInfo = Sdk_Cms_XfCity::getCityPriceSection($this->sCurrCityCode);
        //处理价格区间数据
        $priceList = $this->formatPricData($cityInfo);
        //获取首页轮播图片
        $img = Model_Util::getNewsHtml("api/banner/getHomeCarousel?sCity=".$this->sCurrCityCode);
        //环比上月
        $baifen = Model_Pricecompare::CompareLastMonth($cityInfo['data']['PriceList']);
        //预开盘推荐
        $yuKai = Sdk_Cms_Unit::getAdvanceOpen($this->sCurrCityName);
        $yukaipan = $this->formatUnitInfo($yuKai['data']);
        //新开盘推荐
        $xinkai = Sdk_Cms_Unit::getNewPan($this->sCurrCityName);
        $xinkaipan = $this->formatUnitInfo($xinkai['data']);
        //评级上调
        $shangtiao = Sdk_Cms_Unit::getPingJi($this->sCurrCityCode,1);
        $shangtiao = $this->formatPingJiData($shangtiao['data']);
        //评级下调
        $xiatiao = Sdk_Cms_Unit::getPingJi($this->sCurrCityCode,2);
        $xiatiao = $this->formatPingJiData($xiatiao['data']);
        //滞销
        $zhixiao = Sdk_Cms_Unit::reXiaoOrZhiXiao($this->sCurrCityName);
        $zhixiao = $this->formatReXiaoZhiXiao($zhixiao['data'],5);
        //热销
        $rexiao = Sdk_Cms_Unit::reXiaoOrZhiXiao($this->sCurrCityName);
        $rexiao = $this->formatReXiaoZhiXiao($rexiao['data'],2);
        //每月新开盘
        $xinpan = Sdk_Cms_Unit::getNewPan($this->iCurrCityID);
        //性价比测评榜
        $bangdan = Sdk_Cms_Unit::getXingJiaBi($this->sCurrCityName);
        $bangdan = $this->formatBangDan($bangdan['data']);
        //获取首页楼盘资讯
        $news = Model_Util::getNewsHtml("api/banner/getlounews?sCity=$this->sCurrCityCode");
        //获取首页楼盘资讯最新开盘
        Model_Unit::setCityCode($this->sCurrCityCode);
        $newOpen = Model_Unit::getZuiXinKaiPan();
        //首页分析师
        $analyst = Sdk_Cms_Analyst::getAnalyst($this->sCurrCityCode);
        $analyst = $this->formatAnalysts($analyst['data']);
        //首页底部广告图片
        $bottom = Model_Util::getNewsHtml("api/banner/gethomeindextwo?sCity=$this->sCurrCityCode");
        //获取首页中部广告图片
        $middle = Model_Util::getNewsHtml("api/banner/gethomeindexone?sCity=$this->sCurrCityCode");
       //获取所属城市小区首字母
        $shouzimu = Sdk_Cms_XfCity::getShouzIMu($this->sCurrCityCode);
        //获取首页友情链接
        $link = Sdk_Cms_XfCity::getLinks($this->sCurrCityCode);

        $this->assign('citylist', $citylist);
        $this->assign('reigonlist', $reigonlist);
        $this->assign('priceList', $priceList);
        $this->assign('lunbo', $img);
        $this->assign('cityInfo', $cityInfo);
        $this->assign('baifen', $baifen);
        $this->assign('yukaipan', $yukaipan);
        $this->assign('xinkaipan', $xinkaipan);
        $this->assign('shangtiao', $shangtiao);
        $this->assign('xiatiao', $xiatiao);
        $this->assign('zhixiao', $zhixiao);
        $this->assign('rexiao', $rexiao);
        $this->assign('bangdan', $bangdan);
        $this->assign('news', $news);
        $this->assign('newOpen', $newOpen);
        $this->assign('analyst', $analyst);
        $this->assign('bottom', $bottom);
        $this->assign('middle', $middle);
        $this->assign('shouzimu', $shouzimu);
        $this->assign('link', $link);
        //按户型
        $this->assign('room', Model_Unit::getRoomOne());
        $this->assign('roomtwo', Model_Unit::getRoomTwo());
        //房价建议
        $this->assign('suggest', Model_Unit::getSuggest());
        // 排序方式
        $this->assign('orderType', Model_Unit::getOrderType());
        //从cookie中提取的用户信息
        $this->assign('userInfo', Util_Cookie::get('userInfo'));
        $this->assign('sToken', Util_Cookie::get('sToken'));
    }

    /**
     * 欢迎页*/
    public function WelcomeAction(){
        $aCity=Sdk_Cms_XfCity::getCity();
        $aCityMap=Sdk_Cms_XfCity::getCityMap();

        $aNewCity=array();
        if($aCity["status"]&&$aCityMap["status"]){
            foreach($aCity["data"]["list"] as $item)
            {
                $cityMap=self::getCityMap($item["sPinyin"],$aCityMap);
                if(!empty($item)&&!empty($cityMap)){

                    $newItem["cityDomain"]=$item["sPinyin"];
                    $newItem["cityName"]=$item["sName"];
                    $newItem["esfEnabled"]=$item["iEsfEnabled"];

                    $newItem["cityId"]=$cityMap["cityId"];
                    $newItem["picUrl"]=$cityMap["picUrl"];

                    $aNewCity[]=$newItem;
                }
            }
        }
        $this->assign('aCity', $aNewCity);
    }

    /**
     * 地图上的城市*/
    public function MapCityApiAction(){
        $sCityId=$this->getParam('cityId');

        $aCity=Sdk_Cms_XfCity::getCity();
        $aCityMap=Sdk_Cms_XfCity::getCityMap();

        $aData = array(
            'errno' => 1,
            'data' => ""
        );

        foreach($aCityMap["data"]["list"] as $v){
            if($v&&$v["cityId"]==$sCityId){
                $v["cityName"]=self::getCityMap($v["cityCode"],$aCity,"sPinyin")["sName"];
                $aData = array(
                    'errno' => 0,
                    'data' => $v
                );
            }
        }
        return $this->showMsg($aData, true);
    }

    //获取首页分析师最新点评
    public function getAnalystDianPinAction()
    {
        $result = Sdk_Cms_Analyst::getAnalystDP($this->sCurrCityName);
        Model_Util::setCityCode($this->sCurrCityCode);
        if(!empty($result['data'])){
            foreach($result['data'] as $key=>$value){
                $result[$key]['AnalystsName'] = $value['AnalystsName'];
                $result[$key]['UnitName'] = $value['UnitName'];
                $result[$key]['Opinion'] = $value['Opinion'];
                $result[$key]['Url'] = Model_Util::getDetailUrl(isset($value['ID'])?$value['ID']:0);
            }
        }
        $this->showMsg($result, true);
    }

    //分析师点赞
    public function AnalystsListUpAction()
    {
        $AnalystsID = $this->getParam('AnalystsID');
        if(!empty($AnalystsID)){
            $result = Sdk_Cms_Analyst::analystGood($AnalystsID);
            $this->showMsg($result, true);
        }
    }

    //获取首页评测报告
    public function getEvaluationAction()
    {
        //左侧报告
        $evaluation = Sdk_Cms_Evaluation::getEvaluation($this->sCurrCityCode);
        $evaluation = $this->formatEvaluation($evaluation['data']);  //格式化数据
        //右侧评级推荐
        $zjhxList = Sdk_Cms_Evaluation::getPingJi($this->sCurrCityCode,1);  //最佳户型
        $newzjhxList = $this->formatPinjiTuiJian($zjhxList['data']);

        $cwczList = Sdk_Cms_Evaluation::getPingJi($this->sCurrCityCode,2);  //车位充足
        $newcwczList = $this->formatPinjiTuiJian($cwczList['data']);

        $sqwyList = Sdk_Cms_Evaluation::getPingJi($this->sCurrCityCode,3);  //社区无忧
        $newsqwyList = $this->formatPinjiTuiJian($sqwyList['data']);
        $newArr = array('EvaluationUnitList'=>$evaluation,'zjhx'=>$newzjhxList,'cwcz'=>$newcwczList,'sqwy'=>$newsqwyList);
        $this->showMsg($newArr, true);
    }

    /**
     * 理想家数据保存
     */
    public function savelixiangjiaAction()
    {
        $UserName = $this->getParam('UserName');
        $UnitName = $this->getParam('UnitName');
        $Phone = $this->getParam('Phone');
        $MiniPrice = $this->getParam('MiniPrice');
        $accountName = Util_Cookie::get('userInfo');
        $accountName = isset($accountName['UserName']) ? $accountName['UserName'] : '';
        $cityName = $this->sCurrCityName;
        $result = Sdk_Cms_Unit::saveLiXiangJia($UserName,$UnitName,$Phone,$MiniPrice,$accountName,$cityName);
        $this->showMsg($result, true);
    }

    private function getCityMap($sCityCode,$aCityMap,$sKey="cityCode"){
        if($aCityMap["status"]){
            foreach($aCityMap["data"]["list"] as $item){
                if(!empty($item)&&$item[$sKey]==$sCityCode){
                    return $item;
                }
            }
        }
    }

    /**
     * 处理城市价格区间数据
     */
    private function formatPricData($price)
    {
        $priceList = array();
        if(!empty($price['data'])) {
            foreach ($price as $k=>$v) {
                if(!empty($v['PriceSection'])) {
                    $priceList['PriceSection'] = explode(',', $v['PriceSection']);
                    $priceList['slicePriceSection'] = array_slice($priceList['PriceSection'],0,4);
                }
                if(!empty($v['P_Section'])) {
                    $priceList['section'] = explode(',', $v['P_Section']);
                }
            }
        }
        return $priceList;
    }

    /**
     * 处理首页预开盘推荐
     */
    private function  formatUnitInfo($mOpenList)
    {
        if(isset($mOpenList) && !empty($mOpenList) && $mOpenList != null){
            foreach($mOpenList as $k=>$v){
                if(!empty($v['sRegionName'])) {
                    $mOpenList[$k]['sRegionNamepy'] =  Util_Pinyin::get($v['sRegionName']);
                }
                if(!empty($v['sZoneName'])) {
                    $mOpenList[$k]['sZoneNamepy'] =  Util_Pinyin::get($v['sZoneName']);
                }
                //图片处理
                if(!empty($v['sZongPinPic'])){
                    $mOpenList[$k]['sZongPinPicImg'] =  Model_Util::getFormatImag($v['sZongPinPic'],195,140);
                }
                //时间用于新开盘当月显示
                $mOpenList['date'] = substr(date('d',time()),-1)-1;
            }
            return $mOpenList;
        }else{
            return null;
        }
    }

    /**
     * 处理性价比评测榜数据
     * @param $data
     */
    private function formatBangDan($data)
    {
        if(!empty($data)){
            foreach($data as $key=>$value){
                foreach($value['unitList'] as $k=>$v){
                    if(!empty($v['sRegionName'])) {
                        $data[$key]['unitList'][$k]['sRegionpy'] = Util_Pinyin::get($v['sRegionName']);
                    }
                    if(!empty($v['sZoneName'])) {
                        $data[$key]['unitList'][$k]['sZonepy'] = Util_Pinyin::get($v['sZoneName']);
                    }
                    //图片处理
                    if(!empty($v['sZongPinPic'])){
                        $data[$key]['unitList'][$k]['sZongPinimg'] =  Model_Util::getFormatImag($v['sZongPinPic'],180,130);
                    }
                }
            }
            return $data;
        }else{
            return null;
        }
    }

    //处理评级图片
    private function formatPingJiData($data)
    {
        if(!empty($data)){
            foreach($data as $key=>$value){
                $data[$key]['ZongPingPic'] = Model_Util::getFormatImag($value['ZongPingXQ'],50,35);
            }
            return $data;
        }else{
            return null;
        }
    }

    //处理热销滞销数据
    private function formatReXiaoZhiXiao($data,$type)
    {
        $arr = array();
        if(!empty($data)){
            foreach($data as $key=>$value){
                if($type == $value['ListType']){
                    $arr[$key] = $value;
                    $arr[$key]['ZongPingPic'] = Model_Util::getFormatImag($value['ZongPingXQ'],50,35);
                }
            }
            return $arr;
        }else{
            return null;
        }
    }

    //处理首页分析师数据
    private function formatAnalysts($data)
    {

        if(!empty($data)){
            foreach($data['analysts'] as $key=>$value){
                foreach($data['analystsShow'][$key] as $k=>$v){
                    if($value['AnalystsID'] == $v['AnalystsID']){
                        $newArr[$key]['AnalystsID'] = $value['AnalystsID'];
                        $newArr[$key]['AnalystsName'] = $value['AnalystsName'];
                        $newArr[$key]['AnalystLevel'] = $value['AnalystLevel'];
                        $newArr[$key]['AnalystHead'] = Model_Util::getFormatImag($value['AnalystHead'],61,66);;
                        $newArr[$key]['GoodCount'] = $value['GoodCount'];
                        $newArr[$key]['BriefIntroduction'] = $value['BriefIntroduction'];
                        $newArr[$key]['children'][$k]['unitid'] = $v['unitid'];
                        $newArr[$key]['children'][$k]['UnitName'] = $v['UnitName'];
                        $newArr[$key]['children'][$k]['UnitUrl'] = $v['UnitUrl'];
                        $newArr[$key]['children'][$k]['RegionName'] = $v['RegionName'];
                        $newArr[$key]['children'][$k]['OpinionSummary'] = $v['OpinionSummary'];
                        $newArr[$key]['children'][$k]['ZongPingXQ'] =  Model_Util::getFormatImag($v['ZongPingXQ'],380,210);
                    }
                }
            }
            return $newArr;
        }
    }

    //处理首页评测报告
    private function formatEvaluation($data)
    {
        if(!empty($data)){
            foreach($data as $key=>$value){
                $data[$key]['ZongPingXQ'] = Model_Util::getFormatImag($value['ZongPingXQ'],380,210);
                $data[$key]['Url'] = '';
            }
            return $data;
        }else{
            return array();
        }
    }

    //处理首页评级推荐数据
    private function formatPinjiTuiJian($data)
    {
        $newData = array_rand($data,7);
        $newArr = array();
        if(!empty($newData)){
            foreach($newData as $key=>$value){
                $newArr[$key]['ID'] = $data[$value]['ID'];
                $newArr[$key]['UnitID'] = $data[$value]['UnitID'];
                $newArr[$key]['UnitName'] = $data[$value]['UnitName'];
                $newArr[$key]['Url'] = '';
                $newArr[$key]['ZongPingXQ'] = Model_Util::getFormatImag($data[$value]['ZongPingXQ'],70,50);
                $newArr[$key]['RegionName'] = $data[$value]['RegionName'];
                $newArr[$key]['DistrictName'] = $data[$value]['DistrictName'];
                $newArr[$key]['BidingPrice'] = $data[$value]['BidingPrice'];
                $newArr[$key]['TotalScore'] = $data[$value]['TotalScore'];
                //$newArr[$key]['ZongPingPic'] = Model_Util::getFormatImag($data[$value]['ZongPingXQ'],70,50);
            }
            return $newArr;
        }else{
            return null;
        }
    }

    /**
     * 字母搜索数据
     * @return [type] [description]
     */
    public function letterSearchAction() {
        $aList = [];
        $letter = $this->getParam('letter');
        $aList = Sdk_Cms_Unit::getListByLetter($letter);
        if($aList['status'] == 1)
            $aList = $aList['data'];

        $page = ceil(count($aList) / 100);
        $this->assign('page', $page);
        $this->assign('aList', $aList);

        //当前城市字母表
        $aCityLetter = Sdk_Cms_XfCity::getShouzIMu($this->sCurrCityCode);
        $aCityLetter = ($aCityLetter['status'] == 1 && $aCityLetter['data']) ? $aCityLetter['data'] : [];
        $this->assign('aCityLetter', $aCityLetter);

        //当前选中字母
        $this->assign('currentLetter', $letter);
    }

}