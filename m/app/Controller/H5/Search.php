<?php
/**
 * Created by PhpStorm.
 * Author: wangsufei
 * CreateTime: 2015/4/28 14:44
 * Description: Xfsearch.php
 */

class Controller_H5_Search extends Controller_Base
{
    static public $aEva = [
        1 => [
            'class' => 'rushou',
            'name'  => '尽快入手',
        ],
        2 => [
            'class' => 'tuijian',
            'name'  => '推荐购买',
        ],
        3 => [
            'class' => 'jinshen',
            'name'  => '谨慎购买',
        ],
        4 => [
            'class' => 'guanwang',
            'name'  => '持币观望',
        ]
    ];
    /**
     * 执行动作之前
     */
    public function actionBefore () {
        parent::actionBefore();
        $this->setFrame('touchweb.phtml');
    }

    /**
     * 执行Action后的操作
     * @see Yaf_Controller::actionAfter()
     */
    public function actionAfter()
    {
        parent::actionAfter();
    }

    /**
     * 首页搜索页
     */
    public function indexAction()
    {
        #region 参数初始化，获取数据
        $iCurrCityID=self::getCurrCity()["iCodeID"];
        $iCurrCityID=empty($iCurrCityID)?1:$iCurrCityID;
        $sTitle=empty($aParam["sUnitName"])?"首页-搜索":$aParam["sUnitName"];

        $aFilter=Sdk_Cms_Xfsearch::getFilter(array('cityID' => $iCurrCityID));
        $aFilter = $aFilter['data'];
        $aMeta['title']=$sTitle;

        #endregion

        #region 返回数据
        $this->assign('aMeta', $aMeta);
        $this->assign('aFilter', $aFilter);
        $this->assign('region', $aFilter["region"]["list"]);
        $this->assign('price', $aFilter["price"]["list"]);
        $this->assign('layout', $aFilter["layout"]["list"]);
        $this->aMeta['title'] .= '搜索';
        #endregion
    }

    /**
     * 首页搜索结果页
     */
    public function listAction()
    {
        #region 参数初始化
        $aParam=$this->getParams ();
        $iPage=empty($aParam["iPage"])?1:$aParam["iPage"];

        $iRegionID=empty($aParam["iRegionID"])?"":$aParam["iRegionID"];
        $iPriceID=empty($aParam["iPriceID"])?"":$aParam["iPriceID"];
        $iLayoutID=empty($aParam["iLayoutID"])?"":$aParam["iLayoutID"];
        $sKeyword=empty($aParam["sKeyword"])?"":$aParam["sKeyword"];

        $iCurrCityID=self::getCurrCity()["iCodeID"];
        $iCurrCityID=empty($iCurrCityID)?1:$iCurrCityID;
        #endregion

        #region 新房搜索数据处理
        $aWhere = array(
            'cityID' => $iCurrCityID,
            'iPage' => $iPage,
            'iRows' => 6,
            'regionID' => $iRegionID,
            'priceID' => $iPriceID,
            'layoutID' => $iLayoutID,
            'keyword' => $sKeyword
        );
        $aList = Sdk_Cms_Xfsearch::getList($aWhere);
        $aList = $aList['data']["list"];

        return $this->showMsg($aList,true);
        #endregion
    }

    /**
     * 楼盘名称搜索*/
    public function unitSearchAction()
    {

    }

    /**
     * 新房搜索页
     */
    public function xfSearchIndexAction()
    {
        #region 参数初始化
        $aParam=$this->getParams ();
        $iPage=empty($aParam["iPage"])?1:$aParam["iPage"];

        $iRegionID=empty($aParam["iRegionID"])?0:$aParam["iRegionID"];
        $iPriceID=empty($aParam["iPriceID"])?0:$aParam["iPriceID"];
        $iLayoutID=empty($aParam["iLayoutID"])?0:$aParam["iLayoutID"];
        $sKeyword=empty($aParam["sKeyword"])?"":$aParam["sKeyword"];

        $iFeatureID=empty($aParam["iFeatureID"])?"":$aParam["iFeatureID"];

        $iCurrCityID=self::getCurrCity()["iCodeID"];
        $iCurrCityID=empty($iCurrCityID)?1:$iCurrCityID;
        #endregion

        #region 新房搜索数据处理
        $aWhere = array(
            'cityID' => $iCurrCityID,
            'iPage' => $iPage,
            'iRows' => 9,
            'regionID' => $iRegionID,
            'priceID' => $iPriceID,
            'layoutID' => $iLayoutID,
            'featureID' => $iFeatureID,
            'keyword' => $sKeyword
        );

        $aList = Sdk_Cms_Xfsearch::getList($aWhere);

        $this->assign('list', $aList);
        $this->assign('where', $aWhere);
        #endregion

        #region 筛选条件数据处理
        $aFilter=Sdk_Cms_Xfsearch::getFilter(array('cityID' => $iCurrCityID));

        $aFilter = $aFilter['data'];

        $this->assign('region', $aFilter["region"]["list"]);
        $this->assign('price', $aFilter["price"]["list"]);
        $this->assign('layout', $aFilter["layout"]["list"]);

        $this->assign('regionCur', self::searchArray($aFilter["region"]["list"],"iCodeID",$aWhere["regionID"]));
        $this->assign('priceCur', self::searchArray($aFilter["price"]["list"],"iCodeID",$aWhere["priceID"]));
        $this->assign('layoutCur', self::searchArray($aFilter["layout"]["list"],"iCodeID",$aWhere["layoutID"]));
        #endregion

        #region 筛选关键词数据处理
        $aFilter=Sdk_Cms_Xfsearch::getFeatures(array('cityID' => $iCurrCityID));
        $aFilter = $aFilter['data'];

        $this->assign('features', $aFilter["features"]);
        #endregion

        $sTitle=empty($aParam["sKeyword"])?"新房-搜索":$aParam["sKeyword"];
        $aMeta['title']=$sTitle;
        $this->assign('aMeta', $aMeta);
        $this->assign('aEva', self::$aEva);
        $this->aMeta['title'] .= '新房搜索';
    }

    /**
     * 新房搜索结果页
     */
    public function xfSearchListAction()
    {
        #region 参数初始化
        $aParam=$this->getParams ();
        $iPage=empty($aParam["iPage"])?1:$aParam["iPage"];

        $iRegionID=empty($aParam["iRegionID"])?0:$aParam["iRegionID"];
        $iPriceID=empty($aParam["iPriceID"])?0:$aParam["iPriceID"];
        $iLayoutID=empty($aParam["iLayoutID"])?0:$aParam["iLayoutID"];
        $sKeyword=empty($aParam["sKeyword"])?"":$aParam["sKeyword"];

        $iFeatureID=empty($aParam["iFeatureID"])?"":$aParam["iFeatureID"];

        $iCurrCityID=self::getCurrCity()["iCodeID"];
        $iCurrCityID=empty($iCurrCityID)?1:$iCurrCityID;
        #endregion

        #region 新房搜索数据处理
        $aWhere = array(
            'cityID' => $iCurrCityID,
            'iPage' => $iPage,
            'iRows' => 9,
            'regionID' => $iRegionID,
            'priceID' => $iPriceID,
            'layoutID' => $iLayoutID,
            'featureID' => $iFeatureID,
            'keyword' => $sKeyword
        );
        $aList = Sdk_Cms_Xfsearch::getList($aWhere);

        $this->showMsg($aList,1);
        #endregion
    }

    /**
     * 数组内查找*/
    public function searchArray($array,$key,$value){
        foreach($array as $keyp=>$valuep){
            if($valuep[$key]==$value){
                return $valuep;
            }
        }
        return $array;
    }
}