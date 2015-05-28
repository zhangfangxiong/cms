<?php

/**
 * Created by PhpStorm.
 * User: ddc
 * Date: 2015/4/10
 * Time: 11:17
 */
class Controller_API_Evaluationzxstandard extends Controller_Api_Evaluationbase
{

    public function actionBefore()
    {
        parent::actionBefore();
        $this->config = Model_EvaluationZxBrand::getConfig();
    }

    /**
     * 品牌配置
     * param eID 评测报告ID
     * param cID 章节ID
     */
    public function getBrandAction ()
    {
        $eID = $this->iEvaluationID;
        $pID = $this->iPchapter;
        $cID = $this->iCchapter;

        $aList = Model_EvaluationZxBrand::getBrandByEID($eID);
        if(!empty($aList)) {
            $aList['sGoodTag'] = Model_EvaluationTag::getChapterByIDS($aList['sGoodTag']);
            $aList['sBadTag'] = null; // 没有劣势设为空
            $imgTypeConfig = $this->config['hx']['sType'];
            $imgList = $this->getImage($eID, $imgTypeConfig);
            //图片配置
            //厨房台面图
            $aList['cftmImage'] = isset($imgList[$imgTypeConfig['cftm']]) ? $imgList[$imgTypeConfig['cftm']] : '';
            //厨房橱柜
            $aList['cfcgImage'] = isset($imgList[$imgTypeConfig['cfcg']]) ? $imgList[$imgTypeConfig['cfcg']] : '';
            //厨房水槽
            $aList['cfscImage'] = isset($imgList[$imgTypeConfig['cfsc']]) ? $imgList[$imgTypeConfig['cfsc']] : '';
            //厨房水龙头
            $aList['cfltImage'] = isset($imgList[$imgTypeConfig['cflt']]) ? $imgList[$imgTypeConfig['cflt']] : '';
            //厨房抽油烟机
            $aList['cfyjImage'] = isset($imgList[$imgTypeConfig['cfyj']]) ? $imgList[$imgTypeConfig['cfyj']] : '';
            //厨房抽燃气灶
            $aList['cfrqImage'] = isset($imgList[$imgTypeConfig['cfrq']]) ? $imgList[$imgTypeConfig['cfrq']] : '';
            //厨房抽消毒柜
            $aList['cfxdImage'] = isset($imgList[$imgTypeConfig['cfxd']]) ? $imgList[$imgTypeConfig['cfxd']] : '';
            //厨房抽烤箱
            $aList['cfkxImage'] = isset($imgList[$imgTypeConfig['cfkx']]) ? $imgList[$imgTypeConfig['cfkx']] : '';
            //厨房抽蒸炉
            $aList['cfzlImage'] = isset($imgList[$imgTypeConfig['cfzl']]) ? $imgList[$imgTypeConfig['cfzl']] : '';
            //厨房抽冰箱
            $aList['cfbxImage'] = isset($imgList[$imgTypeConfig['cfbx']]) ? $imgList[$imgTypeConfig['cfbx']] : '';
            //厨房抽墙砖
            $aList['cfqzImage'] = isset($imgList[$imgTypeConfig['cfqz']]) ? $imgList[$imgTypeConfig['cfqz']] : '';
            //厨房抽地砖
            $aList['cfdzImage'] = isset($imgList[$imgTypeConfig['cfdz']]) ? $imgList[$imgTypeConfig['cfdz']] : '';
            //卫浴坐便器
            $aList['wybqImage'] = isset($imgList[$imgTypeConfig['wybq']]) ? $imgList[$imgTypeConfig['wybq']] : '';
            //卫浴坐台盆
            $aList['wytpImage'] = isset($imgList[$imgTypeConfig['wytp']]) ? $imgList[$imgTypeConfig['wytp']] : '';
            //卫浴坐浴缸
            $aList['wyygImage'] = isset($imgList[$imgTypeConfig['wyyg']]) ? $imgList[$imgTypeConfig['wyyg']] : '';
            //卫浴坐龙头
            $aList['wyltImage'] = isset($imgList[$imgTypeConfig['wylt']]) ? $imgList[$imgTypeConfig['wylt']] : '';
            //卫浴坐墙面
            $aList['wyqmImage'] = isset($imgList[$imgTypeConfig['wyqm']]) ? $imgList[$imgTypeConfig['wyqm']] : '';
            //卫浴坐地砖
            $aList['wydzImage'] = isset($imgList[$imgTypeConfig['wydz']]) ? $imgList[$imgTypeConfig['wydz']] : '';
            //卫浴坐浴霸
            $aList['wyybImage'] = isset($imgList[$imgTypeConfig['wyyb']]) ? $imgList[$imgTypeConfig['wyyb']] : '';
            //卫浴柜镜柜组合
            $aList['wyzhImage'] = isset($imgList[$imgTypeConfig['wyzh']]) ? $imgList[$imgTypeConfig['wyzh']] : '';
            //卧室门
            $aList['wsmImage'] = isset($imgList[$imgTypeConfig['wsm']]) ? $imgList[$imgTypeConfig['wsm']] : '';
            //卧室地板
            $aList['wsdbImage'] = isset($imgList[$imgTypeConfig['wsdb']]) ? $imgList[$imgTypeConfig['wsdb']] : '';
            //卧室墙面
            $aList['wsqmImage'] = isset($imgList[$imgTypeConfig['wsqm']]) ? $imgList[$imgTypeConfig['wsqm']] : '';
            //卧室吊灯
            $aList['wsddImage'] = isset($imgList[$imgTypeConfig['wsdd']]) ? $imgList[$imgTypeConfig['wsdd']] : '';
            //全屋新风系统
            $aList['qwxfImage'] = isset($imgList[$imgTypeConfig['qwxf']]) ? $imgList[$imgTypeConfig['qwxf']] : '';
            //全屋新空调
            $aList['qwktImage'] = isset($imgList[$imgTypeConfig['qwkt']]) ? $imgList[$imgTypeConfig['qwkt']] : '';
            //全屋新地暖
            $aList['qwdnImage'] = isset($imgList[$imgTypeConfig['qwdn']]) ? $imgList[$imgTypeConfig['qwdn']] : '';
            //全屋新红外布防系统
            $aList['qwhwImage'] = isset($imgList[$imgTypeConfig['qwhw']]) ? $imgList[$imgTypeConfig['qwhw']] : '';
            //其它图片
            $aList['ppqtImage'] = isset($imgList[$imgTypeConfig['ppqt']]) ? $imgList[$imgTypeConfig['ppqt']] : '';

            if ($aList['sToiletYbLx'] == 1) {
                $aList['sToiletYbLx'] = '灯暖';

            } else if ($aList['sToiletYbLx'] == 2) {
                $aList['sToiletYbLx'] = '风暖';

            } else {
                $aList['sToiletYbLx'] = '';
            }

            if ($aList['iHouseGq'] == 1) {
                $aList['iHouseGq'] = '光纤到楼';

            } else if ($aList['iHouseGq'] == 2) {
                $aList['iHouseGq'] = '光纤到户';

            } else {
                $aList['iHouseGq'] = '';
            }

            $this->formatImages($aList,'Image');
            $this->groupAllImages($aList,'Image');
        }else{
            return $this->showMsg(NULL, false);
        }
        return $this->showMsg($aList, true);
    }

    /**
     * 装修分析
     * param eID 评测报告ID
     * param cID 章节ID
     */
    public function getAnalysisAction ()
    {
        $eID = $this->iEvaluationID;
        $pID = $this->iPchapter;
        $cID = $this->iCchapter;

        $aList = Model_EvaluationZxAnalysis::getZxAnalysisByEID($eID);
        if(!empty($aList)) {
            //优势标签转换
            $aList['sGoodTag'] = Model_EvaluationTag::getChapterByIDS($aList['sGoodTag']);
            //劣势标签转换
            $aList['sBadTag'] = Model_EvaluationTag::getChapterByIDS($aList['sBadTag']);
            //装修标准转换
            if ($aList['iCostRate'] > 0) {
                $aList['iCostRate'] = $this->config['standard']['xjb'][$aList['iCostRate']];
            } else {
                $aList['iCostRate'] = '';
            }
            $aList['iPrice'] = $aList['iPrice'] == '0.00' ? '-' : $aList['iPrice'];
            $aList['iCostPercent'] = $aList['iCostPercent'] == '0.00' ? '-' : $aList['iCostPercent'];

            //获取图片配置
            $imgTypeConfig = $this->config['hx']['sType'];
            $imgList = $this->getImage($eID, $imgTypeConfig);
            //装修描述图
            $aList['zxmsImage'] = isset($imgList[$imgTypeConfig['zxms']]) ? $imgList[$imgTypeConfig['zxms']] : array();
            //装修分析其它图片
            $aList['zxqtImage'] = isset($imgList[$imgTypeConfig['zxqt']]) ? $imgList[$imgTypeConfig['zxqt']] : array();
            $this->formatImages($aList,'Image');
            $this->groupAllImages($aList,'Image');

        }else{
            return $this->showMsg(NULL, false);
        }
        return $this->showMsg($aList, true);
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