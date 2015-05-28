<?php

/**
 * Created by PhpStorm.
 * User: ddc
 * Date: 2015/4/13
 * Time: 11:17
 */
class Controller_API_EvaluationTraffic extends Controller_Api_Evaluationbase
{

    public function actionBefore()
    {
        parent::actionBefore();
        $this->config = Model_EvaluationZxBrand::getConfig();
    }

    /**
     * 自驾出行
     * param eID 评测报告ID
     * param cID 章节ID
     */
    public function getZiJiaAction ()
    {
        $eID = $this->iEvaluationID;
        $pID = $this->iPchapter;
        $cID = $this->iCchapter;

        $aList = Model_EvaluationDriving::getDrivingByEID($eID);
        if(!empty($aList)) {
            //优势标签转换
            $aList['sGoodTag'] = Model_EvaluationTag::getChapterByIDS($aList['sGoodTag']);
            //劣势标签转换
            $aList['sBadTag'] = Model_EvaluationTag::getChapterByIDS($aList['sBadTag']);

            $imgTypeConfig = $this->config['hx']['sType'];
            $imgList = $this->getImage($eID, $imgTypeConfig);
            //图片配置
            //自驾线路图
            $aList['zjxlImage'] = isset($imgList[$imgTypeConfig['zjxl']]) ? $imgList[$imgTypeConfig['zjxl']] : '';
            //其它图片
            $aList['zjqtImage'] = isset($imgList[$imgTypeConfig['zjqt']]) ? $imgList[$imgTypeConfig['zjqt']] : '';

            $this->formatImages($aList,'Image');
            $this->groupAllImages($aList,'Image');
        }else{
            return $this->showMsg(NULL, false);
        }
        return $this->showMsg($aList, true);
    }

    /**
     * 轨交出行
     * param eID 评测报告ID
     * param cID 章节ID
     */
    public function getRailAction ()
    {
        $eID = $this->iEvaluationID;
        $pID = $this->iPchapter;
        $cID = $this->iCchapter;

        $aList = Model_EvaluationJtMetro::getApiEvaluationMetro($eID);
        if(!empty($aList)) {
            //优势标签转换
            $aList['sGoodTag'] = Model_EvaluationTag::getChapterByIDS($aList['sGoodTag']);
            //劣势标签转换
            $aList['sBadTag'] = Model_EvaluationTag::getChapterByIDS($aList['sBadTag']);
            //装修标准转换
            if ($aList['iHasMetro'] > 0) {
                $aList['iHasMetro'] = '附近有轨交';
            }
            //早晚高峰数据转换
            if(!empty($aList['aMetroInfo'])){
                foreach($aList['aMetroInfo'] as $key=>$value){
                    if(!empty($value['aMetroList'])) {
                        foreach($value['aMetroList'] as $k=>$v) {
                            if ($v['iMorningPeak'] == 0) {
                                $aList['aMetroInfo'][$key]['aMetroList'][$k]['iMorningPeak'] = '普通';
                            } elseif ($v['iMorningPeak'] == 1) {
                                $aList['aMetroInfo'][$key]['aMetroList'][$k]['iMorningPeak'] = '拥挤';
                            } elseif ($v['iMorningPeak'] == 2) {
                                $aList['aMetroInfo'][$key]['aMetroList'][$k]['iMorningPeak'] = '非常拥挤';
                            } else {
                                $aList['aMetroInfo'][$key]['aMetroList'][$k]['iMorningPeak'] = '无信息';
                            }
                            if ($v['iEveningPeak'] == 0) {
                                $aList['aMetroInfo'][$key]['aMetroList'][$k]['iEveningPeak'] = '普通';
                            } elseif ($v['iEveningPeak'] == 1) {
                                $aList['aMetroInfo'][$key]['aMetroList'][$k]['iEveningPeak'] = '拥挤';
                            } elseif ($v['iEveningPeak'] == 2) {
                                $aList['aMetroInfo'][$key]['aMetroList'][$k]['iEveningPeak'] = '非常拥挤';
                            } else {
                                $aList['aMetroInfo'][$key]['aMetroList'][$k]['iEveningPeak'] = '无信息';
                            }
                        }
                    }
                }
            }

            $imgTypeConfig = $this->config['hx']['sType'];
            $imgList = $this->getImage($eID, $imgTypeConfig);
            //图片配置
            //其它图片
            $aList['ddqtImage'] = isset($imgList[$imgTypeConfig['ddqt']]) ? $imgList[$imgTypeConfig['ddqt']] : '';
            $aList['ddqtImage'] = $this->groupImages($aList['ddqtImage']);
            $this->formatImages($aList,'Image');
            $this->formatRailImages($aList);
        }else{
            return $this->showMsg(NULL, false);
        }
        return $this->showMsg($aList, true);
    }

    public function formatRailImages(&$aList) {
        if (!empty($aList['aMetroInfo'])) {
            foreach ($aList['aMetroInfo'] as &$item) {
                if (!isset($item['images'][$item['iAutoID']])) {
                    continue;
                }
                $item['images'] = $item['images'][$item['iAutoID']][514];
                if (!empty($item['images'])) {
                    foreach ($item['images'] as &$vc) {
                        $vc['sImage'] = Model_EvaluationImage::getMobileImageUrl($vc['iCricId'], $vc['sImage'],
                            self::IMG_WIDTH, 1, 19, 4);
                    }
                }
            }
        }
    }
    /**
     * 公交出行
     * param eID 评测报告ID
     * param cID 章节ID
     */
    public function getBusAction ()
    {
        $eID = $this->iEvaluationID;
        $pID = $this->iPchapter;
        $cID = $this->iCchapter;

        $aList = Model_EvaluationBus::getBusByEID($eID);
        if(!empty($aList)) {
            //优势标签转换
            $aList['sGoodTag'] = Model_EvaluationTag::getChapterByIDS($aList['sGoodTag']);
            //劣势标签转换
            $aList['sBadTag'] = Model_EvaluationTag::getChapterByIDS($aList['sBadTag']);
            //获取公交线路信息
            $aList['busInfo'] = Model_EvaluationBusInfo::getRelatedBusById($aList['iAutoID']);
            //获取公交站点图片
            if (!empty($aList['busInfo'])) {
                foreach ($aList['busInfo'] as $k => $item) {
                    $aList['busInfo'][$k]['images'] = Model_EvaluationImage::getImagesByTargetId(
                        $item['iAutoId'],
                        $this->config['hx']['sType']['ggcx'],
                        $eID);

                    //线路解析
                    $aList['busInfo'][$k]['sInfo'] = json_decode($item['sInfo'],true);
                    if(!empty($aList['busInfo'][$k]['sInfo'])) {
                        foreach($aList['busInfo'][$k]['sInfo'] as $key=>$value) {
                            //早高峰配置
                            if ($value['iZaogaofeng'] = 1) {
                                $aList['busInfo'][$k]['sInfo'][$key]['zaogaofeng'] = '一般';
                            } elseif ($value['iZaogaofeng'] = 2) {
                                $aList['busInfo'][$k]['sInfo'][$key]['zaogaofeng'] = '拥挤';
                            } elseif ($value['iZaogaofeng'] = 3) {
                                $aList['busInfo'][$k]['sInfo'][$key]['zaogaofeng'] = '非常拥挤';
                            } else {
                                $aList['busInfo'][$k]['sInfo'][$key]['zaogaofeng'] = $value['zaogaofeng'];
                            }
                            //晚高峰配置
                            if ($value['wangaofeng'] = 1) {
                                $aList['busInfo'][$k]['sInfo'][$key]['wangaofeng'] = '一般';
                            } elseif ($value['wangaofeng'] = 2) {
                                $aList['busInfo'][$k]['sInfo'][$key]['wangaofeng'] = '拥挤';
                            } elseif ($value['wangaofeng'] = 3) {
                                $aList['busInfo'][$k]['sInfo'][$key]['wangaofeng'] = '非常拥挤';
                            } else {
                                $aList['busInfo'][$k]['sInfo'][$key]['wangaofeng'] = $value['wangaofeng'];
                            }
                        }
                    }
                }
            }
            $imgTypeConfig = $this->config['hx']['sType'];
            $imgList = $this->getImage($eID, $imgTypeConfig);

            //图片配置
            //其它图片
            $aList['gjqtImage'] = isset($imgList[$imgTypeConfig['gjqt']]) ? $imgList[$imgTypeConfig['gjqt']] : '';
            $aList['gjqtImage'] = $this->groupImages($aList['gjqtImage']);
            $this->formatImages($aList,'Image');
            $this->formatBusImages($aList);
        }else{
            return $this->showMsg(NULL, false);
        }
        return $this->showMsg($aList, true);
    }

    public function formatBusImages(&$aList) {
        if (!empty($aList['busInfo'])) {
            foreach ($aList['busInfo'] as &$item) {
                if (!isset($item['images'][$item['iAutoId']])) {
                    continue;
                }
                $item['images'] = $item['images'][$item['iAutoId']][515];
                if (!empty($item['images'])) {
                    foreach ($item['images'] as &$vc) {
                        $vc['sImage'] = Model_EvaluationImage::getMobileImageUrl($vc['iCricId'], $vc['sImage'],
                            self::IMG_WIDTH, 1, 19, 4);
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