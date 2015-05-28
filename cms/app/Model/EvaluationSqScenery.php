<?php
/**
 * 社区景观
 * author: cjj
 */
class Model_EvaluationSqScenery extends Model_Base
{

    const DB_NAME = 'cms';

    const TABLE_NAME = 't_evaluation_sq_scenery';

    const CHAPTER = 6; //  当前章节ID

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
        $config['standard'] =  Yaf_G::getConf('standard',null,'evaluation');
        $config['evaluationSwitch'] =  Yaf_G::getConf('evaluationSwitch',null,'evaluation');
        return  $config;
    }
    /*
     * 保存数据
     */
    public  static  function saveData($aParam)
    {
        if (empty($aParam)) {
            return false;
        }
        // 最后编辑章节
        $status = Model_Evaluation::updateChapter($aParam['iEvaluationID'],self::CHAPTER);
        Model_EvaluationTag::formatTag($aParam);
        $result = self::getSqSceneryByEID($aParam['iEvaluationID']);
        $aParam['iStatus'] = 1;
        //获取开关配置
        $evaluationSwitch = self::getConfig();
        //判断开关是否开启如果开启就执行添加和修改功能
        if($evaluationSwitch['evaluationSwitch'] == 0) {
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
     * 通过评测报告ID获取社区景观
     *
    */
    public static function getSqSceneryByEID($evaluationID) {

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
     * 批量添加社区景观数据
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
     * 批量修改社区景观数据
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