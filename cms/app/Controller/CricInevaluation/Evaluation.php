<?php

/**
 * 社区品质接口
 * User: cjj
 * Date: 2015/03/16
 * Time: 11:17
 */
class Controller_CricInevaluation_Evaluation extends Controller_CricInevaluation_Base
{

    /**
     * 评测报告
     */
    public function indexAction()
    {
        // 获取接口数据
        $data = self::httpRequest(50);
        if (!empty($data)) {
            // 新增和修改ID
            $IdArr = $this->getAddAndUpdateIdArr($data, 'Model_Evaluation','iEvaluationID');
            $addArray = array();
            $updateArray = array();
            // 获取新增和修改数据
            $this->getAddAndUpdateData($data,$IdArr,$addArray,$updateArray,'iEvaluationID');
            // 保存数据
            Model_Evaluation::addBatchData($addArray);
            Model_Evaluation::updateBatchData($updateArray);
        }
    }

}