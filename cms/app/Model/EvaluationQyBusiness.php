<?php
/**
 * 区域配套-周边商圈
 * author: cjj
 */
class Model_EvaluationQyBusiness extends Model_Base
{

    const DB_NAME = 'cms';

    const TABLE_NAME = 't_evaluation_qy_business';

    const CHAPTER = 19; //  当前章节ID

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
        Model_Evaluation::updateChapter($aParam['iEvaluationID'],self::CHAPTER);
        Model_EvaluationTag::formatTag($aParam);
        $result = self::getQyBusinessByEID($aParam['iEvaluationID']);
        $aParam['iStatus'] = 1;
        //获取开关配置
        $evaluationSwitch =  self::getConfig();
        if (!empty($result)) {
            //如果开关开启就去掉优势劣势，然后执行更新
            if($evaluationSwitch['evaluationSwitch'] == 1){
                    unset($aParam['sGoodTag']);
                    unset($aParam['sBadTag']);
            }
            $aParam['iAutoID'] = $result['iAutoID'];
            return self::updData($aParam);
        } else {
            //如果开关开启就执行添加操作
            if($evaluationSwitch['evaluationSwitch'] == 0) {
                return self::addData($aParam);
            }
        }
    }

    /*
     * 通过评测报告ID获取周边商圈信息
     *
    */
    public static function getQyBusinessByEID($evaluationID) {
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
     * 批量添加周边商圈
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
     * 批量修改周边商圈
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