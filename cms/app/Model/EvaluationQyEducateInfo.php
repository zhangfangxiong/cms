<?php
/**
 * 区域配套-教育资源
 * author: cjj
 */
class Model_EvaluationQyEducateInfo extends Model_Base
{

    const DB_NAME = 'cms';

    const TABLE_NAME = 't_evaluation_qy_school_info';

    const CHAPTER = 17; //  当前章节ID
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
        return  $config;
    }
    /*
     * 保存学校数据
     */
    public  static  function saveData($aParam)
    {
        if (empty($aParam)) {
            return false;
        }
        $id = array();
        //添加幼儿园信息
        if(!empty($aParam['sSchoolName'])){
            foreach($aParam['sSchoolName'] as $key=>$value) {
                if($key == 1){
                    $aParams['iType'] = 1;
                }elseif($key == 2){
                    $aParams['iType'] = 2;
                }else{
                    $aParams['iType'] = 3;
                }
                foreach($value as $k=>$v) {
                    $aParams['iEvaluationID'] = $aParam['iEvaluationID'];  //评测ID
                    $aParams['iSchoolID'] = $aParam['iSchoolID'];  //主表ID
                    $aParams['sSchoolName'] = $v;
                    $aParams['sSchoolType'] = isset($aParam['sSchoolType'][$key][$k])?$aParam['sSchoolType'][$key][$k]:'';
                    $aParams['sSchoolDesc'] = isset($aParam['sSchoolDesc'][$key][$k])?$aParam['sSchoolDesc'][$key][$k]:'';
                    if(!empty($aParams['sSchoolName']) || !empty($aParams['sSchoolType']) || !empty($aParams['sSchoolDesc'])) {
                        if (!empty($aParam['schoolID'][$key][$k])) {
                            $aParams['iAutoID'] = $aParam['schoolID'][$key][$k];
                            self::updData($aParams);
                            $id[$aParams['iType']][$k] = $aParams['iAutoID'];
                        } else {
                            unset($aParams['iAutoID']);
                            $id[$aParams['iType']][$k] = self::addData($aParams);
                        }
                    }
                }
            }
            return $id;
        }
    }


    /*
     * 通过评价主表自增ID获取学校信息
     *
    */
    public static function getQyEducateInfoByEID($iSchoolID,$type)
    {
        if (empty($iSchoolID)) {
            return null;
        }
        $aParam = array(
            'where' => array(
                'iSchoolID' => $iSchoolID,
                'iStatus' => 1,
                'iType' => $type
            ),
        );
        $result = self::getOrm()->fetchAll($aParam);
        if (!empty($result)) {
            return $result;
        }
        return null;
    }

    /**
     * 删除学校信息
     */
    public static function deleteData($id){
        return self::delData($id);
    }
}