<?php
/*
 * author cjj
 */
class Controller_Evaluation_Hxbase extends Controller_Base
{


    protected $iEvaluationID = 0;// 评测报告ID

    protected $iChildChapterID = 0; // 子章节ID


    /*获取GET参数*/
    protected function getID()
    {
        $this->iEvaluationID = intval($this->getParam('eID')); // 评测报告ID
        $this->iChildChapterID = intval($this->getParam('cID')); // 子章节ID
        if (empty($this->iChildChapterID)) {
            $this->iChildChapterID = $this->getSubCatId();
        }
    }

    /*
     * 变量替换公共部分
     */
    protected function assignCommon()
    {
        // 分类章节
        $config = Model_EvaluationZxBrand::getConfig();
        $this->assign('pChapter', $config['chapter']['parent']);
        //数组长度
         $this->assign('pChapterlength', count($config['chapter']['parent']));
        if (isset($config['chapter']['child'][$this->getCatId()])) {
            $this->assign('cChapter', $config['chapter']['child'][$this->getCatId()]);
        }
        $iEvaluationInfo = Model_Evaluation::getDetail($this->iEvaluationID);
        // 评测报告
        $this->assign('iEvaluationInfo', $iEvaluationInfo);
        // 各个章节对应的控制器
        $this->assign('controllerArr', $config['chapter']['controller']);
        // 各个子章节对应的action
        $this->assign('actionArr', $config['chapter']['action']);
        // 当前章节
        $this->assign('currChapter', $this->getCatId());
        //当前子章节
        $this->assign('currChildChapter', $this->iChildChapterID);
        // 优势标签
        $this->assign('goodTags', Model_EvaluationTag::getTagNamesByChapterId($this->getCatId(),$this->iChildChapterID,$config['yls'][1],$this->iEvaluationID));
        // 劣势标签
        $this->assign('badTags', Model_EvaluationTag::getTagNamesByChapterId($this->getCatId(),$this->iChildChapterID,$config['yls'][2],$this->iEvaluationID));
        // 当前城市分析师
        $this->assign('analyst',Model_Analysts::getAnalystNameByID($iEvaluationInfo['iAnalysisID']));
        $this->assign('analystList',Model_Analysts::getAnalystsList($this->iCurrCityID));
        $this->assign('sFileBaseUrl', 'http://' . Yaf_G::getConf('file', 'domain'));
        $this->assign('evaluationSwitch',$config['evaluationSwitch']);
    }

    /*
    * 表单公共数据
    */
    protected function checkCommonData()
    {
        $aParam['sComment'] = $this->getParam('sComment');
        $aParam['iEvaluationID'] = intval($this->getParam('iEvaluationID'));
        $aParam['iAutoID'] = intval($this->getParam('iAutoID'));
        $aParam['sGoodTag'] = $this->getParam('sGoodTag');
        $aParam['sBadTag'] = $this->getParam('sBadTag');
         $aParam['sOtherComment'] = $this->getParam('sOtherComment');
        if (empty($aParam['iEvaluationID'])) {
            return $this->showMsg('请选择评测报告！', false);
        }
        return $aParam;
    }

    /*
     *  获取装修 图片类型
     * */
    protected function filterConfig($min,$max)
    {
        $arr = array();
        foreach($this->config['hx']['sType'] as $item) {
            if ($item>=$min and $item<$max) {
                $arr[] = $item;
            }
        }
        return $arr;
    }

    /**
     * 添加其他图
     */
    protected function addQtImages($type)
    {
        Model_EvaluationImage::addImagesAndDesc($this->getParam('iEvaluationID'),
            $type,
            $this->getParam('qtImage'),
            $this->getParam('qtImageTitle'),
            $this->getParam('qtImageDesc'),
            $this->getParam('qtUpImageId'));
    }

    protected function getCatId()
    {

    }


    protected function getSubCatId()
    {

    }
}