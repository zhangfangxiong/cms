<?php

/**
 * 交通出行接口
 * User: chasel
 * Date: 2015/03/19
 * Time: 10:30
 */
class Controller_CricInevaluation_Traffic extends Controller_CricInevaluation_Base
{
    const CHAPTER_PARENT = 5;
    const CHAPTER_DRIVE = 13;
    const CHAPTER_RAIL = 14;
    const CHAPTER_BUS = 15;
    /**
     * 自驾出行
     */
    public function driveAction()
    {
        // 获取接口数据
        $data = self::httpRequest(self::CHAPTER_DRIVE);

        if (!empty($data)) {
            self::convertTag($data, self::CHAPTER_PARENT, self::CHAPTER_DRIVE);

            $addArray = array();
            $updateArray = array();
            $IdArr = $this->getAddAndUpdateIdArr($data, 'Model_EvaluationDriving');
            // 获取新增和修改数据
            $this->getAddAndUpdateData($data,$IdArr,$addArray,$updateArray);
            // 保存数据
            Model_EvaluationDriving::addBatchData($addArray);
            Model_EvaluationDriving::updateBatchData($updateArray);
            // 自驾线路图
            self::saveImages($data,'Images');
        }

    }

    /**
     * 保存自驾线路图

    public function saveDriveImages(&$data)
    {
        $images = array();
        foreach($data as $item) {
            if (!empty($item['aImages'])) {
                foreach ($item['aImages'] as $image) {
                    $image['iCricId'] = $image['iAutoID'];
                    unset($image['iAutoID']);
                    $images[] = $image;
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
     */
    /**
     * 轨交出行
     */
    public function railAction()
    {
        // 获取接口数据
        $data = self::httpRequest(self::CHAPTER_RAIL);

        if (!empty($data)) {
            self::convertTag($data, self::CHAPTER_PARENT, self::CHAPTER_RAIL);

            $addArray = array();
            $updateArray = array();
            $IdArr = $this->getAddAndUpdateIdArr($data, 'Model_EvaluationJtMetro');
            // 获取新增和修改数据
            $this->getAddAndUpdateData($data,$IdArr,$addArray,$updateArray);

            $len = sizeof($addArray);
            for($i = 0; $i < $len; $i ++ ) {
                $addArray[$i]['iHasMetro'] = 0;
            }
            // 保存数据
            Model_EvaluationJtMetro::addBatchData($addArray);
            Model_EvaluationJtMetro::updateBatchData($updateArray);
            // 保存图片
            self::saveImages($data,self::POSTFIX);
        }
    }

    /**
     * 公交出行
     */
    public function busAction()
    {
        // 获取接口数据
        $data = self::httpRequest(self::CHAPTER_BUS);

        if (!empty($data)) {
            self::convertTag($data, self::CHAPTER_PARENT, self::CHAPTER_BUS);

            $addArray = array();
            $updateArray = array();
            $IdArr = $this->getAddAndUpdateIdArr($data, 'Model_EvaluationBus');
            // 获取新增和修改数据
            $this->getAddAndUpdateData($data,$IdArr,$addArray,$updateArray);
            // 保存数据
            Model_EvaluationBus::addBatchData($addArray);
            Model_EvaluationBus::updateBatchData($updateArray);
            // 保存图片
            self::saveImages($data,self::POSTFIX);
        }
    }

}