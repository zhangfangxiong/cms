<?php
/**
 * 不利因素控制器
 * author: cjj
 */
class Controller_Evaluation_Badfactor extends Controller_Evaluation_Hxbase
{

    const ICATID = 7; // 当前章节

    const ISUBCATID = 21; // 默认子章节

    public function actionBefore() {
        parent::actionBefore();
        $this->config = Model_EvaluationZxBrand::getConfig();
    }
    /**
    * 社区内部
    */
    public function indexAction()
    {
        $this->getID();
        $this->assignCommon();
        $blInSideInfo = Model_EvaluationBlInside::getBlInsideByEID($this->iEvaluationID);
        if (!empty( $blInSideInfo['sGdblys'])) {
            $blInSideInfo['sGdblys'] = json_decode($blInSideInfo['sGdblys'],true); // 固定的不利因素
            $blInSideInfo['sGdblys'] = Model_EvaluationBlInside::convertLowerCase($blInSideInfo['sGdblys']);
        }
        if (!empty($blInSideInfo['sDtblys'])) { // 新增的不利因素
            $blInSideInfo['sDtblys'] = json_decode($blInSideInfo['sDtblys'],true);
        }
        $blImage =  Model_EvaluationImage::getImages($this->iEvaluationID,$this->config['hx']['sType']['blfb']);
        $this->assign('blImage', $blImage);
        $this->assign('blys', $this->config['hx']['yxy']);
        $this->assign('blInSideInfo', $blInSideInfo);
        // 其他图片
        $qtImage = Model_EvaluationImage::getImages($this->iEvaluationID, array(
            3999,
        ));
        $this->assign('qtImage', $qtImage);
    }

    /**
    * 保存社区内部
    * */
    public function saveInSideAction()
    {
        $aParam = $this->_checkInSideData();
        if (empty($aParam)) {
            return null;
        }
        $rs = Model_EvaluationBlInside::saveData($aParam);
        if ($rs) {
            Model_EvaluationImage::addImages($aParam['iEvaluationID'],
                $this->config['hx']['sType']['blfb'],
                $this->getParam('sBlImage'));
            // 添加其他图
            $this -> addQtImages(3999);
            // 删除图片
            Model_EvaluationImage::delImages($this->getParam('sHxImgDel'));
            return $this->showMsg('社区内部数据保存成功', true);
        } else {
            return $this->showMsg('社区内部数据保存失败', false);
        }
    }

    /**
     * 社区外部
     * */
    public function outSideAction()
    {
        $this->getID();
        $this->assignCommon();
        $blOutSideInfo = Model_EvaluationBlOutside::getBlOutsideByEID($this->iEvaluationID);
        if (!empty($blOutSideInfo) && !empty($blOutSideInfo['sBadTag'])) {
            $blImage = Model_EvaluationImage::getImagesByTargetId($blOutSideInfo['sBadTag'],array(
                $this->config['hx']['sType']['blsp'],
            ),$this->iEvaluationID);
            $this->assign('blImage', $blImage);
        }
        $this->assign('blOutSideInfo', $blOutSideInfo);
        $this->assign('imgTypeConfig',  $this->config['hx']['sType']);
        // 其他图片
        $qtImage = Model_EvaluationImage::getImages($this->iEvaluationID, array(
            4099,
        ));
        $this->assign('qtImage', $qtImage);
    }

    /**
     * 保存社区外部
     * */
    public function saveOutSideAction()
    {
        $aParam = $this->_checkOutSideData();
        if (empty($aParam)) {
            return null;
        }
        $rs = Model_EvaluationBlOutside::saveData($aParam);
        if ($rs) {
            $ImageArr = $this->filterOutSideImage($aParam['sBadTag']);
            // 不利因素实拍图
            Model_EvaluationImage::addGroupImages($aParam['iEvaluationID'],
                $this->config['hx']['sType']['blsp'],
                $ImageArr['sBlImage'],
                $ImageArr['sBlImageTitle'],
                $ImageArr['sBlImageDesc'],
                $ImageArr['sBlUpImageId']
            );
            // 添加其他图
            $this -> addQtImages(4099);
            // 删除图
            Model_EvaluationImage::delImages($this->getParam('sHxImgDel'));
            return $this->showMsg('社区外部数据保存成功', true);
        } else {
            return $this->showMsg('社区外部数据保存失败', false);
        }
    }
    /*
     * 过滤图片 只保存已选择的劣势对应的图片
     *
     * */
    public function filterOutSideImage($badTag)
    {
        $ImageArr['sBlImage'] = $this->getParam('sBlImage');
        $ImageArr['sBlImageTitle'] = $this->getParam('sBlImageTitle');
        $ImageArr['sBlImageDesc'] = $this->getParam('sBlImageDesc');
        $ImageArr['sBlUpImageId'] = $this->getParam('sBlUpImageId');
        if (empty($ImageArr) || empty($badTag)) {
            return null;
        }
        foreach ($ImageArr as &$item) {
            if (!empty($item)) {
                foreach ($item  as $k => $v) {
                    if (is_array($badTag)&&!in_array($k,$badTag)) {
                        unset($item[$k]);
                    }
                }
            }
        }
        return $ImageArr;
    }

    /*
    * 获取社区外部的表单数据
    *
    * */
    private function _checkOutSideData()
    {
        $aParam = $this->checkCommonData();
        if (empty($aParam)) {
            return null;
        }
        $aParam['sContent'] = $this->getParam('sContent');
        return $aParam;
    }
    /*
    * 获取社区内部的表单数据
    *
    * */
    private function _checkInSideData()
    {
        $aParam =  $this->checkCommonData();
        if (empty($aParam)) {
            return null;
        }
        $yslDesc = $this->getParam('yslDesc');// 影响楼
        $ysyCheck = $this->getParam('ysyCheck'); // 影响源
        $inherent = array(); // 固定的影响源
        foreach($yslDesc as $k => $item) {
            $inherent[$k]['ysl'] = $item;
            $inherent[$k]['checked'] = isset($ysyCheck[$k]) ? '1':'0';
        }
        $add = array(); // 动态新增的影响源
        $aYsy = $this->getParam('aYsy');// 影响源
        $aBlys = $this->getParam('aBlys');// 不利因素
        $aYsl = $this->getParam('aYsl');// 影响楼
        if (!empty($aYsy)) {
            foreach($aYsy as $k => $item) {
                $add[$k]['ysy'] = $item;
                $add[$k]['blys'] = isset($aBlys[$k]) ? $aBlys[$k]:'';
                $add[$k]['ysl'] = isset($aYsl[$k]) ? $aYsl[$k]:'';
            }
        }

        $aParam['sGdblys'] = json_encode($inherent);
        $aParam['sDtblys'] = !empty($add) ? json_encode($add):'';
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