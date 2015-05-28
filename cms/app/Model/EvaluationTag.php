<?php
/*
 * 评测标签业务管理类
 * @author cjj
 */
class Model_EvaluationTag extends Model_Base
{

    const DB_NAME = 'cms';

    const TABLE_NAME = 't_evaluation_tag';



    /*
     * 获取所需配置
     *
     */
    public static function getConfig()
    {
         // 章节
         $config['chapter'] =  Yaf_G::getConf('chapter',null,'evaluation');
         // 优劣势
         $config['yls'] =  Yaf_G::getConf('yls',null,'evaluation');
         return  $config;
    }

    /*
     * 获取评测标签列表
     * @param array $aParam 查询参数
     * @param int $iPage 当前页
     * @return array
     */

    public static function getTagList($aParam,$iPage)
    {
        $arg = $aParam;// 翻页参数
        if (empty($aParam['iCatID'])) {
           unset($aParam['iCatID']);
        }
        if (empty($aParam['iSubCatID'])) {
            unset($aParam['iSubCatID']);
        }
        if (empty($aParam['sType'])) {
            unset($aParam['sType']);
        } else {
            $config = self::getConfig();
            $aParam['sType'] =  $config['yls'][$aParam['sType']];
        }
        if (!empty($aParam['keywords'])) {
            $aParam['sName LIKE'] = '%' . $aParam['keywords'] . '%';
        }
        unset($aParam['keywords']);
        $aParam['iStatus'] = 1;// 未删除
        return Model_EvaluationTag::getList($aParam, $iPage, 'iCreateTime DESC',20,'', $arg);
    }

    /*
     * 通过标签名获取标签数据
     * @param string $sName 标签名
     * @return array
     */
    public static function getTagInfoByName($sName)
    {
        return self::getRow(array(
            'where' => array(
                'sName' => $sName
            )
        ));
    }

    /*
     * 修改评测标签
     * @param array $aParam
     */

    public static function updateTagInfoById($aParam)
    {
        $aOldTag = self::getDetail($aParam['iTagID']);
        if (empty($aOldTag)) {
            throw new Exception('标签信息不存在！');
        }
        if ($aOldTag['sName'] != $aParam['sName']) {
            $temp = self::getTagInfoByName($aParam['sName']);
            if (!empty($temp)) {
                throw new Exception('标签名称已存在！');
            }
        }
        if (self::updData($aParam) == true) {
            return true;
        } else {
            throw new Exception('标签信息更新失败！');
        }
    }

    /*
     *  通过ID 删除评测
     * @param string $tagId
     * @return bool
     */
    public static function delTagById($tagId)
    {
        if (empty($tagId)) {
            return false;
        }
        return  self::delData($tagId);
    }

    /*
    * 通过章节获取标签名称列表
    * @param int $pId 父章节ID
    * @param int $cId 子章节ID
    * @param string $sType 优劣势
    * @return array
    */
    public static  function getTagNamesByChapterId($pId,$cId,$sType='',$eID=0)
    {
        if (empty($pId) || empty($cId)) {
            return null;
        }
        $aWhere = array(
            'iCatID' => $pId,
            'iSubCatID' => $cId,
            'sType' => $sType,
            'iStatus' => 1
        );
        $aParam['where'] = $aWhere;
        $result = self::getOrm()->fetchAll($aParam);

        $arr = array();
        if (!empty($result)) {
            foreach($result as $item) {
                $arr[$item['iTagID']] = $item['sName'];
            }
        }
        // 其他标签
        $modelMap = array(
            1 => 'Model_EvaluationHxWhole',
            2 => 'Model_EvaluationHxAnalyse',
            3 => 'Model_EvaluationZxBrand',
            4 => 'Model_EvaluationZxAnalysis',
            5 => 'Model_EvaluationSqWhole',
            6 => 'Model_EvaluationSqScenery',
            7 => 'Model_EvaluationSqBuilding',
            8 => 'Model_EvaluationSqPublic',
            9 => 'Model_EvaluationSqConfig',
            10 => 'Model_EvaluationSqParking',
            11 => 'Model_EvaluationWyCost',
            12 => 'Model_EvaluationWyService',
            13 => 'Model_EvaluationDriving',
            14 => 'Model_EvaluationJtMetro',
            15 => 'Model_EvaluationBus',
            16 => 'Model_EvaluationQyPosition',
            17 => 'Model_EvaluationQyEducate',
            18 => 'Model_EvaluationQyMedical',
            19 => 'Model_EvaluationQyBusiness',
            20 => 'Model_EvaluationQyPublic',
            21 => 'Model_EvaluationBlInside',
            22 => 'Model_EvaluationBlOutside',
        );
        if ($eID) {
            $aParam = array(
                'where' => array(
                    'iEvaluationID' => $eID,
                    'iStatus' => 1
                ),
                'limit' => '0,1'
            );
            $result = current($modelMap[$cId]::getAll($aParam));
            $tagIDStr = '';
            if ($sType == '优势') {
                if (isset($result['sGoodTag'])) {
                    $tagIDStr =  $result['sGoodTag'];
                }
            } else {
                if (isset($result['sBadTag'])) {
                    $tagIDStr =  $result['sBadTag'];
                }

            }
            $otherTagArr = array();
            if (!empty($tagIDStr) && !empty($arr) ) {
                $dbTagID = array_keys($arr);
                $evaluationTagID = explode(',',$tagIDStr);
                // 找出其他标签ID
                foreach($evaluationTagID as $k=>$v) {
                    if (!in_array($v,$dbTagID)) {
                        $otherTagArr[] = $v;
                    }
                }
            }
            if (!empty($otherTagArr)) {
                $aParam = array(
                    'where' => array(
                        'iTagID in ' => implode(',',$otherTagArr),
                        'iStatus' => 1
                    )
                );
                $result = self::getAll($aParam);
                if (!empty($result)) {
                    foreach($result as $item) {
                        $arr[$item['iTagID']] = $item['sName'];
                    }
                }
            }
        }
        if (empty($arr)) {
            return null;
        }
        return $arr;
    }

    public static  function  formatTag(&$aParam) {
        if (isset($aParam['sGoodTag'])) {
            $aParam['sGoodTag'] = implode(',',$aParam['sGoodTag']);
        } else {
            $aParam['sGoodTag'] = '';
        }
        if (isset($aParam['sBadTag'])) {
            $aParam['sBadTag'] = implode(',',$aParam['sBadTag']);
        } else {
            $aParam['sBadTag'] = '';
        }
    }
    // 批量添加数据
    public static function addBatchData($data)
    {
        if (empty($data)) {
            return ;
        }
        $map = self::getChapterMap();
        foreach ($data as $k => $item) {
            $item['iCatID'] = $map[$item['iSubCateID']];
            $item['iSubCatID'] = $item['iSubCateID'];
            $num = self::getCnt(array(
                'where' => array(
                    'sType' => $item['sType'],
                    'sName' => $item['sName'],
                    'iSubCatID' => $item['iSubCatID'],
                    'iStatus' => $item['iStatus']
                ),
            ));
            if ($num<=0) {
                self::addData($item);
            }
        }
    }
    /**
     * 获取子章节和父章节对应关系
     * return array('sub' => parent)
     */
    public static function getChapterMap()
    {
        $config = self::getConfig();
        $map = array();
        foreach( $config['chapter']['action'] as $k=> $item) {
            foreach ($item as $kc => $vc) {
                $map[$kc] = $k;
            }
        }
        return $map;
    }

    public static function getChapterByIDS($IDS)
    {
        if (empty($IDS)) {
            return null;
        }
        $aParam = array(
            'where' => array(
                'iTagID in ' => implode(',',$IDS),
                'iStatus' => 1
            )
        );
        $arr = null;
        $result = self::getAll($aParam);
        if (!empty($result)) {
            foreach($result as $item) {
                $arr[$item['iTagID']] = $item['sName'];
            }
        }
        return $arr;
    }

}