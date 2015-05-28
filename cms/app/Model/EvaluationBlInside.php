<?php
/**
 * 不利因素-社区内部
 * author: cjj
 */
class Model_EvaluationBlInside extends Model_Base
{

    const DB_NAME = 'cms';

    const TABLE_NAME = 't_evaluation_bl_inside';

    const CHAPTER = 21; //  当前章节ID
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
        $result = self::getBlInsideByEID($aParam['iEvaluationID']);
        $aParam['iStatus'] = 1;
        //获取开关配置
        $evaluationSwitch = self::getConfig();
        //如果开关开启就执行插入和更新操作
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
     * 通过评测报告ID获取社区外部信息
     *
    */
    public static function getBlInsideByEID($evaluationID) {
        if (empty($evaluationID)) {
            return null;
        }
        $aParam = array(
            'where' => array(
                'iEvaluationID' => $evaluationID,
                'iStatus' =>  1
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
     *  key 名转小写 因接口传的数据key名第一个字母是大写
     */
    public static function convertLowerCase($sGdblys) {
        $newArr = array();
        if (!empty($sGdblys)) {
            foreach($sGdblys as $key => $val) {
                foreach($val as $ck => $cv) {
                    $newArr[$key][strtolower($ck)] = $cv;
                }
            }
        }
        return $newArr;
    }

    /**
     * 批量添加社区内部
     */
    public static function addBatchData($data)
    {
        if (!empty($data)) {
            foreach($data as $item) {
                if (!empty($item['sGdblys'])) {
                    $item['sGdblys'] = json_encode($item['sGdblys']);
                }  else {
                    $item['sGdblys'] = '';
                }
                if (!empty($item['sDtblys'])) {
                    $item['sDtblys'] = json_encode($item['sDtblys']);
                } else {
                    $item['sDtblys'] = '';
                }
                $item = self::getOrm()->filterFields($item);// 过滤数据
                self::addData($item);
            }
        }
    }
    /**
     * 批量修改社区内部
     *
     */
    public static function updateBatchData($data)
    {
        if (!empty($data)) {
            foreach($data as $item) {
                if (!empty($item['sGdblys'])) {
                    $item['sGdblys'] = json_encode($item['sGdblys']);
                }  else {
                    $item['sGdblys'] = '';
                }
                if (!empty($item['sDtblys'])) {
                    $item['sDtblys'] = json_encode($item['sDtblys']);
                } else {
                    $item['sDtblys'] = '';
                }
                $item = self::getOrm()->filterFields($item);// 过滤数据
                self::updData($item);
            }
        }
    }
}