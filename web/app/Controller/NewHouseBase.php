<?php

class Controller_NewHouseBase extends Controller_Base
{
    /**
     * 执行Action前执行
     * @see Yaf_Controller::actionBefore()
     */
    protected $unitID = 0;//楼盘老表中的楼盘ID
    protected $louID = 0;//楼盘新表中的楼盘ID

    protected $cityInfo = array();
    protected $unitInfo = array();
    private $_aMenu = array();
    private $_aSaleSate = array(
        array('text'=>'待售','class' => 'ds_status'),
        array('text'=>'在售','class' => 'zs_status'),
        array('text'=>'售罄','class' => 'sk_status'),
    );//销售状态

    public function actionBefore()
    {
        parent::actionBefore();
        // 当前城市相关信息
        $this->cityInfo = Model_City::getCityInfo($this->sCurrCityCode);

        $this->assign('cityInfo', $this->cityInfo);
        // 楼盘信息
        $unitID = intval($this->getParam('unitid'));
        $unitInfo = Model_Unit::getUnitInfo($unitID);
        if (empty($unitInfo)) {
            return $this->_404();
        }
        $aLouID = Sdk_Cms_Newhouse::getNewLouID($unitID);
        if (!$aLouID['status']) {
            $this->showMsg($aLouID['data']['msg'], true);//新楼盘表中无相关数据
        }
        $this->louID = $aLouID['data']['list']['LouID'];

        $this->unitID = $unitID;
        $this->unitInfo = $unitInfo;
        if (empty($this->unitInfo)) {
            return $this->_404();
        }

        if(!empty($this->unitInfo['unit'])){
            $this->unitInfo['unit']["ZongPingXqUrl"]=$this->getImageUrl($this->unitInfo['unit']["ID"],$this->unitInfo['unit']["Wlm2Pic"],390,308);
            $this->unitInfo['unit']["ZongPingXqUrlS"]=$this->getImageUrl($this->unitInfo['unit']["ID"],$this->unitInfo['unit']["Wlm2Pic"],133,98);
            $this->unitInfo['unit']["BidingPriceFmt"]=Model_Util::getFormatPrice($this->unitInfo['unit']["BidingPrice"]);
            $this->unitInfo['unit']["PriceFmt"]=Model_Util::getFormatPrice($this->unitInfo['unit']["Price"]);
            $this->unitInfo['unit']["BuildAreaFmt"]=sprintf("%.1f", $this->unitInfo['unit']["BuildArea"]);
            $this->unitInfo['unit']["FinalSaleState"]=$this->getSaleState($unitInfo['unit']["SaleState"]);
            $sSaleState=$this->unitInfo['unit']["FinalSaleState"]=="在售"?1:($this->unitInfo['unit']["FinalSaleState"]=="售罄"?2:0);
            $this->unitInfo['unit']["FinalSaleStateIndex"]=$sSaleState;
            $this->unitInfo['unit']["NewRlv"]=$this->getNewRlv($this->unitInfo['unit']["EJScore"]);

        }
        $this->assign('aUser',Util_Cookie::get('userInfo'));
        $this->assign('unit',$this->unitInfo['unit']);

        //  最新批次信息
        $newestBatchTypes = Model_BatchTypes::newestBatchTypes($this->unitInfo['unit']['UnitID']);
        $this->assign('newBatchType', isset($newestBatchTypes[0]) ? $newestBatchTypes[0] : array()); // 本期
        $this->assign('lastBatchType', isset($newestBatchTypes[1]) ? $newestBatchTypes[1] : array()); // 上期
        $this->assign('newestBatchTypes', $newestBatchTypes);

        // 400电话
        $tel400 = Model_City::getCity400($this->sCurrCityName, $this->unitInfo['unit']['RegionName']);
        $this->assign('tel400', $tel400);

        // 住宅类型
        $this->assign('HomeType', $this->getHomeType($newestBatchTypes));
        $this->assign('aMenu',$this->getMenu($unitID));
        $this->assign('sCurUrl',$this->getCurUrl());
        $this->assign('aSaleSate',$this->_aSaleSate);
    }

    /**
     * 返回图片地址(区分cric的图片)
     */
    public static function getImageUrl($iCricId,$image,$w=200,$h=150,$p1=0,$p2=0)
    {
        if (!empty($iCricId)) {
            return Util_Uri::getCricViewURL($image,$w,$h,$p1,$p2);
        } else {
            return Util_Uri::getDFSViewURL ($image,250,210);
        }
    }

    protected function getMenu($unitid)
    {
        Model_Util::setCityCode($this->sCurrCityCode);
        //这里还有显示不显示的调整，到时候再加
        if (empty($this->_aMenu)) {
            $_aMenu = array(
                array('name'=>'楼盘首页','index' => '1','url'=>Model_Util::getDetailUrl($unitid)),
                array('name'=>'详细评测','index' => '2','url'=>Model_Util::getDetailUrl($unitid)),
                array('name'=>'价目表','index' => '3','url'=>Model_Util::getDetailUrl($unitid)),
                array('name'=>'楼盘点评','index' => '4','url'=>Model_Util::getDetailUrl($unitid)),
                array('name'=>'优惠价格','index' => '5','url'=>Model_Util::getDetailUrl($unitid,1,'Discount')),
                array('name'=>'楼盘详情','index' => '6','url'=>Model_Util::getDetailUrl($unitid,1,'Loudetail'))
            );
            $this->_aMenu = $_aMenu;
        }
        return $this->_aMenu;
    }

    //销售状态
    protected function getSaleState($sSaleState)
    {
        if($sSaleState=="形象"||$sSaleState=="未公开"){
            $sSaleState="待售";
        }
        elseif($sSaleState=="预售"||$sSaleState=="尾盘"||$sSaleState=="销售"){
            $sSaleState="在售";
        }
        elseif($sSaleState=="售罄"||$sSaleState=="烂尾楼"){
            $sSaleState="售罄";
        }
        else{
            $sSaleState="";
        }
        return $sSaleState;
    }

    //推荐等级
    protected function getNewRlv($sEJScore)
    {
        if(empty($sEJScore)){
            return "";
        }
        switch ($sEJScore)
        {
            case "尽快入手":
            case "AAA":
                return "尽快入手";
            case "A+":
            case "推荐购买":
                return "推荐购买";
            case "BBB+":
            case "谨慎购买":
                return "谨慎购买";
            case "C":
            case "持币观望":
                return "持币观望";
            default:
                return "";
        }
    }

    //住宅类型
    protected function getHomeType(&$newestBatchTypes)
    {
        return isset($newestBatchTypes[0]) ? preg_replace('/;/', '', $newestBatchTypes[0]['HomeType']) : '';
    }
}