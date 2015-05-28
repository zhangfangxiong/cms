<?php
/**
 * 区域配套控制器
 * author: cjj
 */
class Controller_Evaluation_Region extends Controller_Evaluation_Hxbase
{

    const ICATID = 6; // 当前章节

    const ISUBCATID = 16; // 默认子章节

    public function actionBefore() {
        parent::actionBefore();
        $this->config = Model_EvaluationQyMedical::getConfig();
    }
    /**
     * 区位简介
     */
    public function indexAction()
    {
        $this->getID();
        $this->assignCommon();
        $qyPosition = Model_EvaluationQyPosition::getQyPositionByEID($this->iEvaluationID);
        $qyPositionImages =  Model_EvaluationImage::getImages($this->iEvaluationID,$this->config['hx']['sType']['qytp']);
        $this->assign('qyPositionImages', $qyPositionImages);
        // 其他图片
        $qtImage = Model_EvaluationImage::getImages($this->iEvaluationID, array(
            3499,
        ));
        $this->assign('qtImage', $qtImage);
        $this->assign('qyPosition', $qyPosition);
    }

    /**
     * 保存区位简介
     */
    public function savePositionAction()
    {
        $aParam = $this->_checkPositionData();
        if(empty($aParam)) {
            return null;
        }
        $rs = Model_EvaluationQyPosition::saveData($aParam);
        Model_EvaluationImage::addImagesAndDesc($aParam['iEvaluationID'],
            $this->config['hx']['sType']['qytp'],
            $this->getParam('qyPositionImages'),
            $this->getParam('qyPositionImagesTitle'),
            $this->getParam('qyPositionImagesDesc'),
            $this->getParam('qyPositionUpImageId'));
        // 添加其他图
        $this -> addQtImages(3499);
        // 删除图片
        Model_EvaluationImage::delImages($this->getParam('sHxImgDel'));
        if ($rs) {
            return $this->showMsg('区位简介数据保存成功', true);
        } else {
            return $this->showMsg('区位简介数据保存失败', false);
        }
    }

    /**
     * 教育
     */
    public function  educateAction()
    {
        $this->getID();
        $this->assignCommon();
        $qyEducate = Model_EvaluationQyEducate::getQyEducateByEID($this->iEvaluationID);
        //获取幼儿园信息
        $kInfo = Model_EvaluationQyEducateInfo::getQyEducateInfoByEID($qyEducate['iAutoID'],1);
        //获取小学信息
        $pInfo = Model_EvaluationQyEducateInfo::getQyEducateInfoByEID($qyEducate['iAutoID'],2);
        //获取中学信息
        $mInfo = Model_EvaluationQyEducateInfo::getQyEducateInfoByEID($qyEducate['iAutoID'],3);
        //获取幼儿园图片
        $kImg = Model_EvaluationImage::getImages($this->iEvaluationID, $this->config['hx']['sType']['kindergarten']);
        if(!empty($kImg)){
            foreach($kImg as $key=>$value){
                $kImg[$value['iTargetID']][] = $value;
            }
        }
        //获取小学图片
        $pImg = Model_EvaluationImage::getImages($this->iEvaluationID, $this->config['hx']['sType']['primary']);
        if(!empty($pImg)){
            foreach($pImg as $key=>$value){
                $pImg[$value['iTargetID']][] = $value;
            }
        }

        //获取中学图片
        $mImg = Model_EvaluationImage::getImages($this->iEvaluationID, $this->config['hx']['sType']['middle']);
        if(!empty($mImg)){
            foreach($mImg as $key=>$value){
                $mImg[$value['iTargetID']][] = $value;
            }
        }
        // 其他图片
        $qtImage = Model_EvaluationImage::getImages($this->iEvaluationID, array(
            3599,
        ));
        $this->assign('qtImage', $qtImage);
        $this->assign('kInfo', $kInfo);
        $this->assign('pInfo', $pInfo);
        $this->assign('mInfo', $mInfo);
        $this->assign('qyEducate', $qyEducate);
        $this->assign('kImg', $kImg);
        $this->assign('pImg', $pImg);
        $this->assign('mImg', $mImg);
    }
    /**
     * 保存教育资源
     */
    public function saveEducateAction()
    {
        //检查点评信息数据
        $aParam = $this->checkCommonData();
        if (empty($aParam)) {
            return null;
        }

        $rs = Model_EvaluationQyEducate::saveData($aParam);

        if ($rs) {
            //检查学校信息数据
            $aParams = $this->_checkEducateData();
            if (empty($aParams)) {
                return null;
            }
            //保存学校信息
            $aParams['iEvaluationID'] = $aParam['iEvaluationID'];
            $aParams['iSchoolID'] = $rs;

            $info = Model_EvaluationQyEducateInfo::saveData($aParams);

            //保存幼儿园图片信息
            $aParams['syrySchoolImageTitle'] = $this->getParam('syrySchoolImageTitle');
            $aParams['syrySchoolImageDesc'] = $this->getParam('syrySchoolImageDesc');
            $aParams['syrySchoolImage'] = $this->getParam('syrySchoolImage');
            $aParams['syrySchoolUpImageId'] = $this->getParam('syrySchoolUpImageId');

            if (!empty($aParams['syrySchoolImage'])) {
                $arr = $aParams['syrySchoolImage'];
            } else {
                $arr = $aParams['syrySchoolImageTitle'];
            }
            if (!empty($arr)) {
                foreach ($arr as $key => $value) {
                    Model_EvaluationImage::addImagesAndDesc(
                        $aParams['iEvaluationID'],
                        $this->config['hx']['sType']['kindergarten'],
                        isset($aParams['syrySchoolImage'][$key]) ? $aParams['syrySchoolImage'][$key] : '',
                        isset($aParams['syrySchoolImageTitle'][$key]) ? $aParams['syrySchoolImageTitle'][$key] : '',
                        isset($aParams['syrySchoolImageDesc'][$key]) ? $aParams['syrySchoolImageDesc'][$key] : '',
                        isset($aParams['syrySchoolUpImageId'][$key]) ? $aParams['syrySchoolUpImageId'][$key] : '',
                        isset($info[1][$key]) ? $info[1][$key] : '');
                }
            }
            //保存小学图片
            $aParams['sxxSchoolImageTitle'] = $this->getParam('sxxSchoolImageTitle');
            $aParams['sxxSchoolImageDesc'] = $this->getParam('sxxSchoolImageDesc');
            $aParams['sxxSchoolImage'] = $this->getParam('sxxSchoolImage');
            $aParams['sxxSchoolUpImageId'] = $this->getParam('sxxSchoolUpImageId');

            if (!empty($aParams['sxxSchoolImage'])) {
                $arr = $aParams['sxxSchoolImage'];
            } else {
                $arr = $aParams['sxxSchoolImageTitle'];
            }
            if (!empty($arr)) {
                foreach ($arr as $key => $value) {
                    Model_EvaluationImage::addImagesAndDesc(
                        $aParams['iEvaluationID'],
                        $this->config['hx']['sType']['primary'],
                        isset($aParams['sxxSchoolImage'][$key]) ? $aParams['sxxSchoolImage'][$key] : '',
                        isset($aParams['sxxSchoolImageTitle'][$key]) ? $aParams['sxxSchoolImageTitle'][$key] : '',
                        isset($aParams['sxxSchoolImageDesc'][$key]) ? $aParams['sxxSchoolImageDesc'][$key] : '',
                        isset($aParams['sxxSchoolUpImageId'][$key]) ? $aParams['sxxSchoolUpImageId'][$key] : '',
                        isset($info[2][$key]) ? $info[2][$key] : '');
                }
            }
            //保存中学图片
            $aParams['szxSchoolImageTitle'] = $this->getParam('szxSchoolImageTitle');
            $aParams['szxSchoolImageDesc'] = $this->getParam('szxSchoolImageDesc');
            $aParams['szxSchoolImage'] = $this->getParam('szxSchoolImage');
            $aParams['szxSchoolUpImageId'] = $this->getParam('szxSchoolUpImageId');

            if (!empty($aParams['szxSchoolImage'])) {
                $arr = $aParams['szxSchoolImage'];
            } else {
                $arr = $aParams['szxSchoolImageTitle'];
            }
            if (!empty($arr)) {

                foreach ($arr as $key => $value) {
                   Model_EvaluationImage::addImagesAndDesc(
                        $aParams['iEvaluationID'],
                        $this->config['hx']['sType']['middle'],
                        isset($aParams['szxSchoolImage'][$key]) ? $aParams['szxSchoolImage'][$key] : '',
                        isset($aParams['szxSchoolImageTitle'][$key]) ? $aParams['szxSchoolImageTitle'][$key] : '',
                        isset($aParams['szxSchoolImageDesc'][$key]) ? $aParams['szxSchoolImageDesc'][$key] : '',
                        isset($aParams['szxSchoolUpImageId'][$key]) ? $aParams['szxSchoolUpImageId'][$key] : '',
                        isset($info[3][$key]) ? $info[3][$key] : '');
                }
            }
            // 添加其他图
            $this -> addQtImages(3599);
            // 删除图片
            Model_EvaluationImage::delImages($this->getParam('sHxImgDel'));
            return $this->showMsg('教育资源数据保存成功', true);
        } else {
            return $this->showMsg('教育资源数据保存失败', false);
        }

    }

    /**
     * 删除教育资源中的学校信息
     */
    public  function  delSchoolAction()
    {
        $schoolId = intval($this->getParam('id'));
        $imgId = $this->getParam('imgId');
        if(empty($schoolId)){
            return null;
        }
        $info = Model_EvaluationQyEducateInfo::deleteData($schoolId);
        if($info){
            Model_EvaluationImage::delImages($imgId);
            return $this->showMsg('删除成功', true);
        }else{
            return $this->showMsg('删除失败', false);
        }
    }
    /**
     * 医疗
     */
    public function medicalAction()
    {
        $this->getID();
        $this->assignCommon();
        $qyMedical = Model_EvaluationQyMedical::getQyMedicalByEID($this->iEvaluationID);
        Model_EvaluationQyMedical::formatMedicalData($qyMedical);
        /*医院
        $hospitalHtml = '';
        if (!empty($qyMedical['sHospital'])) {
            $sHospital = json_decode($qyMedical['sHospital'],true);
            $hospitalHtml =  $this->getView()->render(Model_EvaluationQyMedical::TP_PATH,array(
                'list' => $sHospital
            ));
        }*/
        if (!isset($qyMedical['sClinicCheck'])) {
            $qyMedical['sClinicCheck'] = 0;
        }
        if (!isset($qyMedical['sPharmacyCheck'])) {
            $qyMedical['sPharmacyCheck'] =0;
        }
        // 其他图片
        $qtImage = Model_EvaluationImage::getImages($this->iEvaluationID, array(
            3699,
        ));
        $this->assign('qtImage', $qtImage);
        $this->assign('medicalConfig', $this->config['medical']);
        $this->assign('qyMedical', $qyMedical);
    }

    /**
     *  保存医疗信息
     *
     */
    public function saveMedicalAction()
    {
        $aParam = $this->_checkMedicalData();

        $rs = Model_EvaluationQyMedical::saveData($aParam);
        // 添加其他图
        $this -> addQtImages(3699);
        // 删除图片
        Model_EvaluationImage::delImages($this->getParam('sHxImgDel'));
        if ($rs) {
            // 最后编辑章节
            Model_Evaluation::updateChapter($aParam['iEvaluationID'],Model_EvaluationQyMedical::CHAPTER);
            return $this->showMsg('医疗资源数据保存成功', true);
        } else {
            return $this->showMsg('医疗资源数据保存失败', false);
        }
    }
     /**
     * 保存医院
     */
    public function saveHospitalAction()
    {
        $iEvaluationID = intval($this->getParam('iEvaluationID'));
        $hospital['name'] = $this->getParam('hospitalName');
        $hospital['dp'] = $this->getParam('hospitalDp');
        $rs = Model_EvaluationQyMedical::saveHospital($iEvaluationID,$hospital);
        if ($rs) {
            $result = Model_EvaluationQyMedical::getQyMedicalByEID($iEvaluationID);
            if (!empty($result['sHospital'])) {
                $sHospital = json_decode($result['sHospital'],true);
                $hospitalHtml =  $this->getView()->render(Model_EvaluationQyMedical::TP_PATH,array(
                    'list' => $sHospital
                ));
            } else {
                $hospitalHtml = '';
            }
            return $this->showMsg($hospitalHtml, true);
        } else {
            return $this->showMsg('医院数据保存失败', false);
        }
    }

    /**
     * 获取医院
     *
     */
    public function getHospitalAction()
    {
        $eID = intval($this->getParam('eID'));
        $hID = intval($this->getParam('hID'));
        $result = Model_EvaluationQyMedical::getQyMedicalByEID($eID);
        if (empty($result) || empty($result['sHospital'])) {
            return $this->showMsg('', false);
        }
        $hospital = json_decode($result['sHospital'],true);
        foreach($hospital as $k => $v) {
            if ($hID==$k) {
                return $this->showMsg($v, true);
            }
        }
        return $this->showMsg('', false);
    }
    /**
     * 修改医院
     *
     */
    public function editHospitalAction()
    {
        $hID = intval($this->getParam('hID'));
        $iEvaluationID = intval($this->getParam('iEvaluationID'));
        $hospital['name'] = $this->getParam('hospitalName');
        $hospital['dp'] = $this->getParam('hospitalDp');
        Model_EvaluationQyMedical::editHospital($iEvaluationID,$hID,$hospital);
        $result = Model_EvaluationQyMedical::getQyMedicalByEID($iEvaluationID);
        if (!empty($result['sHospital'])) {
            $sHospital = json_decode($result['sHospital'],true);
            $hospitalHtml =  $this->getView()->render(Model_EvaluationQyMedical::TP_PATH,array(
                'list' => $sHospital
            ));
        } else {
            $hospitalHtml = '';
        }
        return $this->showMsg($hospitalHtml, true);
    }
    /**
     * 删除医院
     *
     */
    public function delHospitalAction()
    {
        $hID = intval($this->getParam('hID'));
        $iEvaluationID = intval($this->getParam('eID'));
        $rs = Model_EvaluationQyMedical::delHospital($iEvaluationID,$hID);
        return $this->showMsg('', $rs?true:false);
    }

     /**
     * 周边商圈
     *
     */
    public function businessAction()
    {
        $this->getID();
        $this->assignCommon();
        $qyBusiness = Model_EvaluationQyBusiness::getQyBusinessByEID($this->iEvaluationID);
        $this->assign('qyBusiness', $qyBusiness);
        $this->assign('imgTypeConfig', $this->config['hx']['sType']);
        // 相关图
        $qyConfigImage = Model_EvaluationImage::getImages($this->iEvaluationID, $this->filterConfig(602,607));
        $this->assign('qyConfigImage', $qyConfigImage);
        // 其他图片
        $qtImage = Model_EvaluationImage::getImages($this->iEvaluationID, array(
            3799,
        ));
        $this->assign('qtImage', $qtImage);
    }

    /**
     * 保存周边商圈
     *
     */
    public function saveBusinessAction()
    {
        $aParam = $this->_checkBusinessData();
        if(empty($aParam)) {
            return null;
        }
        $rs = Model_EvaluationQyBusiness::saveData($aParam);
        //添加周边商圈片
        Model_EvaluationImage::addBatchImages($aParam['iEvaluationID'], $this->getParam('qyConfigImage'));
        // 添加其他图
        $this -> addQtImages(3799);
        // 删除图片
        Model_EvaluationImage::delImages($this->getParam('sHxImgDel'));
        if ($rs) {
            return $this->showMsg('周边商圈数据保存成功', true);
        } else {
            return $this->showMsg('周边商圈数据保存失败', false);
        }
    }


    /**
     * 公共资源
     *
     */
    public function publicAction()
    {
        $this->getID();
        $this->assignCommon();
        $qyPublic = Model_EvaluationQyPublic::getQyPublicByEID($this->iEvaluationID);
        // 相关图
        $publicImage = Model_EvaluationImage::getImages($this->iEvaluationID,607);

        if(!empty($publicImage)){
            foreach($publicImage as $k=>$v){
                if($v['iTypeImage'] == 1) {
                        $publicImage[$k]['iTypeImage'] = '已建成';
                    }else{
                        $publicImage[$k]['iTypeImage'] = '建设中/待建';
                    }
            }
        }

        $this->assign('jsText', array(
            0 => '已建成',
            1 => '建设中/待建'
        ));
        $this->assign('publicImage', $publicImage);
        $this->assign('qyPublic', $qyPublic);
        // 其他图片
        $qtImage = Model_EvaluationImage::getImages($this->iEvaluationID, array(
            3899,
        ));
        $this->assign('qtImage', $qtImage);
    }

    /**
     * 保存公共资源
     *
     */
    public function savePublicAction()
    {
        $aParam = $this->_checkPublicData();
        if(empty($aParam)) {
            return null;
        }

        //景观实拍图建设情况
        $publicImageType = $this->getParam('publicImageTitle');
        if(!empty($publicImageType)){
            foreach($publicImageType as $key=>$value){
                if($value == '已建成'){
                    $publicImageType[$key] = 1;
                }elseif($value == '建设中/待建'){
                    $publicImageType[$key] = 2;
                }
            }
        }

        Model_EvaluationImage::addImagesPublic($aParam['iEvaluationID'],
            607,
            $this->getParam('publicImage'),
            $this->getParam('publicImageName'),
            null,
            $this->getParam('publicUpImageId'),
            $publicImageType);
        // 添加其他图
        $this -> addQtImages(3899);
        // 删除图片
        Model_EvaluationImage::delImages($this->getParam('sHxImgDel'));
        $rs = Model_EvaluationQyPublic::saveData($aParam);
        if ($rs) {
            return $this->showMsg('公共资源数据保存成功', true);
        } else {
            return $this->showMsg('公共资源数据保存失败', false);
        }

    }

    /*
     * 获取区位简介的表单数据
     *
     * */
    private function _checkPositionData()
    {
        return $this->checkCommonData();
    }
    /*
    * 获取公共资源的表单数据
    *
    * */
    private function _checkPublicData()
    {
        $aParam =  $this->checkCommonData();
        if (empty($aParam)) {
            return null;
        }
        $aParam['sPublicName'] = $this->getParam('sPublicName');
        $aParam['sDesc'] = $this->getParam('sDesc');
        $aParam['sFinished'] = intval($this->getParam('sFinished'));
        return $aParam;
    }

    /*
   * 获取教育资源的表单数据
   *
   * */
    private function  _checkEducateData()
    {
        // 幼儿园
        $aParam['sSchoolName'] = $this->getParam('sSchoolName');
        $aParam['sSchoolType'] = $this->getParam('sSchoolType');
        $aParam['sSchoolDesc'] = $this->getParam('sSchoolDesc');
        $aParam['schoolID'] = $this->getParam('schoolID');
        return $aParam;
    }

    /*
    * 获取周边商圈的表单数据
    *
    * */
    private function  _checkBusinessData()
    {
        $aParam = $this->checkCommonData();
        if (empty($aParam)) {
            return null;
        }
        $aParam['sStore'] = $this->getParam('sStore'); // 卖场
        $aParam['sBank'] = $this->getParam('sBank'); // 银行
        $aParam['sRepast'] = $this->getParam('sRepast'); // 餐饮
        $aParam['sLivingService'] = $this->getParam('sLivingService'); // 生活服务
        $aParam['sMarket'] = $this->getParam('sMarket'); // 周边菜场
        return $aParam;
    }

    /*
     * 获取医疗的表单数据
     *
     * */
    private function _checkMedicalData()
    {
        $aParam = $this->checkCommonData();
        if (empty($aParam)) {
            return null;
        }
        $sHospitalName = $this->getParam('hospitalName'); // 医院名称
        $sHospitalDp = $this->getParam('hospitalDp'); // 医院店评
        $sHospital = array();
        if (!empty($sHospitalName)) {
            foreach ($sHospitalName as $k => $item) {
                $sHospital[$k]['name'] = $item;
                $sHospital[$k]['dp'] = isset($sHospitalDp[$k]) ? $sHospitalDp[$k]:'';
            }
        }
        // 保存图片
        $sHospitalImagesTitle = $this->getParam('sHospitalImagesTitle');
        $sHospitalImages  = $this->getParam('sHospitalImages');
        $sHospitalImagesDesc  = $this->getParam('sHospitalImagesDesc');
        $sHospitalUpImageId  = $this->getParam('sHospitalUpImageId');
        $iEvaluationID = $this->getParam('iEvaluationID');
        if (!empty($sHospitalImagesTitle)) {
           foreach($sHospitalImagesTitle as $k => $item) {
               $sHospital[$k]['imgID'] = Model_EvaluationImage::addImagesAndDesc($iEvaluationID,
                   901,
                   isset($sHospitalImages[$k])?$sHospitalImages[$k]:null,
                   $sHospitalImagesTitle[$k],
                   $sHospitalImagesDesc[$k],
                   isset($sHospitalUpImageId[$k])?$sHospitalUpImageId[$k]:null);
           }
        }
        // 删除图片
        Model_EvaluationImage::delImages($this->getParam('sHxImgDel'));
       // print_r($sHospital);die;
        if (!empty($sHospital)) {
            $aParam['sHospital'] = json_encode($sHospital);
        } else {
            $aParam['sHospital'] = '';
        }
        $aParam['sClinicCheck'] = intval($this->getParam('sClinicCheck'));  // 周边无诊所 1 无 0 有
        $aParam['sPharmacyCheck'] = intval($this->getParam('sPharmacyCheck')); // 周边无药店
        // 获取诊所
        $sClinic = array();
        if (!$aParam['sClinicCheck']) {
            $sClinicName = $this->getParam('zsName'); // 诊所名称
            $sClinicType = $this->getParam('zsType'); // 诊所类型
            $sClinicDp = $this->getParam('zsDp'); // 诊所店评
            $sClinicZk = $this->getParam('zsZk'); // 专科诊所名
            if (!empty($sClinicName)) {
                foreach ($sClinicName as $k => $item) {
                    $sClinic[$k]['name'] = $item;
                    if (isset($sClinicType[$k])) {
                        $sClinic[$k]['type'] = $sClinicType[$k];
                    }
                    if (isset($sClinicZk[$k])) {
                        $sClinic[$k]['zk'] = $sClinicZk[$k];
                    }
                    // 3 代表 是否选中了专科诊所
                    if (is_array($sClinicType[$k])&&!in_array(3,$sClinicType[$k])) {
                        $sClinic[$k]['zk'] = '';
                    }
                    if (isset($sClinicDp[$k])) {
                        $sClinic[$k]['dp'] = $sClinicDp[$k];
                    }
                }
            }
        }
        if (!empty($sClinic)) {
            $aParam['sClinic'] = json_encode($sClinic);
        } else {
            $aParam['sClinic'] = '';
        }
        // 获取药店
        $sPharmacy = array();
        if (!$aParam['sPharmacyCheck']) {
            $sPharmacyName = $this->getParam('ydName'); // 药店名称
            $sPharmacyTs = $this->getParam('ydTs'); // 药店特色
            $sPharmacyDp = $this->getParam('ydDp'); // 药店店评
            if (!empty($sPharmacyName)) {
                foreach ($sPharmacyName as $k => $item) {
                    $sPharmacy[$k]['name'] = $item;
                    if (isset($sPharmacyTs[$k])) {
                        $sPharmacy[$k]['ts'] = $sPharmacyTs[$k];
                    }
                    if (isset($sPharmacyDp[$k])) {
                        $sPharmacy[$k]['dp'] = $sPharmacyDp[$k];
                    }
                }
            }
        }
        if (!empty($sPharmacy)) {
            $aParam['sPharmacy'] = json_encode($sPharmacy);
        } else {
            $aParam['sPharmacy'] = '';
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