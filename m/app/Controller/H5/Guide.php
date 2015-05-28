<?php
/**
 * Created by PhpStorm.
 * Author: wangsufei
 * CreateTime: 2015/4/28 14:42
 * Description: XfGuide.php
 */

class Controller_H5_Guide extends Controller_Base
{
    /**
     * 执行Action前执行
     * @see Yaf_Controller::actionBefore()
     */
    public function actionBefore()
    {
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
     * 列表页
     * @return
     */
    public function indexAction()
    {
        #region 参数初始化
        $aParam=$this->getParams ();

        $iPage=empty($aParam["iPage"])?1:$aParam["iPage"];
        $iBuildingID=empty($aParam["iBuildingID"])?"":$aParam["iBuildingID"];

        $iCurrCityID=self::getCurrCity()["iCodeID"];
        $iCurrCityID=empty($iCurrCityID)?1:$iCurrCityID;

        $sTitle=empty($aParam["sUnitName"])?"最新导购":$aParam["sUnitName"];
        #endregion

        #region 数据处理
        $aWhere = array(
            'buildingID' => $iBuildingID,
            'cityID' => $iCurrCityID,
            'iPage' => $iPage,
            'iRows' => 9
        );

        $aList = Sdk_Cms_Xfguide::getList($aWhere);

        $list = $aList['data']["list"];
        #endregion

        $aMeta['title'] = $sTitle;

        $this->assign('where', $aWhere);
        $this->assign('sTitle', $sTitle);
        $this->assign('aList', $list);
        $this->assign('aMeta', $aMeta);
        $this->assign('iBuildingID', $iBuildingID);
        $this->aMeta['title'] .= '最新导购';
    }

    public function listAction()
    {
        #region 参数初始化
        $aParam=$this->getParams ();

        $iPage=empty($aParam["iPage"])?1:$aParam["iPage"];
        $iBuildingID=empty($aParam["iBuildingID"])?"":$aParam["iBuildingID"];

        $iCurrCityID=self::getCurrCity()["iCodeID"];
        $iCurrCityID=empty($iCurrCityID)?1:$iCurrCityID;

        $sTitle=empty($aParam["sUnitName"])?"最新导购":$aParam["sUnitName"];
        #endregion

        #region 数据处理
        $aWhere = array(
            'buildingID' => $iBuildingID,
            'cityID' => $iCurrCityID,
            'iPage' => $iPage,
            'iRows' => 9
        );
        $aList = Sdk_Cms_Xfguide::getList($aWhere);
        $list = $aList['data']["list"];
        #endregion

        return $this->showMsg($list, true);
        $this->aMeta['title'] .= '最新导购列表';
    }
}