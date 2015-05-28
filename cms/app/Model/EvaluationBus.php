<?php
/*
 * 交通出行，自驾信息模型类
 * @author chasel
 */
class Model_EvaluationBus extends Model_Base
{

    const DB_NAME = 'cms';
    const TABLE_NAME = 't_evaluation_jt_bus';


    /*
    * 通过评测报告ID获取户型
    */
    public static function getBusByEID($Id)
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

    public static function saveData($busInfo)
    {
        //获取开关配置
        $evaluationSwitch =  Yaf_G::getConf('evaluationSwitch',null,'evaluation');
        //如果开关开启就执行插入和更新操作
        if (!empty($busInfo['iAutoID'])) {
            if($evaluationSwitch == 1) {
                unset($busInfo['sGoodTag']);
                unset($busInfo['sBadTag']);
            }
            return self::updData($busInfo);
        } else {
            if($evaluationSwitch == 0) {
                return self::addData($busInfo);
            }
        }
    }

    /**
     * 批量添加公交出行信息
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
     * 批量修改公交出行信息
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