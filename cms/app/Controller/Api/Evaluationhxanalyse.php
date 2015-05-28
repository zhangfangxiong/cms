<?php

/**
 * 评测报告户型分析接口
 * User: cjj
 * Date: 2015/1/27
 * Time: 11:17
 */
class Controller_API_Evaluationhxanalyse extends Controller_Api_Evaluationbase
{

    /**
     * 整体评价
     */
    public function indexAction()
    {
        $config = Model_EvaluationHxWhole::getConfig();
        // 整体评价信息
        $hxWhole = Model_EvaluationHxWhole::getHxWholeByEID($this->iEvaluationID);
        if(!empty($hxWhole)) {
            // 户型基本信息(面积，南边朝向等数据)
            $hxWhole['InfoList']  = Model_EvaluationHxType::getHxTypeList($this->iEvaluationID);
            // 优势标签
            if(!empty($this->iPchapter) && !empty($this->iCchapter)) {
                $hxWhole['sGoodTag'] = Model_EvaluationTag::getChapterByIDS($hxWhole['sGoodTag']);
                // 劣势标签
                $hxWhole['sBadTag'] = Model_EvaluationTag::getChapterByIDS($hxWhole['sBadTag']);
            }else{
                $hxWhole['sGoodTag'] = null;
                $hxWhole['sBadTag'] = null;
            }
            $iType = $config['hx']['sType']['ztqt'];
            $imgList = Model_EvaluationImage::getImages($this->iEvaluationID, $iType);
            //图片配置
             //其它图片
            if (empty($imgList)) {
                $hxWhole['ztqtImage'] = '';
            } else {
                $hxWhole['ztqtImage'] = $imgList;
            }
            $this->formatImages($hxWhole,'Image');
            $this->groupAllImages($hxWhole,'Image');
        }else{
            return $this->showMsg(NULL, false);
        }
        return $this->showMsg($hxWhole, true);
    }

    /**
     * 户型分析
     */
    public function hxAction()
    {
        // 户型分析信息
        $HxAnalyse = Model_EvaluationHxAnalyse::getHxAnalyseByEID($this->iEvaluationID);
        if (!empty($HxAnalyse)) {
            // 户型信息
            $HxType  = Model_EvaluationHxType::getHxTypeList($this->iEvaluationID);
            $HxAnalyse['hxInfo'] = $this->formatHxType($HxType);
            $config = Model_EvaluationHxWhole::getConfig();
            // 优势标签
            $HxAnalyse['sGoodTag'] =  Model_EvaluationTag::getChapterByIDS($HxAnalyse['sGoodTag']);
            // 劣势标签
            $HxAnalyse['sBadTag'] =  Model_EvaluationTag::getChapterByIDS($HxAnalyse['sBadTag']);
            // 户型相关图
            $HxAnalyse['hxImgs'] = array();
            if (!empty($HxAnalyse['sHuXingID'])) {
                $HxAnalyse['sHuXingID'] = explode(',',$HxAnalyse['sHuXingID']);
                $HxAnalyse['hxImgs'] = Model_EvaluationImage::getImagesByTargetId($HxAnalyse['sHuXingID'],array(
                    $config['hx']['sType']['hd'],
                    $config['hx']['sType']['sp'],
                ),$this->iEvaluationID);
            }
            // 点评图 其他图片
            $HxAnalyse['hxqtImage'] = Model_EvaluationImage::getImages($this->iEvaluationID, array(
                $config['hx']['sType']['dp'],
            ));
            $HxAnalyse['hxqtImage'] = $this->groupImages($HxAnalyse['hxqtImage']);
            $this->formatImages($HxAnalyse,'Image');
            $this -> formatHxAnalyse($HxAnalyse);
            return $this->showMsg($HxAnalyse, true);
        } else{
            return $this->showMsg(NULL, false);
        }

    }


    public function formatHxAnalyse(&$HxAnalyse)
    {
        if (!empty($HxAnalyse)) {
            if (!empty($HxAnalyse['hxInfo'])) {
                foreach ($HxAnalyse['hxInfo'] as $kc => $vc) {
                    if (!isset($HxAnalyse['hxImgs'][$kc])) {
                        unset($HxAnalyse['hxInfo'][$kc]);
                    }
                }
            }
            if (!empty($HxAnalyse['hxImgs'])) {
                foreach ($HxAnalyse['hxImgs'] as $k => &$item) {
                    if (empty($item)) {
                        continue;
                    }
                    foreach ($item as $type=> &$images) {
                        if (empty($images)) {
                            continue;
                        }

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
                        foreach ($images as $kc => $vc) {
                            $iGroup[] = $vc['iGroup'];
                        }
                        array_multisort($iGroup,SORT_ASC,$images);
                        */
                        $iGroup = array();
                        foreach ($images as $kc=> $arr) {
                            /* 一个图片组 只用一个描述
                            if (in_array($images[$kc]['iGroup'],$iGroup)) {
                                if (isset($images[$kc-1])) {
                                    $images[$kc-1]['sDesc'] = '';
                                }
                            }
                            $iGroup[] = $images[$kc]['iGroup'];*/
                            // 图片名称 和 地址修改
                            if ($arr['iTypeID'] == 101) {
                                $images[$kc]['sTitle'] = '户型图-'.$images[$kc]['sTitle'];
                            } else {
                                $images[$kc]['sTitle'] = '实拍图-'.$images[$kc]['sTitle'];
                            }
                            $images[$kc]['sImage'] = Model_EvaluationImage::getMobileImageUrl($images[$kc]['iCricId'],$images[$kc]['sImage'],self::IMG_WIDTH,1,19,4);
                        }
                    }
                }
                // 把 类型那一维 去掉
                foreach ($HxAnalyse['hxImgs'] as $k => &$item) {
                    if (empty($item)) {
                        continue;
                    }
                    $tempArr = array();
                    foreach ($item as $type=> &$images) {
                        if (empty($images)) {
                            continue;
                        }
                        foreach ($images as $kc=> $arr) {
                            $tempArr[] = $arr;
                        }
                    }
                    $HxAnalyse['hxImgs'][$k] = $tempArr;
                }

            }
        }
    }
    /**
     * 数据重构

    public function formatHxAnalyse(&$HxAnalyse) {
        if (!empty($HxAnalyse)) {
            if (!empty($HxAnalyse['hxInfo'])) {
                foreach ($HxAnalyse['hxInfo'] as $kc => $vc) {
                    if (!isset($HxAnalyse['hxImgs'][$kc])) {
                        unset($HxAnalyse['hxInfo'][$kc]);
                    }
                }
            }
            if (!empty($HxAnalyse['hxImgs'])) {
                foreach ($HxAnalyse['hxImgs'] as $k => &$v) {
                    if (!empty($v)) {
                        foreach($v as $kc => &$item) {
                            if ($item['iTypeID'] == 101) {
                                $item['sTitle'] = '户型图-'.$item['sTitle'];
                            } else {
                                $item['sTitle'] = '实拍图-'.$item['sTitle'];
                            }
                            $item['sImage'] = Model_EvaluationImage::getMobileImageUrl($item['iCricId'],$item['sImage'],self::IMG_WIDTH,1,4,4);
                        }
                    }
                }
            }
        }
    }
    */

    private  function formatHxType($HxType) {
        $newArr = array();
        if (!empty($HxType)) {
            foreach($HxType as $k => $val) {
                $temp['iAutoID'] = $val['iAutoID'];
                $temp['sHuXingID'] = $val['sHuXingID'];
                $temp['iRoomNum'] = $val['iRoomNum'];
                $temp['iHallNum'] = $val['iHallNum'];
                $temp['iToiletNum'] = $val['iToiletNum'];

                $newArr[$val['iAutoID']] = $temp;
            }
        }
        return $newArr;
    }
}