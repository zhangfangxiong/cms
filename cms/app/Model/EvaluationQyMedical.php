<?php
/**
 * 区域配套-医疗资源
 * author: cjj
 */
class Model_EvaluationQyMedical extends Model_Base
{

    const DB_NAME = 'cms';

    const TABLE_NAME = 't_evaluation_qy_hospital';

    const CHAPTER = 18; //  当前章节ID

    const TP_PATH = '/Evaluation/Region/hospitalList.phtml';
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
        if (empty($aParam)) {
            return false;
        }
        Model_EvaluationTag::formatTag($aParam);
        $result = self::getQyMedicalByEID($aParam['iEvaluationID']);
        $aParam['iStatus'] = 1;
        //获取开关配置
        $evaluationSwitch = self::getConfig();

        if (!empty($result)) {
            //如果开关关闭就去掉优势劣势，然后执行更新操作
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
     * 通过评测报告ID获取区位简介
     *
    */
    public static function getQyMedicalByEID($evaluationID) {
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
    /*
     * 保存医院信息
     *
     * */
    public static function saveHospital($iEvaluationID,$hospital) {
        if (empty($iEvaluationID) || empty($hospital)) {
            return false;
        }
        $result = self::getQyMedicalByEID($iEvaluationID);
        if (!empty($result)) {
            if (!empty($result['sHospital'])) {
                $dbHospital = json_decode($result['sHospital'],true);
                array_push($dbHospital,$hospital);
            } else {
                $dbHospital[0] = $hospital;
            }
            $dbHospital = json_encode($dbHospital);
           return  self::updData(array(
                    'iAutoID' => $result['iAutoID'],
                    'sHospital' => $dbHospital,
            ));
        } else {
            $dbHospital[0] = $hospital;
            $dbHospital = json_encode($dbHospital);
            return self::saveData(array(
                'iEvaluationID' => $iEvaluationID,
                'sHospital' => $dbHospital,
            ));
        }
    }
    /*
     * 编辑医院信息
     * */
    public static function editHospital($iEvaluationID,$hId,$hospital) {
        if (empty($iEvaluationID) || empty($hospital)) {
            return false;
        }
        $result = self::getQyMedicalByEID($iEvaluationID);
        if (!empty($result) && !empty($result['sHospital'])) {
            $dbHospital = json_decode($result['sHospital'],true);
            if (isset($dbHospital[$hId])) {
                $dbHospital[$hId] = $hospital;
                return  self::updData(array(
                    'iAutoID' => $result['iAutoID'],
                    'sHospital' => json_encode($dbHospital),
                ));
            }
        }
    }
    /*
     * 删除医院信息
     * */
    public static function delHospital($iEvaluationID,$hId)
    {
        if (empty($iEvaluationID)) {
            return false;
        }
        $result = self::getQyMedicalByEID($iEvaluationID);
        if (!empty($result) && !empty($result['sHospital'])) {
            $dbHospital = json_decode($result['sHospital'],true);
            if (isset($dbHospital[$hId])) {
                unset($dbHospital[$hId]);
            }
            if (!empty($dbHospital)) {
                $dbHospital = json_encode($dbHospital);
            } else {
                $dbHospital = '';
            }
            return  self::updData(array(
                'iAutoID' => $result['iAutoID'],
                'sHospital' => $dbHospital,
            ));
        }
        return false;
    }

    /**
     * 医疗数据重新设置下
     *
     */
    public static function formatMedicalData(&$qyMedical)
    {
        if (!empty($qyMedical['sHospital'])) {
            $qyMedical['sHospital'] = json_decode($qyMedical['sHospital'],true);
            // 取出图片
            foreach($qyMedical['sHospital'] as $k => $item) {
                if (isset($item['imgID']) && !empty($item['imgID'])) {
                    $qyMedical['sHospital'][$k]['images'] = Model_EvaluationImage::getImagesByID($item['imgID']);
                } else {
                    $qyMedical['sHospital'][$k]['images'] = null;
                }
            }
        } else { // 默认显示一条
            $qyMedical['sHospital'][0]['name'] = '';
            $qyMedical['sHospital'][0]['dp'] = '';
        }
        if (!empty($qyMedical['sClinic'])) {
            $qyMedical['sClinic'] = json_decode($qyMedical['sClinic'],true);
        } else { // 默认显示一条
            $qyMedical['sClinic'][0]['name'] = '';
            $qyMedical['sClinic'][0]['type'] = array();
            $qyMedical['sClinic'][0]['zk'] = '';
            $qyMedical['sClinic'][0]['dp'] = '';
        }
        if (!empty($qyMedical['sPharmacy'])) {
            $qyMedical['sPharmacy'] = json_decode($qyMedical['sPharmacy'],true);
        } else {
            $qyMedical['sPharmacy'][0]['name'] = '';
            $qyMedical['sPharmacy'][0]['ts'] = array();
            $qyMedical['sPharmacy'][0]['dp'] = '';
        }
    }

    /**
     * 批量添加医疗资源
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
     * 批量修改医疗资源
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