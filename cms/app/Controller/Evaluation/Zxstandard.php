<?php

/**
 * 装修标准品牌配置控制器
 * author: cjj
 */
class Controller_Evaluation_Zxstandard extends Controller_Evaluation_Hxbase
{

    const ICATID = 2; // 当前章节

    const ISUBCATID = 3; // 默认子章节


    protected $config = null;

    public function actionBefore()
    {
        parent::actionBefore();
        $this->config = Model_EvaluationZxBrand::getConfig();
    }

    /**
     * 品牌配置
     */
    public function indexAction()
    {
        $this->getID();
        $this->assignCommon();
        $this->assign('imgTypeConfig', $this->config['hx']['sType']);
        // 品牌
        $brand = Model_EvaluationZxBrand::getBrandByEID($this->iEvaluationID);
        // 品牌图片
        $zxImages = Model_EvaluationImage::getImages($this->iEvaluationID, $this->filterConfig(200, 300));
        // 其他图片
        $qtImage = Model_EvaluationImage::getImages($this->iEvaluationID, array(
            2199,
        ));
        $this->assign('qtImage', $qtImage);
        $this->assign('brandConfig', $this->config['standard']);
        $this->assign('zxStandard', $brand);
        $this->assign('zxImages', $zxImages);
    }

    /**
     * 装修分析
     */
    public function analysisAction()
    {

        $this->getID();
        $this->assignCommon();
        $analysis = Model_EvaluationZxAnalysis::getZxAnalysisByEID($this->iEvaluationID);
        // 是否毛坯交付
        $brandInfo = Model_EvaluationZxBrand::getBrandByEID($this->iEvaluationID);
        if (!empty($brandInfo)) {
            $analysis['iIsBlank'] = $brandInfo['iIsBlank'];
        } else {
            $analysis['iIsBlank'] = 0;
        }
        // 装修分析图片
        $analysisImage = Model_EvaluationImage::getImages($this->iEvaluationID, $this->config['hx']['sType']['zxms']);
        $this->assign('analysisImage', $analysisImage);
        // 其他图片
        $qtImage = Model_EvaluationImage::getImages($this->iEvaluationID, array(
            2299,
        ));
        $this->assign('qtImage', $qtImage);
        $this->assign('analysisConfig', $this->config['standard']);
        $this->assign('analysis', $analysis);
    }


    /**
     * 保存品牌配置
     *
     */
    public function saveBrandAction()
    {
        // 添加品牌数据
        $aParam = $this->checkData();
        $rs = Model_EvaluationZxBrand::saveData($aParam);
        //添加品牌图片
        Model_EvaluationImage::addBatchImages(intval($aParam['iEvaluationID']), $this->getParam('brandImage'));
        // 添加点评图
        $this -> addQtImages(2199);
        // 删除图片
        Model_EvaluationImage::delImages($this->getParam('sHxImgDel'));
        if ($rs) {
            return $this->showMsg('品牌数据保存成功', true);
        } else {
            return $this->showMsg('品牌数据保存失败', false);
        }

    }

    /**
     * 保存装修分析
     *
     */
    public function saveAnalysisAction()
    {
        $aParam = $this->checkAnalysisData();
        if (empty($aParam)) return null;
        $rs = Model_EvaluationZxAnalysis::saveData($aParam);
        // 添加样板房实拍图
        Model_EvaluationImage::addImagesAndDesc($aParam['iEvaluationID'],
            $this->config['hx']['sType']['zxms'],
            $this->getParam('analysisImage'),
            $this->getParam('analysisImageTitle'),
            $this->getParam('analysisImageDesc'),
            $this->getParam('analysisUpImageId'));
        // 添加点评图
        $this -> addQtImages(2299);
        // 删除图片
        Model_EvaluationImage::delImages($this->getParam('sHxImgDel'));
        if ($rs) {
            return $this->showMsg('装修分析数据保存成功', true);
        } else {
            return $this->showMsg('装修分析数据保存失败', false);
        }
    }

    /*
     * 选择毛坯交付
     *

    public function  checkBlankAction()
    {
        $iIsBlank = intval($this->getParam('iIsBlank'));
        Util_Cookie::set('isBlank',  $iIsBlank);
        return $this->showMsg('', true);
    }
    */


    /**
     * 获取品牌配置的表单参数
     *
     */
    private function checkData()
    {
        $aParam = array();
        if (!empty($_POST)) {
            foreach ($_POST as $k => $v) {
                if (!in_array($k, array('brandImage'))) {
                    $aParam[$k] = $v;
                }
            }
        }
        return $aParam;
    }

    /*
     * 获取装修分析表单数据
     */
    public function checkAnalysisData()
    {
        $aParam['sComment'] = $this->getParam('sComment');
        $aParam['iEvaluationID'] = intval($this->getParam('iEvaluationID'));
        $aParam['iAutoID'] = intval($this->getParam('iAutoID'));
        $aParam['sGoodTag'] = $this->getParam('sGoodTag');
        $aParam['sBadTag'] = $this->getParam('sBadTag');
        $aParam['iPrice'] = floatval($this->getParam('iPrice'));
        $aParam['iCostRate'] = intval($this->getParam('iCostRate'));
        $aParam['iCostPercent'] = $this->getParam('iCostPercent');
        $aParam['sOtherComment'] = $this->getParam('sOtherComment');
        if (empty($aParam['iEvaluationID'])) {
            return $this->showMsg('请选择评测报告！', false);
        }
        return $aParam;
    }

    protected function getCatId()
    {
        return self::ICATID;
    }


    protected function getSubCatId()
    {
        return self::ISUBCATID;
    }
}