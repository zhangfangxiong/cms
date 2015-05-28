<?php

/**
 * 户型分析接口
 * User: cjj
 * Date: 2015/03/16
 * Time: 11:17
 */
class Controller_CricInevaluation_Hxanalyse extends Controller_CricInevaluation_Base
{


    const P_CHAPTER = 1;
    /**
     * 整体评价
     */
    public function ztpjAction()
    {
        // 获取接口数据
        $data = self::httpRequest(Model_EvaluationHxWhole::CHAPTER);
        // 标签文字转ID
        self::convertTag($data,self::P_CHAPTER,Model_EvaluationHxWhole::CHAPTER);
        if (!empty($data)) {
            // 获取新增和修改ID
            $addArray = array();
            $updateArray = array();
            $IdArr = $this->getAddAndUpdateIdArr($data,'Model_EvaluationHxWhole','IHxWholeID');
            // 获取新增和修改数据
            $this->getAddAndUpdateData($data,$IdArr,$addArray,$updateArray,'IHxWholeID');
            // 保存数据
            Model_EvaluationHxWhole::addBatchData($addArray);
            Model_EvaluationHxWhole::updateBatchData($updateArray);
            // 保存图片
            self::saveImages($data,'Images');
            // 户型数据
           $this->saveHxInfo($data);
        }
    }

    /**
     * 保存户型数据
     */
    public function saveHxInfo(&$data)
    {
        $hxInfoData = null;
        if (!empty($data)) {
            foreach($data as $item) {
                if (!empty($item['hxData'])) {
                    foreach($item['hxData'] as $v) {
                        $v['iEvaluationID'] = $item['iEvaluationID'];
                        $hxInfoData[] = $v;
                    }
                }
            }
        }
        $addArray = array();
        $updateArray = array();
        // 获取新增和修改ID
        $IdArr = $this->getAddAndUpdateIdArr($hxInfoData,'Model_EvaluationHxType');
        // 获取新增和修改数据
        $this->getAddAndUpdateData($hxInfoData,$IdArr,$addArray,$updateArray);
        Model_EvaluationHxType::addBatchData($addArray);
        Model_EvaluationHxType::updateBatchData($updateArray);

    }

    /**
     * 户型分析
     */
    public function hxfxAction()
    {
        // 获取接口数据
        $data = self::httpRequest(Model_EvaluationHxAnalyse::CHAPTER);
        self::convertTag($data,self::P_CHAPTER,Model_EvaluationHxAnalyse::CHAPTER);
        if (!empty($data)) {
            $addArray = array();
            $updateArray = array();
            $IdArr = $this->getAddAndUpdateIdArr($data, 'Model_EvaluationHxAnalyse');
            // 获取新增和修改数据
            $this->getAddAndUpdateData($data,$IdArr,$addArray,$updateArray);
            // 保存数据
            Model_EvaluationHxAnalyse::addBatchData($addArray);
            Model_EvaluationHxAnalyse::updateBatchData($updateArray);
            // 户型相关图
            self::saveHxImages($data);
        }
    }

    /**
     * 保存 户型分析相关图
     */
    public function saveHxImages(&$data)
    {
       /*
            $data[images] => Array
            (
                [1303] => Array
                (
                    [101] => Array (
                        )
               )
           )
       */
        $images = array();
        foreach($data as $item) {
            if (!empty($item['images'])) {
                foreach ($item['images'] as $typeArr) {
                    if (isset($typeArr[101]) && !empty($typeArr[101])) {
                        foreach ($typeArr[101] as $v) {
                            $v['iCricId'] = $v['iAutoID'];
                            unset($v['iAutoID']);
                            $images[] = $v;
                        }
                    }
                    if (isset($typeArr[102]) && !empty($typeArr[102])) {
                        foreach ($typeArr[102] as $v) {
                            $v['iCricId'] = $v['iAutoID'];
                            unset($v['iAutoID']);
                            $images[] = $v;
                        }
                    }
                }
            }
            if (!empty($item['OtherImages'])) { //其他图片
                foreach ($item['OtherImages'] as $v) {
                    $v['iCricId'] = $v['iAutoID'];
                    unset($v['iAutoID']);
                    $images[] = $v;
                }
            }
        }
        $addArray = array();
        $updateArray = array();
        // 获取新增和修改ID
        $IdArr = $this->getAddAndUpdateIdArr($images,'Model_EvaluationImage','iCricId');
        // 获取新增和修改数据
        $this->getAddAndUpdateData($images,$IdArr,$addArray,$updateArray,'iCricId');
        Model_EvaluationImage::addBatchData($addArray);
        Model_EvaluationImage::updateBatchData($updateArray);
    }

}