<?php

/**
 * Created by PhpStorm.
 * User: ddc
 * Date: 2015/4/10
 * Time: 11:17
 */
class Controller_API_Evaluationwyfw extends Controller_Api_Evaluationbase
{

    public function actionBefore()
    {
        parent::actionBefore();
        $this->config = Model_EvaluationZxBrand::getConfig();
    }

    /**
     * 物业费用
     * param eID 评测报告ID
     * param cID 章节ID
     */
    public function getWyfwAction ()
    {
        $eID = $this->iEvaluationID;
        $pID = $this->iPchapter;
        $cID = $this->iCchapter;

        $aList = Model_EvaluationWyCost::getWyCostByEID($eID);
        if(!empty($aList)) {
            //劣势标签转换
            $aList['sBadTag'] = Model_EvaluationTag::getChapterByIDS($aList['sBadTag']);
            $aList['sGoodTag'] = null; // 没有优势设为空
            if ($aList['sCompanyConfirm']) {
                $aList['sCompanyConfirm'] = '物业公司未确定';
            }
            if ($aList['sPriceConfirm']) {
                $aList['sPriceConfirm'] = '物业费未确定';
            }
            //获取图片配置
            $imgTypeConfig = $this->config['hx']['sType'];
            $imgList = $this->getImage($eID, $imgTypeConfig);
            //其它图片
            $aList['wyqtImage'] = isset($imgList[$imgTypeConfig['wyqt']]) ? $imgList[$imgTypeConfig['wyqt']] : '';
            $this->formatImages($aList,'Image');
            $this->groupAllImages($aList,'Image');

        }else{
            return $this->showMsg(NULL, false);
        }
        return $this->showMsg($aList, true);
    }

    /**
     * 物业服务
     * param eID 评测报告ID
     * param cID 章节ID
     */
    public function getServiceAction ()
    {
        $eID = $this->iEvaluationID;
        $pID = $this->iPchapter;
        $cID = $this->iCchapter;

        $aList = Model_EvaluationWyService::getWyServiceByEID($eID);
        if(!empty($aList)) {
            //优势标签转换
            $aList['sGoodTag'] = Model_EvaluationTag::getChapterByIDS($aList['sGoodTag']);
            //劣势标签转换
            $aList['sBadTag'] = Model_EvaluationTag::getChapterByIDS($aList['sBadTag']);

            if ($aList['sEscrowVehicleChecked'] == 1) {
                $aList['sEscrowVehicleChecked'] = '代管车辆';
            }
            if ($aList['sEscrowHousingChecked'] == 1) {
                $aList['sEscrowHousingChecked'] = '代管房屋';
            }
            if ($aList['sHomeCleaningChecked'] == 1) {
                $aList['sHomeCleaningChecked'] = '上门保洁';
            }
            if ($aList['sEntranceCardChecked'] == 1) {
                $aList['sEntranceCardChecked'] = '补办/新办门禁卡';
            }
            if ($aList['sLaundryChecked'] == 1) {
                $aList['sLaundryChecked'] = '衣物洗整服务';
            }
            if ($aList['sShuttleChilrenChecked'] == 1) {
                $aList['sShuttleChilrenChecked'] = '代接送小孩上学、入托';
            }
            if ($aList['sOnsiteRepairChecked'] == 1) {
                $aList['sOnsiteRepairChecked'] = '上门维修';
            }
            if ($aList['sLandscapingChecked'] == 1) {
                $aList['sLandscapingChecked'] = '园林绿化设计';
            }
            if ($aList['sOtherServiceChecked'] == 1) {
                $aList['sOtherServiceChecked'] = '其它服务';
            }
            //获取图片配置
            $imgTypeConfig = $this->config['hx']['sType'];
            $imgList = $this->getImage($eID, $imgTypeConfig);
            //其它图片
            $aList['fwqtImage'] = isset($imgList[$imgTypeConfig['fwqt']]) ? $imgList[$imgTypeConfig['fwqt']] : '';
            $this->formatImages($aList,'Image');
            $this->groupAllImages($aList,'Image');
        }else{
            return $this->showMsg(NULL, false);
        }
        return $this->showMsg($aList, true);
    }

    /**
     * 获取图片方法
     *param eID 评测报告ID
     * param imgTypeConfig 图片类型配置
     */
    private function  getImage($eID,$imgTypeConfig)
    {
        $img = Model_EvaluationImage::getImages($eID,$imgTypeConfig);
        return $img;
    }
}