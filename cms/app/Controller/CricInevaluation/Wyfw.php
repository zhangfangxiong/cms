<?php

/**
 * 物业服务接口
 * User: cjj
 * Date: 2015/03/16
 * Time: 11:17
 */
class Controller_CricInevaluation_Wyfw extends Controller_CricInevaluation_Base
{

    const P_CHAPTER = 4;

    /**
     * 物业费用
     */
    public function costAction()
    {

        // 获取接口数据
        $data = self::httpRequest(Model_EvaluationWyCost::CHAPTER);
        // 标签文字转ID
        self::convertTag($data,self::P_CHAPTER,Model_EvaluationWyCost::CHAPTER);
        if (!empty($data)) {
            // 新增和修改ID
            $IdArr = $this->getAddAndUpdateIdArr($data, 'Model_EvaluationWyCost');
            $addArray = array();
            $updateArray = array();
            // 获取新增和修改数据
            $this->getAddAndUpdateData($data,$IdArr,$addArray,$updateArray);
            // 保存数据
            Model_EvaluationWyCost::addBatchData($addArray);
            Model_EvaluationWyCost::updateBatchData($updateArray);
            // 保存图片
            self::saveImages($data,self::POSTFIX);
        }
    }

    /**
     * 物业服务
     */
    public function serviceAction()
    {

        // 获取接口数据
        $data = self::httpRequest(Model_EvaluationWyService::CHAPTER);
        // 标签文字转ID
        self::convertTag($data,self::P_CHAPTER,Model_EvaluationWyService::CHAPTER);
        if (!empty($data)) {
            // 新增和修改ID
            $IdArr = $this->getAddAndUpdateIdArr($data, 'Model_EvaluationWyService');
            $addArray = array();
            $updateArray = array();
            // 获取新增和修改数据
            $this->getAddAndUpdateData($data,$IdArr,$addArray,$updateArray);
            // 保存数据
            Model_EvaluationWyService::addBatchData($addArray);
            Model_EvaluationWyService::updateBatchData($updateArray);
            // 保存图片
            self::saveImages($data,self::POSTFIX);
        }
    }

}