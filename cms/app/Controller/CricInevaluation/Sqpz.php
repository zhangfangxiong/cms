<?php

/**
 * 社区品质接口
 * User: cjj
 * Date: 2015/03/16
 * Time: 11:17
 */
class Controller_CricInevaluation_Sqpz extends Controller_CricInevaluation_Base
{

    const P_CHAPTER = 3;

    /**
     * 整体规划
     */
    public function wholeAction()
    {
        // 获取接口数据
        $data = self::httpRequest(Model_EvaluationSqWhole::CHAPTER);
        // 标签文字转ID
        self::convertTag($data,self::P_CHAPTER,Model_EvaluationSqWhole::CHAPTER);
        if (!empty($data)) {
            // 新增和修改ID
            $IdArr = $this->getAddAndUpdateIdArr($data, 'Model_EvaluationSqWhole');
            $addArray = array();
            $updateArray = array();
            // 获取新增和修改数据
            $this->getAddAndUpdateData($data,$IdArr,$addArray,$updateArray);
            // 去除图片字段
            $this->filterFields($addArray,self::POSTFIX);
            $this->filterFields($updateArray,self::POSTFIX);
            // 保存数据
            Model_EvaluationSqWhole::addBatchData($addArray);
            Model_EvaluationSqWhole::updateBatchData($updateArray);
            // 保存图片
            self::saveImages($data,self::POSTFIX);
        }
    }

    /**
     * 社区景观
     */
    public function sceneryAction()
    {
        // 获取接口数据
        $data = self::httpRequest(Model_EvaluationSqScenery::CHAPTER);
        // 标签文字转ID
        self::convertTag($data,self::P_CHAPTER,Model_EvaluationSqScenery::CHAPTER);
        if (!empty($data)) {
            // 新增和修改ID
            $IdArr = $this->getAddAndUpdateIdArr($data, 'Model_EvaluationSqScenery');
            $addArray = array();
            $updateArray = array();
            // 获取新增和修改数据
            $this->getAddAndUpdateData($data,$IdArr,$addArray,$updateArray);
            // 去除图片字段
            $this->filterFields($addArray,self::POSTFIX);
            $this->filterFields($updateArray,self::POSTFIX);
            // 保存数据
            Model_EvaluationSqScenery::addBatchData($addArray);
            Model_EvaluationSqScenery::updateBatchData($updateArray);
            // 保存图片
            self::saveImages($data,self::POSTFIX);
        }
    }

    /**
     * 建筑立面
     */
    public function buildAction()
    {

        // 获取接口数据
        $data = self::httpRequest(Model_EvaluationSqBuilding::CHAPTER);
        // 标签文字转ID
        self::convertTag($data,self::P_CHAPTER,Model_EvaluationSqBuilding::CHAPTER);
        if (!empty($data)) {
            // 新增和修改ID
            $IdArr = $this->getAddAndUpdateIdArr($data, 'Model_EvaluationSqBuilding');
            $addArray = array();
            $updateArray = array();
            // 获取新增和修改数据
            $this->getAddAndUpdateData($data,$IdArr,$addArray,$updateArray);
            // 去除图片字段
            $this->filterFields($addArray,self::POSTFIX);
            $this->filterFields($updateArray,self::POSTFIX);
            // 保存数据
            Model_EvaluationSqBuilding::addBatchData($addArray);
            Model_EvaluationSqBuilding::updateBatchData($updateArray);
            // 保存图片
            self::saveImages($data,self::POSTFIX);
        }

    }

    /**
     *  公共部位
     */
    public function publicAction()
    {

        // 获取接口数据
        $data = self::httpRequest(Model_EvaluationSqPublic::CHAPTER);
        // 标签文字转ID
        self::convertTag($data,self::P_CHAPTER,Model_EvaluationSqPublic::CHAPTER);
        if (!empty($data)) {
            // 新增和修改ID
            $IdArr = $this->getAddAndUpdateIdArr($data, 'Model_EvaluationSqPublic');
            $addArray = array();
            $updateArray = array();
            // 获取新增和修改数据
            $this->getAddAndUpdateData($data,$IdArr,$addArray,$updateArray);
            // 去除图片字段
            $this->filterFields($addArray,self::POSTFIX_B);
            $this->filterFields($updateArray,self::POSTFIX_B);
            // 保存数据
            Model_EvaluationSqPublic::addBatchData($addArray);
            Model_EvaluationSqPublic::updateBatchData($updateArray);
            // 保存图片
            self::saveImages($data,self::POSTFIX_B);
        }
    }

    /**
     *  社区配置
     */
    public function configAction()
    {
        // 获取接口数据
        $data = self::httpRequest(Model_EvaluationSqConfig::CHAPTER);
        // 标签文字转ID
        self::convertTag($data,self::P_CHAPTER,Model_EvaluationSqConfig::CHAPTER);
        if (!empty($data)) {
            // 新增和修改ID
            $IdArr = $this->getAddAndUpdateIdArr($data, 'Model_EvaluationSqConfig');
            $addArray = array();
            $updateArray = array();
            // 获取新增和修改数据
            $this->getAddAndUpdateData($data,$IdArr,$addArray,$updateArray);
            // 去除图片字段
            $this->filterFields($addArray,self::POSTFIX_B);
            $this->filterFields($updateArray,self::POSTFIX_B);
            // 保存数据
            Model_EvaluationSqConfig::addBatchData($addArray);
            Model_EvaluationSqConfig::updateBatchData($updateArray);
            // 保存图片
            self::saveImages($data,self::POSTFIX_B);
        }
    }

    /**
     *  车位情况
     */
    public function parkingAction()
    {

        // 获取接口数据
        $data = self::httpRequest(Model_EvaluationSqParking::CHAPTER);
        // 标签文字转ID
        self::convertTag($data,self::P_CHAPTER,Model_EvaluationSqParking::CHAPTER);
        if (!empty($data)) {
            // 新增和修改ID
            $IdArr = $this->getAddAndUpdateIdArr($data, 'Model_EvaluationSqParking');
            $addArray = array();
            $updateArray = array();
            // 获取新增和修改数据
            $this->getAddAndUpdateData($data,$IdArr,$addArray,$updateArray);
            // 去除图片字段
            $this->filterFields($addArray,self::POSTFIX);
            $this->filterFields($updateArray,self::POSTFIX);
            // 保存数据
            Model_EvaluationSqParking::addBatchData($addArray);
            Model_EvaluationSqParking::updateBatchData($updateArray);
            // 保存图片
            self::saveImages($data,self::POSTFIX);
        }
    }

}