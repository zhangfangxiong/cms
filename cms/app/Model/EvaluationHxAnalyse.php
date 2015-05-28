<?php
/*
 * 户型分析业务管理类
 * @author cjj
 */
class Model_EvaluationHxAnalyse extends Model_Base
{

    const DB_NAME = 'cms';

    const TABLE_NAME = 't_evaluation_hx_analysis';
    const CHAPTER = 2;  // 当前章节ID

    /*
    * 通过评测报告ID 获取户型分析
    */
    public static function getHxAnalyseByEID($evaluationID)  {
        if (empty($evaluationID)) {
            return null;
        }
        $aParam = array(
            'where' => array(
                'iEvaluationID' => $evaluationID,
                 'iStatus' => 1,
            ),
            'limit' => '0,1'
        );
        $result = self::getOrm()->fetchAll($aParam);
        if (!empty($result)) {
            $result = current($result);
            if (!empty($result['sGoodTag'])) {
                $result['sGoodTag'] = explode(',',$result['sGoodTag']);
            }
            if (!empty($result['sBadTag'])) {
                $result['sBadTag'] = explode(',',$result['sBadTag']);
            }
            return $result;
        }
        return null;
    }


    /*
     * 添加户型分析数据
     */
    public static function addHxAnalyse($aParam)
    {
        if (empty($aParam)) {
            return false;
        }
        // 过滤空数据
        foreach($aParam as $k => $v) {
            if (empty($v)) {
                unset($aParam[$k]);
            }
        }
        // 最后编辑章节
        $status = Model_Evaluation::updateChapter($aParam['iEvaluationID'],self::CHAPTER);
        // 检测 该评测报告下的整体评价是否已存在
        $result = self::getHxAnalyseByEID($aParam['iEvaluationID']);
        // 是否删除了户型编号
        $config = Model_EvaluationHxWhole::getConfig();
        //判断开关是否开启，如果没开启就执行添加和编辑功能
        if($config['evaluationSwitch'] == 0) {
            if (!empty($result['sHuXingID']) && !empty($aParam['sHuXingID'])) {
                $delHxIdArr = array_diff(explode(',', $result['sHuXingID']), explode(',', $aParam['sHuXingID']));
                Model_EvaluationImage::delImagesByTargetId($delHxIdArr, array(
                    $config['hx']['sType']['hd'],
                    $config['hx']['sType']['sp'],
                ));
            }
            Model_EvaluationTag::formatTag($aParam);
            $aParam['iStatus'] = 1;
            // 添加
            if (empty($result)) {
                return self::addData($aParam);
                // 修改
            } else {
                $aParam['iAutoID'] = $result['iAutoID'];
                return self::updData($aParam);
            }
        }else{
            return $status;
        }
    }

    /**
     * 批量添加整体评价数据
     */
    public static function addBatchData($data)
    {
        if (!empty($data)) {
            foreach($data as $item) {
                $item = self::getOrm()->filterFields($item);// 过滤数据
                self::addData($item);
            }
        }
    }
    /**
     * 批量修改整体评价数据
     *
     */
    public static function updateBatchData($data)
    {
        if (!empty($data)) {
            foreach($data as $item) {
                $item = self::getOrm()->filterFields($item);// 过滤数据
                self::updData($item);
            }
        }
    }
}