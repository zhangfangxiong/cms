<?php

/**
 * Created by PhpStorm.
 * User: ddc
 * Date: 2015/4/13
 * Time: 11:17
 */
class Controller_API_EvaluationRegion extends Controller_Api_Evaluationbase
{

    public function actionBefore()
    {
        parent::actionBefore();
        $this->config = Model_EvaluationQyMedical::getConfig();
    }

    /**
     * 区位简介
     * param eID 评测报告ID
     * param cID 章节ID
     */
    public function getRegionAction ()
    {
        $eID = $this->iEvaluationID;
        $pID = $this->iPchapter;
        $cID = $this->iCchapter;

        $aList = Model_EvaluationQyPosition::getQyPositionByEID($eID);
        if(!empty($aList)) {
            //优势标签转换
            $aList['sGoodTag'] = Model_EvaluationTag::getChapterByIDS($aList['sGoodTag']);
            //劣势标签转换
            $aList['sBadTag'] = Model_EvaluationTag::getChapterByIDS($aList['sBadTag']);
            $imgTypeConfig = $this->config['hx']['sType'];
            $imgList = $this->getImage($eID, $imgTypeConfig);
            //图片配置
            //区位图
            $aList['qytpImage'] = isset($imgList[$imgTypeConfig['qytp']]) ? $imgList[$imgTypeConfig['qytp']] : '';
            //其它图片
            $aList['qwqtImage'] = isset($imgList[$imgTypeConfig['qwqt']]) ? $imgList[$imgTypeConfig['qwqt']] : '';
            $this->formatImages($aList,'Image');
            $this->groupAllImages($aList,'Image');

        }else{
            return $this->showMsg(NULL, false);
        }
        return $this->showMsg($aList, true);
    }

    /**
     * 教育资源
     * param eID 评测报告ID
     * param cID 章节ID
     */
    public function getEducateAction ()
    {
        $eID = $this->iEvaluationID;
        $pID = $this->iPchapter;
        $cID = $this->iCchapter;

        $aList = Model_EvaluationQyEducate::getQyEducateByEID($eID);
        if(!empty($aList)) {
            //优势标签转换
            $aList['sGoodTag'] = Model_EvaluationTag::getChapterByIDS($aList['sGoodTag']);
            //劣势标签转换
            $aList['sBadTag'] = Model_EvaluationTag::getChapterByIDS($aList['sBadTag']);
            //获取幼儿园信息
            $aList['kInfo'] = Model_EvaluationQyEducateInfo::getQyEducateInfoByEID($aList['iAutoID'], 1);
            //获取小学信息
            $aList['pInfo'] = Model_EvaluationQyEducateInfo::getQyEducateInfoByEID($aList['iAutoID'], 2);
            //获取中学信息
            $aList['mInfo'] = Model_EvaluationQyEducateInfo::getQyEducateInfoByEID($aList['iAutoID'], 3);
            //获取幼儿园图片
            if (!empty($aList['kInfo'])) {
                foreach ($aList['kInfo'] as $key => $value) {
                    $aList['kInfo'][$key]['images'] = Model_EvaluationImage::getImagesByTargetId($value['iAutoID'],
                        $this->config['hx']['sType']['kindergarten'], $eID);
                    $aList['kInfo'][$key]['images'] = $this->formatEducateImages($aList['kInfo'][$key]['images'],$cID);
                }

            }
            //获取小学图片
            if (!empty($aList['pInfo'])) {
                foreach ($aList['pInfo'] as $key => $value) {
                    $aList['pInfo'][$key]['images'] = Model_EvaluationImage::getImagesByTargetId($value['iAutoID'], $this->config['hx']['sType']['primary'], $eID);
                    $aList['pInfo'][$key]['images'] = $this->formatEducateImages($aList['pInfo'][$key]['images'],$cID);
                }

            }
            //获取中学图片
            if (!empty($aList['mInfo'])) {
                foreach ($aList['mInfo'] as $key => $value) {
                    $aList['mInfo'][$key]['images'] = Model_EvaluationImage::getImagesByTargetId($value['iAutoID'], $this->config['hx']['sType']['middle'], $eID);
                    $aList['mInfo'][$key]['images'] = $this->formatEducateImages($aList['mInfo'][$key]['images'],$cID);
                }
            }
            //其它图片
            $imgTypeConfig = $this->config['hx']['sType'];
            $imgList = $this->getImage($eID, $imgTypeConfig);
            //图片配置
            //其它图片
            $aList['jyqtImage'] = isset($imgList[$imgTypeConfig['jyqt']]) ? $imgList[$imgTypeConfig['jyqt']] : '';
            $aList['jyqtImage'] = $this->groupImages($aList['jyqtImage']);
            $this->formatImages($aList,'Image');

        }else{
            return $this->showMsg(NULL, false);
        }
        return $this->showMsg($aList, true);
    }

    /**
     * 医疗资源
     * param eID 评测报告ID
     * param cID 章节ID
     */
    public function getMedicalAction ()
    {
        $eID = $this->iEvaluationID;
        $pID = $this->iPchapter;
        $cID = $this->iCchapter;

        $aList = Model_EvaluationQyMedical::getQyMedicalByEID($eID);
        if(!empty($aList)) {
            $this->formatMedicalData($aList);
            //优势标签转换
            $aList['sGoodTag'] = Model_EvaluationTag::getChapterByIDS($aList['sGoodTag']);
            //劣势标签转换
            $aList['sBadTag'] = Model_EvaluationTag::getChapterByIDS($aList['sBadTag']);
            //诊所数据转换
            if(!empty($aList['sClinic'])){
                foreach($aList['sClinic'] as $k=>$v){
                    if (isset($v['type'])&&!empty($v['type'])) {
                        $aList['sClinic'][$k]['type'] = $this->config['medical']['clinicType'][$v['type'][0]];
                    } else {
                        $aList['sClinic'][$k]['type'] = '';
                    }

                }
            }
            //药房数据转换
            if(!empty($aList['sPharmacy'])){
                foreach($aList['sPharmacy'] as $k=>$v){
                    if(!empty($v['ts'])) {
                        foreach($v['ts'] as $key=>$value) {
                            $aList['sPharmacy'][$k]['ts'][$key] = $this->config['medical']['pharmacyTs'][$value];
                        }
                    }
                }
            }
            //其它图片
            $imgTypeConfig = $this->config['hx']['sType'];
            $imgList = $this->getImage($eID, $imgTypeConfig);
            //图片配置
            //其它图片
            $aList['ylqtImage'] = isset($imgList[$imgTypeConfig['ylqt']]) ? $imgList[$imgTypeConfig['ylqt']] : '';
            $aList['ylqtImage'] = $this->groupImages($aList['ylqtImage']);
            $this->formatImages($aList,'Image');
            $this -> formatHospitalImages($aList);
        }else{
            return $this->showMsg(NULL, false);
        }
        return $this->showMsg($aList, true);
    }
    public  function formatMedicalData(&$qyMedical)
    {
        if (!empty($qyMedical['sHospital'])) {
            $qyMedical['sHospital'] = json_decode($qyMedical['sHospital'],true);
            // 取出图片
            foreach($qyMedical['sHospital'] as $k => $item) {
                if (isset($item['imgID']) && !empty($item['imgID'])) {
                    $qyMedical['sHospital'][$k]['images'] = Model_EvaluationImage::getImagesByID($item['imgID']);
                } else {
                    $qyMedical['sHospital'][$k]['images'] = null;
                }
            }
        } else {
            $qyMedical['sHospital'] = null;
        }
        if (!empty($qyMedical['sClinic'])) {
            $qyMedical['sClinic'] = json_decode($qyMedical['sClinic'],true);
        } else {
            $qyMedical['sClinic'] = null;
        }
        if (!empty($qyMedical['sPharmacy'])) {
            $qyMedical['sPharmacy'] = json_decode($qyMedical['sPharmacy'],true);
        } else {
            $qyMedical['sPharmacy'] = null;
        }
    }

    /**
     * 周边商圈
     * param eID 评测报告ID
     * param cID 章节ID
     */
    public function getBusinessAction ()
    {
        $eID = $this->iEvaluationID;
        $pID = $this->iPchapter;
        $cID = $this->iCchapter;

        $aList = Model_EvaluationQyBusiness::getQyBusinessByEID($eID);
        if(!empty($aList)) {
            //优势标签转换
            $aList['sGoodTag'] = Model_EvaluationTag::getChapterByIDS($aList['sGoodTag']);
            //劣势标签转换
            $aList['sBadTag'] = Model_EvaluationTag::getChapterByIDS($aList['sBadTag']);
            $imgTypeConfig = $this->config['hx']['sType'];
            $imgList = $this->getImage($eID, $imgTypeConfig);
            //图片配置
            //周边卖场
            $aList['zbmcImage'] = isset($imgList[$imgTypeConfig['zbmc']]) ? $imgList[$imgTypeConfig['zbmc']] : '';
            //周边银行图
            $aList['zbyhImage'] = isset($imgList[$imgTypeConfig['zbyh']]) ? $imgList[$imgTypeConfig['zbyh']] : '';
            //周边餐饮图
            $aList['zbcyImage'] = isset($imgList[$imgTypeConfig['zbcy']]) ? $imgList[$imgTypeConfig['zbcy']] : '';
            //周边生活服务
            $aList['zbfwImage'] = isset($imgList[$imgTypeConfig['zbfw']]) ? $imgList[$imgTypeConfig['zbfw']] : '';
            //周边菜场图
            $aList['zbccImage'] = isset($imgList[$imgTypeConfig['zbcc']]) ? $imgList[$imgTypeConfig['zbcc']] : '';
            //其它图片
            $aList['sqqtImage'] = isset($imgList[$imgTypeConfig['sqqt']]) ? $imgList[$imgTypeConfig['sqqt']] : '';
            $aList['sqqtImage'] = $this->groupImages($aList['sqqtImage']);

            $this->formatImages($aList,'Image');
        }else{
            return $this->showMsg(NULL, false);
        }
        return $this->showMsg($aList, true);
    }

    public function formatHospitalImages(&$aList)
    {
            if (!empty($aList['sHospital'])) {
                foreach($aList['sHospital'] as $k => &$item) {
                    if (!empty($item['images'])) {
                        foreach($item['images'] as $kc => &$vc) {
                            $vc['sImage'] = Model_EvaluationImage::getMobileImageUrl($vc['iCricId'], $vc['sImage'],
                                self::IMG_WIDTH, 1, 19, 4);
                        }
                    }

                }
            }
    }
    /**
     * 公共资源
     * param eID 评测报告ID
     * param cID 章节ID
     */
    public function getPublicAction ()
    {
        $eID = $this->iEvaluationID;
        $pID = $this->iPchapter;
        $cID = $this->iCchapter;

        $aList = Model_EvaluationQyPublic::getQyPublicByEID($eID);
        if(!empty($aList)) {
            //优势标签转换
            $aList['sGoodTag'] = Model_EvaluationTag::getChapterByIDS($aList['sGoodTag']);
            $aList['sBadTag'] = null; // 没有劣势 设为空
            $imgTypeConfig = $this->config['hx']['sType'];
            $imgList = $this->getImage($eID, $imgTypeConfig);

            //图片配置
            //景观实拍图
            $aList['qyjgImage'] = isset($imgList[$imgTypeConfig['qyjg']]) ? $imgList[$imgTypeConfig['qyjg']] : '';
            if(!empty($aList['qyjgImage'])){
                foreach($aList['qyjgImage'] as $key=>$value){
                    if($value['iTypeImage'] == 1) {
                            $aList['qyjgImage'][$key]['iTypeImage'] = '已建成';
                        }else{
                            $aList['qyjgImage'][$key]['iTypeImage'] = '建设中/待建';
                    }
                }
            }
            //其它图片
            $aList['zyqtImage'] = isset($imgList[$imgTypeConfig['zyqt']]) ? $imgList[$imgTypeConfig['zyqt']] : '';
            $this->formatImages($aList,'Image');
            $this->groupAllImages($aList,'Image');
        }else{
            return $this->showMsg(NULL, false);
        }
        return $this->showMsg($aList, true);
    }

    /**
     * 处理显示图片
     */
    protected function formatEducateImages($data,$cID)
    {
        if(!empty($data)){
            foreach($data as $key=>$value){
                foreach($value as $ke=>$va){
                    foreach($va as $k=>$v) {
                        unset($data[$key]);
                        $v['sImage'] = Model_EvaluationImage::getMobileImageUrl($v['iCricId'], $v['sImage'],
                            self::IMG_WIDTH, 1, 19, 4);
                        $data[$k] = $v;
                    }
                }
            }
            return $data;
        }else{
            return array();
        }
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