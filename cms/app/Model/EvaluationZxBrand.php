<?php
/**
 * 品牌配置
 * author: cjj
 */
class Model_EvaluationZxBrand extends Model_Base
{

    const DB_NAME = 'cms';

    const TABLE_NAME = 't_evaluation_zx_brand';

    const CHAPTER = 3; // 当前章节ID

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
        $config['medical'] =  Yaf_G::getConf('medical',null,'evaluation');
        $config['evaluationSwitch'] =  Yaf_G::getConf('evaluationSwitch',null,'evaluation');
        return  $config;
    }
    /*
     * 保存数据
     */
    public  static  function saveData($aParam)
    {

        if (isset($aParam['sGoodTag'])) {
            $aParam['sGoodTag'] = implode(',',$aParam['sGoodTag']);
        }
        if (!isset($aParam['iIsBlank'])) {
            $aParam['iIsBlank']  = 0;
        }
        // 最后编辑章节
        $status = Model_Evaluation::updateChapter($aParam['iEvaluationID'],self::CHAPTER);
        $result = self::getBrandByEID($aParam['iEvaluationID']);
        $aParam['iStatus'] = 1;
        //获取开关配置
        $evaluationSwitch = self::getConfig();
        //如果开关开启就执行插入及更新操作
        if($evaluationSwitch['evaluationSwitch'] == 0) {
            if (!empty($result)) {
                $aParam['iAutoID'] = $result['iAutoID'];
                return self::updData($aParam);
            } else {
                unset( $aParam['iAutoID']);
                return self::addData($aParam);
            }
        }else{
            return $status;
        }
    }

    /*
     * 通过评测报告ID获取品牌配置
     *
    */
    public static function getBrandByEID($evaluationID) {

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
            return $result;
        }
        return null;
    }

    /*
     * 修改毛坯交付字段
     **/
    public static function updateIsBlank($evaluationID,$iIsBlank) {

       /*
        $aParam = array(
            'where' => array(
                'iEvaluationID' => $evaluationID
            ),
        );
        $arr = self::getRow($aParam);
        if (!empty($arr)) {
            $data['iAutoID'] = $arr['iAutoID'];
            $data['iEvaluationID'] = $arr['iEvaluationID'];
            $data['iIsBlank'] = $arr['iIsBlank'];
            self::updData($data);
        } else {

        }
       */
    }
    /**
     * 批量添加数据
     */
    public static function addBatchData($data)
    {
        if (!empty($data)) {
            foreach($data as $item) {
                $item = self::getOrm()->filterFields($item);// 过滤数据
                $item = self::formatInfo($item);
                self::addData($item);
            }
        }
    }
    public static function formatInfo($item)
    {

        $config = Yaf_G::getConf('standard',null,'evaluation');
        $config['qllx'] =array_flip($config['qllx']);
        if (!empty($item['sToiletYbLx'])) {
            $item['sToiletYbLx'] = $config['qllx'][$item['sToiletYbLx']];
        } else {
            $item['sToiletYbLx'] = 0;
        }
        return $item;
    }
    /**
     * 批量修改数据
     *
     */
    public static function updateBatchData($data)
    {
        if (!empty($data)) {
            foreach($data as $item) {
                $item = self::getOrm()->filterFields($item);// 过滤数据
                $item = self::formatInfo($item);
                self::updData($item);
            }
        }
    }

}