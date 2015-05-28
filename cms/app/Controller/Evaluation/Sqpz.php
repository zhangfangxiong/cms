<?php

/**
 * 社区品质控制器
 * author: cjj
 */
class Controller_Evaluation_Sqpz extends Controller_Evaluation_Hxbase
{

    const ICATID = 3; // 当前章节

    const ISUBCATID = 5; // 默认子章节

    public function actionBefore()
    {
        parent::actionBefore();
        $this->config = Model_EvaluationZxBrand::getConfig();
    }

    /**
     * 整体规划
     */
    public function indexAction()
    {
        $this->getID();
        $this->assignCommon();
        $sqWhole = Model_EvaluationSqWhole::getSqWholeByEID($this->iEvaluationID);
        $pm = $this->config['hx']['sType']['sqpm'];
        $nk = $this->config['hx']['sType']['sqnk'];
        $images = Model_EvaluationImage::getImages($this->iEvaluationID, array($pm, $nk));
        $sqPmImage = null;
        $sqQjImage = null;
        // 小区平面图
        if (isset($images[$pm])) {
            $sqPmImage = $images[$pm];
        }
        // 全景鸟瞰图
        if (isset($images[$nk])) {
            $sqQjImage = $images[$nk];
        }
        // 其他图片
        $qtImage = Model_EvaluationImage::getImages($this->iEvaluationID, array(
            2399,
        ));
        $this->assign('qtImage', $qtImage);
        $this->assign('sqPmImage', $sqPmImage);
        $this->assign('sqQjImage', $sqQjImage);
        $this->assign('sqWhole', $sqWhole);
    }

    /**
     * 保存整体规划
     */
    public function saveSqWholeAction()
    {
        $aParam = $this->checkData();
        if (empty($aParam)) {
            return null;
        }
        $rs = Model_EvaluationSqWhole::saveData($aParam);
        // 添加小区平面图
        Model_EvaluationImage::addImagesAndDesc($aParam['iEvaluationID'],
            $this->config['hx']['sType']['sqpm'],
            $this->getParam('sqPmImage'),
            $this->getParam('sqPmImageTitle'),
            $this->getParam('sqPmImageDesc'),
            $this->getParam('sqPmUpImageId'));
        // 添加全景鸟瞰图
        Model_EvaluationImage::addImagesAndDesc($aParam['iEvaluationID'],
            $this->config['hx']['sType']['sqnk'],
            $this->getParam('sqQjImage'),
            $this->getParam('sqQjImageTitle'),
            $this->getParam('sqQjImageDesc'),
            $this->getParam('sqQjUpImageId'));
        // 添加点评图
        $this -> addQtImages(2399);
        // 删除图片
        Model_EvaluationImage::delImages($this->getParam('sHxImgDel'));
        if ($rs) {
            return $this->showMsg('整体规划数据保存成功', true);
        } else {
            return $this->showMsg('整体规划数据保存失败', false);
        }
    }

    /**
     * 社区景观
     */
    public function sceneryAction()
    {
        $this->getID();
        $this->assignCommon();
        $xg = $this->config['hx']['sType']['sqxg'];
        $sp = $this->config['hx']['sType']['sqsp'];
        $images = Model_EvaluationImage::getImages($this->iEvaluationID, array($xg, $sp));
        $sqJxImage = null;
        $sqJsImage = null;
        // 景观效果图
        if (isset($images[$xg])) {
            $sqJxImage = $images[$xg];
        }
        // 景观实拍图
        if (isset($images[$sp])) {
            $sqJsImage = $images[$sp];
        }
        // 其他图片
        $qtImage = Model_EvaluationImage::getImages($this->iEvaluationID, array(
            2499,
        ));
        $this->assign('qtImage', $qtImage);
        $sqScenery = Model_EvaluationSqScenery::getSqSceneryByEID($this->iEvaluationID);
        $this->assign('sqJxImage', $sqJxImage);
        $this->assign('sqJsImage', $sqJsImage);
        $this->assign('sqScenery', $sqScenery);
    }

    /**
     * 保存社区景观
     */
    public function saveSceneryAction()
    {
        $aParam = $this->checkSceneryData();
        if (empty($aParam)) {
            return null;
        }
        $rs = Model_EvaluationSqScenery::saveData($aParam);
        // 添加景观效果图
        Model_EvaluationImage::addImagesAndDesc($aParam['iEvaluationID'],
            $this->config['hx']['sType']['sqxg'],
            $this->getParam('sqJxImage'),
            $this->getParam('sqJxImageTitle'),
            $this->getParam('sqJxImageDesc'),
            $this->getParam('sqJxUpImageId'));
        // 添加景观实拍图
        Model_EvaluationImage::addImagesAndDesc($aParam['iEvaluationID'],
            $this->config['hx']['sType']['sqsp'],
            $this->getParam('sqJsImage'),
            $this->getParam('sqJsImageTitle'),
            $this->getParam('sqJsImageDesc'),
            $this->getParam('sqJsUpImageId'));
        // 添加其他图
        $this -> addQtImages(2499);
        // 删除图片
        Model_EvaluationImage::delImages($this->getParam('sHxImgDel'));
        if ($rs) {
            return $this->showMsg('社区景观数据保存成功', true);
        } else {
            return $this->showMsg('社区景观数据保存失败', false);
        }
    }

    /**
    * 建筑立面
    */
    public function buildAction()
    {
        $this->getID();
        $this->assignCommon();
        $wx = $this->config['hx']['sType']['sqwx'];
        $ws = $this->config['hx']['sType']['sqws'];
        $images = Model_EvaluationImage::getImages($this->iEvaluationID, array($wx, $ws));
        $sqWxImage = null;
        $sqWsImage = null;
        // 外立面效果图
        if (isset($images[$wx])) {
            $sqWxImage = $images[$wx];
        }
        // 外立面实拍图
        if (isset($images[$ws])) {
            $sqWsImage = $images[$ws];
        }
        // 其他图片
        $qtImage = Model_EvaluationImage::getImages($this->iEvaluationID, array(
            2599,
        ));
        $this->assign('qtImage', $qtImage);
        $sqBuilding = Model_EvaluationSqBuilding::getSqBuildingByEID($this->iEvaluationID);
        $this->assign('sqWxImage', $sqWxImage);
        $this->assign('sqWsImage', $sqWsImage);
        $this->assign('sqBuilding', $sqBuilding);

    }

    /**
     * 保存建筑立面
     */
    public function saveBuildAction()
    {
        $aParam = $this->checkBuildData();
        if (empty($aParam)) {
            return null;
        }
        $rs = Model_EvaluationSqBuilding::saveData($aParam);
        // 添加外立面效果图
        Model_EvaluationImage::addImagesAndDesc($aParam['iEvaluationID'],
            $this->config['hx']['sType']['sqwx'],
            $this->getParam('sqWxImage'),
            $this->getParam('sqWxImageTitle'),
            $this->getParam('sqWxImageDesc'),
            $this->getParam('sqWxUpImageId'));
        // 添加外立面实拍图
        Model_EvaluationImage::addImagesAndDesc($aParam['iEvaluationID'],
            $this->config['hx']['sType']['sqws'],
            $this->getParam('sqWsImage'),
            $this->getParam('sqWsImageTitle'),
            $this->getParam('sqWsImageDesc'),
            $this->getParam('sqWsUpImageId'));
        // 添加其他图
        $this -> addQtImages(2599);
        // 删除图片
        Model_EvaluationImage::delImages($this->getParam('sHxImgDel'));
        if ($rs) {
            return $this->showMsg('建筑立面数据保存成功', true);
        } else {
            return $this->showMsg('建筑立面数据保存失败', false);
        }
    }

    /**
     * 公共部位
     */
    public function publicAction()
    {
        $this->getID();
        $this->assignCommon();
        $sqPublic = Model_EvaluationSqPublic::getSqPublicByEID($this->iEvaluationID);
        // 公共部位图片
        $zxImages = Model_EvaluationImage::getImages($this->iEvaluationID, $this->filterConfig(300, 400));
        $this->assign('sqPublic', $sqPublic);
        $this->assign('zxImages', $zxImages);
        // 其他图片
        $qtImage = Model_EvaluationImage::getImages($this->iEvaluationID, array(
            2699,
        ));
        $this->assign('qtImage', $qtImage);
        // 图片类型
        $this->assign('imgTypeConfig', $this->config['hx']['sType']);
    }

    /**
     * 保存公共部位
     */
    public function savePublicAction()
    {
        $aParam = $this->checkPublicData();
        if (empty($aParam)) {
            return null;
        }
        $rs = Model_EvaluationSqPublic::saveData($aParam);
        //添加公共部位图片
        Model_EvaluationImage::addBatchImages($aParam['iEvaluationID'], $this->getParam('publicImage'));
        // 添加其他图
        $this -> addQtImages(2699);
        // 删除图片
        Model_EvaluationImage::delImages($this->getParam('sHxImgDel'));
        if ($rs) {
            return $this->showMsg('公共部位数据保存成功', true);
        } else {
            return $this->showMsg('公共部位数据保存失败', false);
        }
    }

    /**
     * 社区配置
     */
    public function configAction()
    {
        $this->getID();
        $this->assignCommon();
        $this->assign('imgTypeConfig', $this->config['hx']['sType']);
        $sqConfig = Model_EvaluationSqConfig::getSqConfigByEID($this->iEvaluationID);
        // 公共部位图片
        $sqConfigImage = Model_EvaluationImage::getImages($this->iEvaluationID, $this->filterConfig(300, 340));
        $this->assign('sqConfigImage', $sqConfigImage);
        // 其他图片
        $qtImage = Model_EvaluationImage::getImages($this->iEvaluationID, array(
            2799,
        ));
        $this->assign('qtImage', $qtImage);
        $this->assign('sqConfig', $sqConfig);
    }
    /**
     * 保存社区配置
     */
    public function saveConfigAction()
    {
        $this->getID();
        $this->assignCommon();
        $aParam = $this->checkConfigData();
        if (empty($aParam)) {
            return null;
        }
        $rs = Model_EvaluationSqConfig::saveData($aParam);
        // 保存图片
        Model_EvaluationImage::addBatchImagesAndDesc(
            $aParam['iEvaluationID'],
            $this->getParam('sqConfigImage'),
            $this->getParam('sqConfigImageTitle'),
            $this->getParam('sqConfigImageDesc'),
            $this->getParam('sqConfigUpImageId')
        );
        // 添加其他图
        $this -> addQtImages(2799);
        // 删除图片
        Model_EvaluationImage::delImages($this->getParam('sHxImgDel'));
        if ($rs) {
            return $this->showMsg('社区配置数据保存成功', true);
        } else {
            return $this->showMsg('社区配置数据保存失败', false);
        }

    }
    /**
     * 车位情况
     */
    public function parkingAction()
    {
        $this->getID();
        $this->assignCommon();
        $sqParking = Model_EvaluationSqParking::getSqParkingByEID($this->iEvaluationID);
        // 车位相关图
        $sqParkingImages = Model_EvaluationImage::getImages($this->iEvaluationID, array(
            $this->config['hx']['sType']['cwpm'],
            $this->config['hx']['sType']['cwsj'],
            $this->config['hx']['sType']['xclx']
        ));
        // 其他图片
        $qtImage = Model_EvaluationImage::getImages($this->iEvaluationID, array(
            2899,
        ));
        $this->assign('qtImage', $qtImage);
        // 超过两个图片字段 用数组处理
        $this->assign('imgTypeConfig', $this->config['hx']['sType']);
        $this->assign('sqParking', $sqParking);
        $this->assign('sqParkingImages', $sqParkingImages);

    }

    /**
     * 保存车位数据
     */
    public function saveParkingAction()
    {
        $aParam = $this->checkParkingData();
        if (empty($aParam)) {
            return null;
        }
        $rs = Model_EvaluationSqParking::saveData($aParam);

        //添加 车位相关图片 todo:图片类型超过两个批量处理
        Model_EvaluationImage::addBatchImagesAndDesc(
            $aParam['iEvaluationID'],
            $this->getParam('sqPkImage'),
            $this->getParam('sqPkImageTitle'),
            $this->getParam('sqPkImageDesc'),
            $this->getParam('sqPkUpImageId')
        );
        // 添加其他图
        $this -> addQtImages(2899);
        // 删除图片
        Model_EvaluationImage::delImages($this->getParam('sHxImgDel'));
        if ($rs) {
            return $this->showMsg('车位数据保存成功', true);
        } else {
            return $this->showMsg('车位数据保存失败', false);
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

    /*
     * 整体规划表单数据
     *
    */
    private function checkData()
    {
        $aParam = $this->checkCommonData();
        if (empty($aParam)) {
            return null;
        }
        $aParam['sPlanDesc'] = $this->getParam('sPlanDesc');
        return $aParam;

    }

    /*
     * 社区景观表单数据
     *
    */
    private function checkSceneryData()
    {

        return $this->checkCommonData();

    }

    /*
    * 建筑立面表单数据
    *
    */
    private function checkBuildData()
    {
        return $this->checkCommonData();
    }

    /*
    * 公共部位表单数据
    */
    private function checkPublicData()
    {
        $aParam = array();
        if (!empty($_POST)) {
            foreach ($_POST as $k => $v) {
                if (!in_array($k, array('publicImage'))) {
                    $aParam[$k] = $v;
                }
            }
        }
        return $aParam;
    }

    /*
    * 车位情况表单数据
    */
    private function checkParkingData()
    {

        $aParam = $this->checkCommonData();
        if (empty($aParam)) {
            return null;
        }
        $aParam['iTotalNum'] = intval($this->getParam('iTotalNum'));
        $aParam['iFixedNum'] = intval($this->getParam('iFixedNum'));
        $aParam['iTempNum'] = intval($this->getParam('iTempNum'));
        $aParam['iRate'] = floatval($this->getParam('iRate'));
        $aParam['iSellPrice'] = floatval($this->getParam('iSellPrice'));
        $aParam['iRentPrice'] = floatval($this->getParam('iRentPrice'));
        $aParam['iTempPrice'] = floatval($this->getParam('iTempPrice'));
        $aParam['iUndergroundNum'] = intval($this->getParam('iUndergroundNum'));
        $aParam['iOvergroundNum'] = intval($this->getParam('iOvergroundNum'));
        $aParam['sPakingDesc'] = $this->getParam('sPakingDesc');
        return $aParam;
    }
    /*
     * 社区配置表单数据
    */
    private function checkConfigData()
    {
        $aParam['sComment'] = $this->getParam('sComment');
        $aParam['iEvaluationID'] = intval($this->getParam('iEvaluationID'));
        $aParam['sOtherComment'] = $this->getParam('sOtherComment');
        $aParam['iIsConfig'] = intval($this->getParam('iIsConfig'));
        $aParam['iSnyycChecked'] = intval($this->getParam('iSnyycChecked'));
        $aParam['iSnjsfChecked'] = intval($this->getParam('iSnjsfChecked'));
        $aParam['iSnqpfChecked'] = intval($this->getParam('iSnqpfChecked'));
        $aParam['iSnqtChecked'] = intval($this->getParam('iSnqtChecked'));
        if (empty($aParam['iEvaluationID'])) {
            return $this->showMsg('请选择评测报告！', false);
        }
        return $aParam;
    }

}