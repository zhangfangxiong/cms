<?php
/**
 * 物业费用
 * author: cjj
 */
class Model_EvaluationWyCost extends Model_Base
{

    const DB_NAME = 'cms';

    const TABLE_NAME = 't_evaluation_wy_cost';

    const CHAPTER = 11; //  当前章节ID

    /*
     * 保存数据
     */
    public static function saveData($aParam)
    {
        if (empty($aParam)) { // 空 为表单都是选填项
            return true;
        }
        // 最后编辑章节
        $status = Model_Evaluation::updateChapter($aParam['iEvaluationID'],self::CHAPTER);
        Model_EvaluationTag::formatTag($aParam);
        $result = self::getWyCostByEID($aParam['iEvaluationID']);
        $aParam['iStatus'] = 1;
        //获取开关配置
        $evaluationSwitch = Yaf_G::getConf('evaluationSwitch',null,'evaluation');
        //如果开关关闭就执行插入及更新操作
        if($evaluationSwitch == 0) {
            if (!empty($result)) {
                $aParam['iAutoID'] = $result['iAutoID'];
                return self::updData($aParam);
            } else {
                return self::addData($aParam);
            }
        }else{
            return $status;
        }
    }

    /*
     * 通过评测报告ID获取装修分析
     *
    */
    public static function getWyCostByEID($evaluationID) {

        if (empty($evaluationID)) {
            return null;
        }
        $aParam = array(
            'where' => array(
                'iEvaluationID' => $evaluationID,
                'iStatus' => 1
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
    /**
     * 批量添加物业费用
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
     * 批量修改物业费用
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