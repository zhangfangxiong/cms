<?php
/**
 * 评测整体评价业务类
 * author: cjj
 */
class Model_EvaluationHxWhole extends Model_Base
{

    const DB_NAME = 'cms';

    const TABLE_NAME = 't_evaluation_hx_whole';

    const CHAPTER = 1; //  当前章节ID
    /*
    * 获取所需配置
    *
    */
    public static function getConfig()
    {
        // 章节
        $config['chapter'] =  Yaf_G::getConf('chapter',null,'evaluation');
        $config['yls'] =  Yaf_G::getConf('yls',null,'evaluation');
        $config['hx'] =  Yaf_G::getConf('hx',null,'evaluation');
        $config['evaluationSwitch'] =  Yaf_G::getConf('evaluationSwitch',null,'evaluation');
        return  $config;
    }
    /*
     * 添加整体评价
     */
    /**
     * @param $aParam
     * @return bool|int|void
     */
    public static function addHxWhole($aParam)
    {
        if (empty($aParam)) {
            return false;
        }
        // 过滤空数据
//        foreach($aParam as $k => $v) {
//            if (empty($v)) {
//                unset($aParam[$k]);
//            }
//        }
        //获取开关配置
        $evaluationSwitch = Yaf_G::getConf('evaluationSwitch',null,'evaluation');
        // 最后编辑章节
        $status = Model_Evaluation::updateChapter($aParam['iEvaluationID'],self::CHAPTER);
        // 检测 该评测报告下的整体评价是否已存在
        $result = self::getHxWholeByEID($aParam['iEvaluationID']);
        Model_EvaluationTag::formatTag($aParam);
        $aParam['iStatus'] = 1;
        //如果开关为关闭就做添加和修改操作
        if($evaluationSwitch == 0) {
            // 添加
            if (empty($result)) {
                return self::addData($aParam);
                // 修改
            } else {
                $aParam['IHxWholeID'] = $result['IHxWholeID'];
                return self::updData($aParam);
            }
        }else{
            return $status;
        }
    }

     /*
     * 通过评测报告ID 获取整体评价
     */
    public static function getHxWholeByEID($evaluationID)  {

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