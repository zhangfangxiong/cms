<?php

/**
 * Created by PhpStorm.
 * User: ddc
 * Date: 2015/4/10
 * Time: 11:17
 */
class Controller_API_Evaluationbadfactor extends Controller_Api_Evaluationbase
{

    public function actionBefore()
    {
        parent::actionBefore();
        $this->config = Model_EvaluationZxBrand::getConfig();
    }

    /**
     * 社区内部
     * param eID 评测报告ID
     * param cID 章节ID
     */
    public function getBadfactorAction ()
    {
        $eID = $this->iEvaluationID;
        $pID = $this->iPchapter;
        $cID = $this->iCchapter;

        $aList = Model_EvaluationBlInside::getBlInsideByEID($eID);

        if(!empty($aList)) {

            //优势标签转换
            $aList['sBadTag'] = Model_EvaluationTag::getChapterByIDS($aList['sBadTag']);
            $aList['sGoodTag'] = null; // 没有设为空
            //不利因素
            if (!empty( $aList['sGdblys'])) {
                $aList['sGdblys'] = json_decode($aList['sGdblys'],true); // 固定的不利因素
                $aList['sGdblys'] = Model_EvaluationBlInside::convertLowerCase($aList['sGdblys']);
                foreach($this->config['hx']['yxy'] as $key=>$value){
                        if($aList['sGdblys'][$key]['checked'] == 1) {
                            $aList['sGdblys'][$key]['ysy'] = $value[1];
                            $aList['sGdblys'][$key]['blys'] = $value[2];
                        }
                }
            }
            if (!empty($aList['sDtblys'])) { // 新增的不利因素
                $aList['sDtblys'] = json_decode($aList['sDtblys'],true);
            }

            $imgTypeConfig = $this->config['hx']['sType'];
            $imgList = $this->getImage($eID, $imgTypeConfig);
            //图片配置
            //不利因素分布图
            $aList['blfbImage'] = isset($imgList[$imgTypeConfig['blfb']]) ? $imgList[$imgTypeConfig['blfb']] : '';
            //其它图片
            $aList['nbqtImage'] = isset($imgList[$imgTypeConfig['nbqt']]) ? $imgList[$imgTypeConfig['nbqt']] : '';
            unset($aList['sImage']);
            $this->formatImages($aList,'Image');
            $this->groupAllImages($aList,'Image');
        }else{
            return $this->showMsg(NULL, false);
        }

        return $this->showMsg($aList, true);
    }

    /**
     * 社区外部
     * param eID 评测报告ID
     * param cID 章节ID
     */
    public function getOutSideAction ()
    {
        $eID = $this->iEvaluationID;
        $pID = $this->iPchapter;
        $cID = $this->iCchapter;
        $aList = Model_EvaluationBlOutside::getBlOutsideByEID($eID);
        if(!empty($aList)) {
            //获取图片
            if (!empty($aList['sBadTag'])) {
                $aList['badTagImgs'] = Model_EvaluationImage::getImagesArrByTargetId($aList['sBadTag'],array(
                    $this->config['hx']['sType']['blsp'],
                ),$eID);
            } else {
                $aList['badTagImgs'] = null;
            }
             //劣势标签转换
            $aList['sBadTag'] = Model_EvaluationTag::getChapterByIDS($aList['sBadTag']);
            $aList['sGoodTag'] = null; // 没有设为空
            $iType = $this->config['hx']['sType']['wbqt'];
            $imgList = $this->getImage($eID, $iType);
            //图片配置
            //其它图片
            $aList['wbqtImage'] = $imgList;
            $aList['wbqtImage'] = $this->groupImages($aList['wbqtImage']);
            $this->formatImages($aList,'Image');
            $this->formatOutSideInfo($aList);
        }else{
            return $this->showMsg(NULL, false);
        }
        return $this->showMsg($aList, true);
    }
    /**
     * 数据重构
     */
    public function formatOutSideInfo(&$OutSideInfo) {
        if (!empty($OutSideInfo)) {
            if (!empty($OutSideInfo['badTagImgs'])) {
                foreach ($OutSideInfo['badTagImgs'] as $k => &$images) {
                    if (!empty($images)) {
                        // 一个图片组 只用一个描述
                        $iGroup = array();
                        foreach ($images as $kc=> $arr) {
                            $iGroup[$arr['iGroup']][] = $kc;
                        }
                        foreach ($iGroup as $itemChild) {
                            $count = count($itemChild);
                            if ($count > 1) {
                                unset($itemChild[$count-1]); // 最后描述一个保留
                                foreach($itemChild as  $vc) {
                                    $images[$vc]['sDesc'] = '';
                                }
                            }
                        }
                        /*
                       $iGroup = array();
                        foreach ($images as $field => $v) {
                            $iGroup[] = $v['iGroup'];
                        }
                        array_multisort($iGroup,SORT_ASC,$images);
                        */
                        foreach($images as $kc => &$item) {
                            /*
                            if (in_array($item['iGroup'],$iGroup)) {
                                if (isset($images[$kc-1])) {
                                    $images[$kc-1]['sDesc'] = '';
                                }
                            }
                            $iGroup[] = $images[$kc]['iGroup'];
                            */
                            $item['sImage'] = Model_EvaluationImage::getMobileImageUrl($item['iCricId'],$item['sImage'],self::IMG_WIDTH,1,19,4);
                        }
                    }
                }
            }
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