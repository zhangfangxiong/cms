<?php
/**
 * 物业服务控制器
 * author: cjj
 */
class Controller_Evaluation_Wyfw extends Controller_Evaluation_Hxbase
{

    const ICATID = 4; // 当前章节

    const ISUBCATID = 11; // 默认子章节

    public function actionBefore() {
        parent::actionBefore();
        $this->config = Model_EvaluationZxBrand::getConfig();
    }

    /**
     * 物业费用
     */
    public function indexAction()
    {
        $this->getID();
        $this->assignCommon();
        $wyCost = Model_EvaluationWyCost::getWyCostByEID($this->iEvaluationID);
        // 其他图片
        $qtImage = Model_EvaluationImage::getImages($this->iEvaluationID, array(
            2999,
        ));
        $this->assign('qtImage', $qtImage);
        $this->assign('wyCost', $wyCost);
    }
    /**
     * 保存物业费用数据
     */
    public function saveCostAction()
    {
        $aParam = $this->checkWyCostData();
        if (empty($aParam)) {
            return null;
        }
        $rs = Model_EvaluationWyCost::saveData($aParam);
        // 添加其他图
        $this -> addQtImages(2999);
        // 删除图片
        Model_EvaluationImage::delImages($this->getParam('sHxImgDel'));
        if ($rs) {
            return $this->showMsg('物业费用数据保存成功', true);
        } else {
            return $this->showMsg('物业费用数据保存失败', false);
        }
    }

    /**
     * 物业服务
     */
    public function serviceAction()
    {
        $this->getID();
        $this->assignCommon();
        $wyService = Model_EvaluationWyService::getWyServiceByEID($this->iEvaluationID);
        // 其他图片
        $qtImage = Model_EvaluationImage::getImages($this->iEvaluationID, array(
            3099,
        ));
        $this->assign('qtImage', $qtImage);
        $this->assign('wyService', $wyService);
    }

    /**
     * 保存物业服务
     */
    public function saveServiceAction()
    {
        $aParam = $this->checkServiceData();
        if (empty($aParam)) {
            return null;
        }
        $rs = Model_EvaluationWyService::saveData($aParam);
        // 添加其他图
        $this -> addQtImages(3099);
        // 删除图片
        Model_EvaluationImage::delImages($this->getParam('sHxImgDel'));
        if ($rs) {
            return $this->showMsg('物业服务数据保存成功', true);
        } else {
            return $this->showMsg('物业服务数据保存失败', false);
        }

    }

    protected function getCatId()
    {
        return self::ICATID;
    }

    protected function getSubCatId()
    {
        return self::ISUBCATID;
    }

    /**
     * 获取物业费用表单数据
     */
    private function checkWyCostData()
    {
        $aParam = $this->checkCommonData();
        if (empty($aParam)) {
            return null;
        }
        $aParam['sCompany'] = $this->getParam('sCompany');
        $aParam['sCompanyConfirm'] = intval($this->getParam('sCompanyConfirm'));
        $aParam['sQuality'] = $this->getParam('sQuality');
        $aParam['sPrice'] = floatval($this->getParam('sPrice'));
        $aParam['sPriceConfirm'] = intval($this->getParam('sPriceConfirm'));
        return $aParam;
    }
    /*
    * 获取物业服务表单数据
    */
    private function checkServiceData()
    {
        $aParam = $this->checkCommonData();
        if (empty($aParam)) {
            return null;
        }
        $aParam['sEscrowVehicle'] = $this->getParam('sEscrowVehicle');
        $aParam['sEscrowVehicleChecked'] = intval($this->getParam('sEscrowVehicleChecked'));
        $aParam['sEscrowHousing'] = $this->getParam('sEscrowHousing');
        $aParam['sEscrowHousingChecked'] = intval($this->getParam('sEscrowHousingChecked'));
        $aParam['sHomeCleaning'] = $this->getParam('sHomeCleaning');
        $aParam['sHomeCleaningChecked'] = intval($this->getParam('sHomeCleaningChecked'));
        $aParam['sEntranceCard'] = $this->getParam('sEntranceCard');
        $aParam['sEntranceCardChecked'] = intval($this->getParam('sEntranceCardChecked'));
        $aParam['sLaundry'] = $this->getParam('sLaundry');
        $aParam['sLaundryChecked'] = intval($this->getParam('sLaundryChecked'));
        $aParam['sShuttleChilren'] = $this->getParam('sShuttleChilren');
        $aParam['sShuttleChilrenChecked'] = intval($this->getParam('sShuttleChilrenChecked'));
        $aParam['sOnsiteRepair'] = $this->getParam('sOnsiteRepair');
        $aParam['sOnsiteRepairChecked'] = intval($this->getParam('sOnsiteRepairChecked'));
        $aParam['sLandscaping'] = $this->getParam('sLandscaping');
        $aParam['sLandscapingChecked'] = intval($this->getParam('sLandscapingChecked'));
        $aParam['sOtherService'] = $this->getParam('sOtherService');
        $aParam['sOtherServiceChecked'] = intval($this->getParam('sOtherServiceChecked'));
        return $aParam;
    }

}