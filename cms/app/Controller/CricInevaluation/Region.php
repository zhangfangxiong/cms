<?php

/**
 * 区域配套接口
 * User: cjj
 * Date: 2015/03/16
 * Time: 11:17
 */
class Controller_CricInevaluation_Region extends Controller_CricInevaluation_Base
{

    const P_CHAPTER = 6;

    /**
     * 区位简介
     */
    public function positionAction()
    {
        // 获取接口数据
        $data = self::httpRequest(Model_EvaluationQyPosition::CHAPTER);
        // 标签文字转ID
        self::convertTag($data,self::P_CHAPTER,Model_EvaluationQyPosition::CHAPTER);
        if (!empty($data)) {
            // 新增和修改ID
            $IdArr = $this->getAddAndUpdateIdArr($data, 'Model_EvaluationQyPosition');
            $addArray = array();
            $updateArray = array();
            // 获取新增和修改数据
            $this->getAddAndUpdateData($data,$IdArr,$addArray,$updateArray);
            // 去除图片字段
            $this->filterFields($addArray,self::POSTFIX);
            $this->filterFields($updateArray,self::POSTFIX);
            // 保存数据
            Model_EvaluationQyPosition::addBatchData($addArray);
            Model_EvaluationQyPosition::updateBatchData($updateArray);
            // 保存图片
            self::saveImages($data,self::POSTFIX);
        }
    }

    /**
     * 教育资源
     */
    public function educateAction()
    {
        // 获取接口数据
        $data = self::httpRequest(Model_EvaluationQyEducate::CHAPTER);
        // 标签文字转ID
        self::convertTag($data,self::P_CHAPTER,Model_EvaluationQyEducate::CHAPTER);
        if (!empty($data)) {
            // 新增和修改ID
            $IdArr = $this->getAddAndUpdateIdArr($data, 'Model_EvaluationQyEducate');
            $addArray = array();
            $updateArray = array();
            // 获取新增和修改数据
            $this->getAddAndUpdateData($data,$IdArr,$addArray,$updateArray);
            // 保存数据
            Model_EvaluationQyEducate::addBatchData($addArray);
            Model_EvaluationQyEducate::updateBatchData($updateArray);
            // 保存图片
            self::saveImages($data,self::POSTFIX);
        }
    }

    /**
     * 医疗资源
     */
    public function medicalAction()
    {

        // 获取接口数据
        $data = self::httpRequest(Model_EvaluationQyMedical::CHAPTER);
        // 标签文字转ID
        self::convertTag($data,self::P_CHAPTER,Model_EvaluationQyMedical::CHAPTER);
        if (!empty($data)) {
            // 新增和修改ID
            $IdArr = $this->getAddAndUpdateIdArr($data, 'Model_EvaluationQyMedical');
            $addArray = array();
            $updateArray = array();
            // 获取新增和修改数据
            $this->getAddAndUpdateData($data,$IdArr,$addArray,$updateArray);
            // 保存数据
            Model_EvaluationQyMedical::addBatchData($addArray);
            Model_EvaluationQyMedical::updateBatchData($updateArray);
            // 保存图片
            self::saveImages($data,self::POSTFIX);
        }
    }

    /**
     *  周边商圈
     */
    public function businessAction()
    {
        // 获取接口数据
        $data = self::httpRequest(Model_EvaluationQyBusiness::CHAPTER);
        // 标签文字转ID
        self::convertTag($data,self::P_CHAPTER,Model_EvaluationQyBusiness::CHAPTER);
        if (!empty($data)) {
            // 新增和修改ID
            $IdArr = $this->getAddAndUpdateIdArr($data, 'Model_EvaluationQyBusiness');
            $addArray = array();
            $updateArray = array();
            // 获取新增和修改数据
            $this->getAddAndUpdateData($data,$IdArr,$addArray,$updateArray);
            // 保存数据
            Model_EvaluationQyBusiness::addBatchData($addArray);
            Model_EvaluationQyBusiness::updateBatchData($updateArray);
            // 保存图片
            self::saveImages($data,self::POSTFIX);
        }
    }

    /**
     *  公共资源
     */
    public function publicAction()
    {
        // 获取接口数据
        $data = self::httpRequest(Model_EvaluationQyPublic::CHAPTER);
        // 标签文字转ID
        self::convertTag($data,self::P_CHAPTER,Model_EvaluationQyPublic::CHAPTER);
        if (!empty($data)) {
            // 新增和修改ID
            $IdArr = $this->getAddAndUpdateIdArr($data, 'Model_EvaluationQyPublic');
            $addArray = array();
            $updateArray = array();
            // 获取新增和修改数据
            $this->getAddAndUpdateData($data,$IdArr,$addArray,$updateArray);
            // 去除图片字段
            $this->filterFields($addArray,self::POSTFIX);
            $this->filterFields($updateArray,self::POSTFIX);
            // 保存数据
            Model_EvaluationQyPublic::addBatchData($addArray);
            Model_EvaluationQyPublic::updateBatchData($updateArray);
            // 保存图片
            self::saveImages($data,self::POSTFIX);
        }
    }

}