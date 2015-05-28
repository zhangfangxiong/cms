<?php
/**
 * 户型分析控制器
 * author: cjj
 * Date: 2015/1/26
 */
class Controller_Evaluation_Hxanalyse extends Controller_Evaluation_Hxbase
{

    const ICATID = 1; // 当前章节

    const ISUBCATID = 1; // 默认子章节


    /**
    * 默认整体评价
    */

    public function indexAction()
    {
        $this->getID();
        // 户型相关配置
        $config = Model_EvaluationHxWhole::getConfig();
        // 整体评价信息
        $hxWhole = Model_EvaluationHxWhole::getHxWholeByEID($this->iEvaluationID);
        // 户型基本信息(面积，南边朝向等数据)
        $hxInfoList  = Model_EvaluationHxType::getHxTypeList($this->iEvaluationID);
        $hxInfoHtml =  $this->getView()->render(Model_EvaluationHxType::TP_PATH,array(
            'list' => $hxInfoList,
            'evaluationSwitch' => $config['evaluationSwitch']
        ));
        // 户型至少显示一个
        if (empty($HxType)) {
            $HxType[0]['iRoomNum'] = 0;
            $HxType[0]['iHallNum'] = 0;
            $HxType[0]['iToiletNum'] = 0;
        }
        // 其他图片
        $qtImage = Model_EvaluationImage::getImages($this->iEvaluationID, array(
            2099,
        ));
        $this->assign('qtImage', $qtImage);
        $this->assign('hxInfoHtml', $hxInfoHtml);
        $this->assign('hxWhole', $hxWhole);
        $this->assign('HxType', $HxType);
        $this->assign('hxConifg', $config['hx']);
        $this->assignCommon();
    }

    /**
     * 添加整体评价
     *
     */
    public function addAction()
    {

        $aParam = $this->_checkData();
        if (empty($aParam)) return null;
        if (Model_EvaluationHxWhole::addHxWhole($aParam) > 0) {
            // 添加点评图
            $this -> addQtImages(2099);
            // 删除图
            Model_EvaluationImage::delImages($this->getParam('sHxImgDel'));
            return $this->showMsg('整体评价信息保存成功！', true);
        } else {
            return $this->showMsg('整体评价信息保存失败！', false);
        }
    }


    /**
     * 户型分析
     */
    public function hxAction()
    {
        $this->getID();
        $this->assignCommon();
        // 户型分析信息
        $HxAnalyse = Model_EvaluationHxAnalyse::getHxAnalyseByEID($this->iEvaluationID);
        // 户型信息
        $HxType  = Model_EvaluationHxType::getHxTypeList($this->iEvaluationID);
        $HxType = $this->formatHxType($HxType);
        $config = Model_EvaluationHxWhole::getConfig();
        // 户型相关图
        $HxImages = array();
        if (!empty($HxAnalyse['sHuXingID'])) {
            $HxAnalyse['sHuXingID'] = explode(',',$HxAnalyse['sHuXingID']);
            $HxImages = Model_EvaluationImage::getImagesByTargetId($HxAnalyse['sHuXingID'],array(
                $config['hx']['sType']['hd'],
                $config['hx']['sType']['sp'],
            ),$this->iEvaluationID);
        }
        // 点评图
        $dpImages = Model_EvaluationImage::getImages($this->iEvaluationID, array(
            $config['hx']['sType']['dp'],
        ));
        $this->assign('imgTypeConfig',  $config['hx']['sType']);
        $this->assign('HxImages', $HxImages);
        $this->assign('dpImages', $dpImages);
        $this->assign('HxType', $HxType);
        $this->assign('HxAnalyse', $HxAnalyse);

    }

    private  function formatHxType($HxType) {
        $newArr = array();
        if (!empty($HxType)) {
            foreach($HxType as $k => $val) {
                $newArr[$val['iAutoID']] = $val;
            }
        }
        return $newArr;
    }
    /**
     * 添加 户型分析
     */
    public function addHxanalyseAction()
    {
        $aParam = $this->_checkHxanalyseData();
        $aImgData = $this->_checkImgData();
        if (empty($aParam)) {
            return null;
        }
        // 户型编号id 数组key为户型编号表主键
        if (!empty($aImgData['sHxImageTitle'])) {
            $aParam['sHuXingID'] = implode(',',array_keys($aImgData['sHxImageTitle']));
        }
        $config = Model_EvaluationHxWhole::getConfig();
        // 添加户型
        if (Model_EvaluationHxAnalyse::addHxAnalyse($aParam)) {
            if (!empty($aImgData)) {
                // 添加户型图
                Model_EvaluationImage::addGroupImages($aParam['iEvaluationID'],
                    $config['hx']['sType']['hd'],
                    $aImgData['sHxImage'],
                    $aImgData['sHxImageTitle'],
                    $aImgData['sHxImageDesc'],
                    $aImgData['sHxUpImageId']
                );
                // 添加户型实拍图
                Model_EvaluationImage::addGroupImages($aParam['iEvaluationID'],
                    $config['hx']['sType']['sp'],
                    $aImgData['sHxRealImage'],
                    $aImgData['sHxRealImageTitle'],
                    $aImgData['sHxRealImageDesc'],
                    $aImgData['sHxRealUpImageId']
                );
                // 添加点评图
                Model_EvaluationImage::addImagesAndDesc($aParam['iEvaluationID'],
                    $config['hx']['sType']['dp'],
                    $aImgData['sHxOtherDpImg'],
                    $aImgData['sHxOtherDpImgTitle'],
                    $aImgData['sHxOtherDpImgDesc'],
                    $aImgData['sHxOtherUpImageId']);
                // 删除图
                Model_EvaluationImage::delImages($aImgData['sHxImgDel']);
            }
            return $this->showMsg('户型数据保存成功！', true);
        } else {
            return $this->showMsg('户型数据保存失败！', false);
        }
    }




    /*
     * 数据检测 整体评价表单数据
     */
    private function _checkData()
    {
        $aParam = array();
        $aParam['IHxWholeID'] = intval($this->getParam('IHxWholeID'));
        $aParam['iEvaluationID'] = intval($this->getParam('iEvaluationID'));
        $aParam['sComment'] = htmlspecialchars($this->getParam('sComment'));
        $aParam['sGoodTag'] = $this->getParam('sGoodTag');
        $aParam['sBadTag'] = $this->getParam('sBadTag');
        $aParam['sOtherComment'] = htmlspecialchars($this->getParam('sOtherComment'));
        if (empty($aParam['iEvaluationID'])) {
            return $this->showMsg('请选择评测报告！', false);
        }
        if (empty($aParam['sComment'])) {
            return $this->showMsg('请输入分析师点评！', false);
        }
//        if (empty($aParam['sOtherComment'])) {
//            return $this->showMsg('请输入户型整体描述！', false);
//        }
        return $aParam;
    }

    /*
     * 户型分析表单数据
     */
    private function _checkHxanalyseData()
    {
        $aParam['iEvaluationID'] = intval($this->getParam('iEvaluationID'));
        $aParam['iAutoID'] = intval($this->getParam('iAuID'));
        $aParam['sComment'] = htmlspecialchars($this->getParam('sComment'));
        $aParam['sGoodTag'] = $this->getParam('sGoodTag');
        $aParam['sBadTag'] = $this->getParam('sBadTag');
        $aParam['sOtherComment'] = htmlspecialchars($this->getParam('sOtherComment'));
        if (empty($aParam['iAutoID'])) {
            unset($aParam['iAutoID']);
        }
        if (empty($aParam['iEvaluationID'])) {
            return $this->showMsg('请选择评测报告！', false);
        }
        if (empty($aParam['sComment'])) {
            return $this->showMsg('请输入分析师点评！', false);
        }
        return $aParam;
    }
    /*
     * 图片相关表单数据
     */
    private function _checkImgData()
    {
        $aParam['sHxImage'] = $this->getParam('sHxImage'); // 户型图
        $aParam['sHxImageTitle'] = $this->getParam('sHxImageTitle');
        $aParam['sHxImageDesc'] = $this->getParam('sHxImageDesc');
        $aParam['sHxUpImageId'] = $this->getParam('sHxUpImageId');
        $aParam['sHxRealImage'] = $this->getParam('sHxRealImage');// 户型实拍图
        $aParam['sHxRealImageTitle'] = $this->getParam('sHxRealImageTitle');
        $aParam['sHxRealImageDesc'] = $this->getParam('sHxRealImageDesc');
        $aParam['sHxRealUpImageId'] = $this->getParam('sHxRealUpImageId');
        $aParam['sHxOtherDpImg'] = $this->getParam('sHxOtherDpImg'); //点评图
        $aParam['sHxOtherDpImgDesc'] = $this -> getParam('sHxOtherDpImgDesc');
        $aParam['sHxOtherUpImageId'] = $this -> getParam('sHxOtherUpImageId');
        $aParam['sHxOtherDpImgTitle'] = $this -> getParam('sHxOtherDpImgTitle');

        $aParam['sHxImgDel'] = $this -> getParam('sHxImgDel');
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