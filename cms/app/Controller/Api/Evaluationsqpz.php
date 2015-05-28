<?php

/**
 * Created by PhpStorm.
 * User: ddc
 * Date: 2015/4/10
 * Time: 11:17
 */
class Controller_API_Evaluationsqpz extends Controller_Api_Evaluationbase
{

    public function actionBefore()
    {
        parent::actionBefore();
        $this->config = Model_EvaluationZxBrand::getConfig();
    }

    /**
     * 整体规划
     * param eID 评测报告ID
     * param cID 章节ID
     */
    public function getWholeAction ()
    {
        $eID = $this->iEvaluationID;
        $pID = $this->iPchapter;
        $cID = $this->iCchapter;

        $aList = Model_EvaluationSqWhole::getSqWholeByEID($eID);
        if(!empty($aList)) {
            //优势标签转换
            $aList['sGoodTag'] = Model_EvaluationTag::getChapterByIDS($aList['sGoodTag']);
            //劣势标签转换
            $aList['sBadTag'] = Model_EvaluationTag::getChapterByIDS($aList['sBadTag']);
            $imgTypeConfig = $this->config['hx']['sType'];
            $imgList = $this->getImage($eID, $imgTypeConfig);
            //图片配置
            // 平面图
            $aList['sqpmImage'] = isset($imgList[$imgTypeConfig['sqpm']]) ? $imgList[$imgTypeConfig['sqpm']] : '';
            // 鸟瞰图
            $aList['sqnkImage'] = isset($imgList[$imgTypeConfig['sqnk']]) ? $imgList[$imgTypeConfig['sqnk']] : '';
            //其它图片
            $aList['ghqtImage'] = isset($imgList[$imgTypeConfig['ghqt']]) ? $imgList[$imgTypeConfig['ghqt']] : '';
            $this->formatImages($aList,'Image');
            $this->groupAllImages($aList,'Image');
        }else{
            return $this->showMsg(NULL, false);
        }
        return $this->showMsg($aList, true);
    }

    /**
     * 社区景观
     * param eID 评测报告ID
     * param cID 章节ID
     */
    public function getSceneryAction ()
    {
        $eID = $this->iEvaluationID;
        $pID = $this->iPchapter;
        $cID = $this->iCchapter;

        $aList = Model_EvaluationSqScenery::getSqSceneryByEID($eID);
        if(!empty($aList)) {
            //优势标签转换
            $aList['sGoodTag'] = Model_EvaluationTag::getChapterByIDS($aList['sGoodTag']);
            //劣势标签转换
            $aList['sBadTag'] = Model_EvaluationTag::getChapterByIDS($aList['sBadTag']);
            //获取图片配置
            $imgTypeConfig = $this->config['hx']['sType'];
            $imgList = $this->getImage($eID, $imgTypeConfig);
            //景观效果图
            $aList['sqxgImage'] = isset($imgList[$imgTypeConfig['sqxg']]) ? $imgList[$imgTypeConfig['sqxg']] : '';
            //景观实拍图
            $aList['sqspImage'] = isset($imgList[$imgTypeConfig['sqsp']]) ? $imgList[$imgTypeConfig['sqsp']] : '';
            //其它图片
            $aList['jgqtImage'] = isset($imgList[$imgTypeConfig['jgqt']]) ? $imgList[$imgTypeConfig['jgqt']] : '';
            $this->formatImages($aList,'Image');
            $this->groupAllImages($aList,'Image');
        }else{
            return $this->showMsg(NULL, false);
        }
        return $this->showMsg($aList, true);
    }

    /**
     * 建筑立面
     * param eID 评测报告ID
     * param cID 章节ID
     */
    public function getBuildAction ()
    {
        $eID = $this->iEvaluationID;
        $pID = $this->iPchapter;
        $cID = $this->iCchapter;

        $aList = Model_EvaluationSqBuilding::getSqBuildingByEID($eID);
        if(!empty($aList)) {
            //优势标签转换
            $aList['sGoodTag'] = Model_EvaluationTag::getChapterByIDS($aList['sGoodTag']);
            //劣势标签转换
            $aList['sBadTag'] = Model_EvaluationTag::getChapterByIDS($aList['sBadTag']);
            //获取图片配置
            $imgTypeConfig = $this->config['hx']['sType'];
            $imgList = $this->getImage($eID, $imgTypeConfig);
            //建筑立面效果图
            $aList['sqwsImage'] = isset($imgList[$imgTypeConfig['sqws']]) ? $imgList[$imgTypeConfig['sqws']] : '';
            //建筑立面实拍图
            $aList['sqwxImage'] = isset($imgList[$imgTypeConfig['sqwx']]) ? $imgList[$imgTypeConfig['sqwx']] : '';
            //其它图片
            $aList['jzqtImage'] = isset($imgList[$imgTypeConfig['jzqt']]) ? $imgList[$imgTypeConfig['jzqt']] : '';

            $this->formatImages($aList,'Image');
            $this->groupAllImages($aList,'Image');

        }else{
            return $this->showMsg(NULL, false);
        }
        return $this->showMsg($aList, true);
    }

    /**
     * 公共部位
     * param eID 评测报告ID
     * param cID 章节ID
     */
    public function getPublicAction ()
    {
        $eID = $this->iEvaluationID;
        $pID = $this->iPchapter;
        $cID = $this->iCchapter;

        $aList = Model_EvaluationSqPublic::getSqPublicByEID($eID);
        if(!empty($aList)) {
            //优势标签转换
            $aList['sGoodTag'] = Model_EvaluationTag::getChapterByIDS($aList['sGoodTag']);
            //劣势标签转换
            $aList['sBadTag'] = Model_EvaluationTag::getChapterByIDS($aList['sBadTag']);
            //获取图片配置
            $imgTypeConfig = $this->config['hx']['sType'];
            $imgList = $this->getImage($eID, $imgTypeConfig);
            //首层门厅单元门
            $aList['scdyImage'] = isset($imgList[$imgTypeConfig['scdy']]) ? $imgList[$imgTypeConfig['scdy']] : '';
            //首层门厅门禁系统
            $aList['scmjImage'] = isset($imgList[$imgTypeConfig['scmj']]) ? $imgList[$imgTypeConfig['scmj']] : '';
            //首层门厅灯具
            $aList['scdjImage'] = isset($imgList[$imgTypeConfig['scdj']]) ? $imgList[$imgTypeConfig['scdj']] : '';
            //首层门厅报箱
            $aList['scbxImage'] = isset($imgList[$imgTypeConfig['scbx']]) ? $imgList[$imgTypeConfig['scbx']] : '';
            //首层门厅地砖
            $aList['scdzImage'] = isset($imgList[$imgTypeConfig['scdz']]) ? $imgList[$imgTypeConfig['scdz']] : '';
            //首层门厅墙面
            $aList['scqmImage'] = isset($imgList[$imgTypeConfig['scqm']]) ? $imgList[$imgTypeConfig['scqm']] : '';
            //电梯厅电梯
            $aList['dtImage'] = isset($imgList[$imgTypeConfig['dt']]) ? $imgList[$imgTypeConfig['dt']] : '';
            //电梯厅灯具
            $aList['dtdjImage'] = isset($imgList[$imgTypeConfig['dtdj']]) ? $imgList[$imgTypeConfig['dtdj']] : '';
            //电梯厅地砖
            $aList['dtdzImage'] = isset($imgList[$imgTypeConfig['dtdz']]) ? $imgList[$imgTypeConfig['dtdz']] : '';
            //电梯厅墙面
            $aList['dtqmImage'] = isset($imgList[$imgTypeConfig['dtqm']]) ? $imgList[$imgTypeConfig['dtqm']] : '';
            //走廊灯具
            $aList['zldjImage'] = isset($imgList[$imgTypeConfig['zldj']]) ? $imgList[$imgTypeConfig['zldj']] : '';
            //走廊地砖
            $aList['zldzImage'] = isset($imgList[$imgTypeConfig['zldz']]) ? $imgList[$imgTypeConfig['zldz']] : '';
            //走廊墙面
            $aList['zlqmImage'] = isset($imgList[$imgTypeConfig['zlqm']]) ? $imgList[$imgTypeConfig['zlqm']] : '';
            //其它图片
            $aList['ggqtImage'] = isset($imgList[$imgTypeConfig['ggqt']]) ? $imgList[$imgTypeConfig['ggqt']] : '';
            $this->formatImages($aList,'Image');
            $this->groupAllImages($aList,'Image');
        }else{
            return $this->showMsg(NULL, false);
        }
        return $this->showMsg($aList, true);
    }

    /**
     * 社区配置
     * param eID 评测报告ID
     * param cID 章节ID
     */
    public function getConfigAction ()
    {
        $eID = $this->iEvaluationID;
        $pID = $this->iPchapter;
        $cID = $this->iCchapter;

        $aList = Model_EvaluationSqConfig::getSqConfigByEID($eID);
        if(!empty($aList)) {
            $aList['sGoodTag'] = null;
            $aList['sBadTag'] = null;
            //获取图片配置
            $imgTypeConfig = $this->config['hx']['sType'];
            $imgList = $this->getImage($eID, $imgTypeConfig);
            if (!empty($imgList) && count($imgList) > 0) {
                //儿童活动场 规划图
                $aList['etghImage'] = isset($imgList[$imgTypeConfig['etgh']]) ? $imgList[$imgTypeConfig['etgh']] : array();
                //儿童活动场 实景图
                $aList['etsjImage'] = isset($imgList[$imgTypeConfig['etsj']]) ? $imgList[$imgTypeConfig['etsj']] : array();
                //老年活动区 规划图
                $aList['lnghImage'] = isset($imgList[$imgTypeConfig['lngh']]) ? $imgList[$imgTypeConfig['lngh']] : array();
                //老年活动区 实景图
                $aList['lnsjImage'] = isset($imgList[$imgTypeConfig['lnsj']]) ? $imgList[$imgTypeConfig['lnsj']] : array();
                //篮球场 规划图
                $aList['lqghImage'] = isset($imgList[$imgTypeConfig['lqgh']]) ? $imgList[$imgTypeConfig['lqgh']] : array();
                //篮球场 实景图
                $aList['lqsjImage'] = isset($imgList[$imgTypeConfig['lqsj']]) ? $imgList[$imgTypeConfig['lqsj']] : array();
                //网球场 规划图
                $aList['wqghImage'] = isset($imgList[$imgTypeConfig['wqgh']]) ? $imgList[$imgTypeConfig['wqgh']] : array();
                //网球场 实景图
                $aList['wqsjImage'] = isset($imgList[$imgTypeConfig['wqsj']]) ? $imgList[$imgTypeConfig['wqsj']] : array();
                //室外游泳池 规划图
                $aList['swygImage'] = isset($imgList[$imgTypeConfig['swyg']]) ? $imgList[$imgTypeConfig['swyg']] : array();
                //室外游泳池 实景图
                $aList['swysImage'] = isset($imgList[$imgTypeConfig['swys']]) ? $imgList[$imgTypeConfig['swys']] : array();
                //室内游泳池 规划图
                $aList['slygImage'] = isset($imgList[$imgTypeConfig['slyg']]) ? $imgList[$imgTypeConfig['slyg']] : array();
                //室内游泳池 实景图
                $aList['slysImage'] = isset($imgList[$imgTypeConfig['slys']]) ? $imgList[$imgTypeConfig['slys']] : array();
                //健身房 规划图
                $aList['jsghImage'] = isset($imgList[$imgTypeConfig['jsgh']]) ? $imgList[$imgTypeConfig['jsgh']] : array();
                //健身房 实景图
                $aList['jssjImage'] = isset($imgList[$imgTypeConfig['jssj']]) ? $imgList[$imgTypeConfig['jssj']] : array();
                //棋牌房 规划图
                $aList['qpghImage'] = isset($imgList[$imgTypeConfig['qpgh']]) ? $imgList[$imgTypeConfig['qpgh']] : array();
                //棋牌房 实景图
                $aList['qpsjImage'] = isset($imgList[$imgTypeConfig['qpsj']]) ? $imgList[$imgTypeConfig['qpsj']] : array();
                //会所其他图
                $aList['hsqtImage'] = isset($imgList[$imgTypeConfig['hsqt']]) ? $imgList[$imgTypeConfig['hsqt']] : array();
                //其他图
                $aList['pzqtImage'] = isset($imgList[$imgTypeConfig['pzqt']]) ? $imgList[$imgTypeConfig['pzqt']] : array();
            }
            $this->formatImages($aList,'Image');
            $this->groupAllImages($aList,'Image');

        }else{
            return $this->showMsg(NULL, false);
        }
        return $this->showMsg($aList, true);
    }

    /**
     * 车位情况
     * param eID 评测报告ID
     * param cID 章节ID
     */
    public function getParkingAction ()
    {
        $eID = $this->iEvaluationID;
        $pID = $this->iPchapter;
        $cID = $this->iCchapter;

        $aList = Model_EvaluationSqParking::getSqParkingByEID($eID);
        if(!empty($aList)) {
            //优势标签转换
            $aList['sGoodTag'] = Model_EvaluationTag::getChapterByIDS($aList['sGoodTag']);
            //劣势标签转换
            $aList['sBadTag'] = Model_EvaluationTag::getChapterByIDS($aList['sBadTag']);

            $aList['iSellPrice'] = $aList['iSellPrice'] == '0.00' ? '-' : $aList['iSellPrice'];
            $aList['iRentPrice'] = $aList['iRentPrice'] == '0.00' ? '-' : $aList['iRentPrice'];
            $aList['iTempPrice'] = $aList['iTempPrice'] == '0.00' ? '-' : $aList['iTempPrice'];
            $aList['iFixedNum'] = $aList['iFixedNum'] == '0.00' ? '-' : $aList['iFixedNum'];
            $aList['iTempNum'] = $aList['iTempNum'] == '0.00' ? '-' : $aList['iTempNum'];
            $aList['iUndergroundNum'] = $aList['iUndergroundNum'] == '0.00' ? '-' : $aList['iUndergroundNum'];
            $aList['iOvergroundNum'] = $aList['iOvergroundNum'] == '0.00' ? '-' : $aList['iOvergroundNum'];

            //获取图片配置
            $imgTypeConfig = $this->config['hx']['sType'];
            $imgList = $this->getImage($eID, $imgTypeConfig);
            //地下车位平面图
            $aList['cwpmImage'] = isset($imgList[$imgTypeConfig['cwpm']]) ? $imgList[$imgTypeConfig['cwpm']] : array();
            //车位实景图
            $aList['cwsjImage'] = isset($imgList[$imgTypeConfig['cwsj']]) ? $imgList[$imgTypeConfig['cwsj']] : array();
            //行车路线
            $aList['xclxImage'] = isset($imgList[$imgTypeConfig['xclx']]) ? $imgList[$imgTypeConfig['xclx']] : array();
            //其它图片
            $aList['cwqtImage'] = isset($imgList[$imgTypeConfig['cwqt']]) ? $imgList[$imgTypeConfig['cwqt']] : array();

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