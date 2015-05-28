<?php
/*
 * 交通出行，自驾信息模型类
 * @author chasel
 */
class Model_EvaluationDriving extends Model_Base
{

    const DB_NAME = 'cms';
    const TABLE_NAME = 't_evaluation_jt_driving';
    const CHAPTERDRIVING = 13;

    /*
    * 通过评测报告ID获取户型
    */
    public static function getDrivingByEID($Id)
    {
        if (empty($Id)) {
            return null;
        }
        $aParam = array(
            'where' => array(
                'iEvaluationID' => $Id,
                'iStatus' => 1
            ),
            'order' => 'iCreateTime DESC',
        );
        $result = self::getOrm()->fetchRow($aParam);

        if (!empty($result)) {
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

    public static function saveData($driving)
    {
        //更新最后编辑章节
        $iEvaluationID = intval($driving['iEvaluationID']);
        $status = Model_Evaluation::updateChapter($iEvaluationID, self::CHAPTERDRIVING);
        //获取开关配置
        $evaluationSwitch =  Yaf_G::getConf('evaluationSwitch',null,'evaluation');
        //如果开关关闭就执行插入和更新操作
        if($evaluationSwitch == 0) {
            if (!empty($driving['iAutoID'])) {
                return self::updData($driving);
            } else {
                return self::addData($driving);
            }
        }else{
            return $status;
        }
    }

    /**
     * 批量添加自驾出行信息
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
     * 批量修改自驾出行信息
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