<?php

/**
 * 装修标准接口
 * User: cjj
 * Date: 2015/03/16
 * Time: 11:17
 */
class Controller_CricInevaluation_Zxstandard extends Controller_CricInevaluation_Base
{

    const P_CHAPTER = 2;

    /**
     * 品牌配置
     */
    public function brandAction()
    {
        // 获取接口数据
        $data = self::httpRequest(Model_EvaluationZxBrand::CHAPTER);
        // 标签文字转ID
        self::convertTag($data,self::P_CHAPTER,Model_EvaluationZxBrand::CHAPTER);
        if (!empty($data)) {
            // 新增和修改ID
            $IdArr = $this->getAddAndUpdateIdArr($data, 'Model_EvaluationZxBrand');
            $addArray = array();
            $updateArray = array();
            // 获取新增和修改数据
            $this->getAddAndUpdateData($data,$IdArr,$addArray,$updateArray);
            // 去除图片字段
            $this->filterFields($addArray,self::POSTFIX_B);
            $this->filterFields($updateArray,self::POSTFIX_B);
            // 保存数据
            Model_EvaluationZxBrand::addBatchData($addArray);
            Model_EvaluationZxBrand::updateBatchData($updateArray);
            // 保存图片
            self::saveImages($data,self::POSTFIX_B);
        }
    }

    /**
     * 装修分析
     */
    public function analysisAction()
    {
        // 获取接口数据
        $data = self::httpRequest(Model_EvaluationZxAnalysis::CHAPTER);
        // 标签文字转ID
        self::convertTag($data,self::P_CHAPTER,Model_EvaluationZxAnalysis::CHAPTER);
        if (!empty($data)) {
            // 新增和修改ID
            $IdArr = $this->getAddAndUpdateIdArr($data, 'Model_EvaluationZxAnalysis');
            $addArray = array();
            $updateArray = array();
            // 获取新增和修改数据
            $this->getAddAndUpdateData($data,$IdArr,$addArray,$updateArray);
            // 保存数据
            Model_EvaluationZxAnalysis::addBatchData($addArray);
            Model_EvaluationZxAnalysis::updateBatchData($updateArray);
            // 保存图片
            self::saveImages($data,'Images');
        }
    }

}