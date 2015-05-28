<?php
/*
 * 户型基本信息管理类 (房子朝向,面积等数据)
 * @author cjj
 */
class Model_EvaluationHxType extends Model_Base
{

    const DB_NAME = 'cms';

    const TABLE_NAME = 't_evaluation_hx_detail';

    const TP_PATH = '/Evaluation/Hxanalyse/hxList.phtml';


   /*
   * 获取所需配置
   */
    public static function getConfig()
    {
        // 户型
        $config['hx'] =  Yaf_G::getConf('hx',null,'evaluation');
        $config['evaluationSwitch'] =  Yaf_G::getConf('evaluationSwitch',null,'evaluation');
        return  $config;
    }
    /*
     * 添加户型数据
     * @param array $aParam 数据
     */
    public static function addHxType($aParam)
    {
        if (empty($aParam)) {
            return false;
        }

        if (self::checkNumberExist($aParam['iHxInfoID'],$aParam['sHuXingID'])) {
            throw new Exception('户型编号已存在该户型中!');
        }
        foreach ($aParam as $k => $v) {
            if (empty($v)) {
                unset($aParam[$k]);
            }
        }
        return self::addData($aParam);
    }
    /*
     * 修改户型数据
     */
    public static function upHxType($aParam)
    {
        if (empty($aParam)) {
            return false;
        }
        $oldHxType = self::getDetail($aParam['iAutoID']);
        if (empty($oldHxType)) {
            unset($aParam['iAutoID']);
            return self::addHxType($aParam);
        }
        if ($oldHxType['sHuXingID'] != $aParam['sHuXingID']) {
            if (self::checkNumberExist($aParam['iHxInfoID'],$aParam['sHuXingID'])) {
                throw new Exception('户型编号已存在该户型中!');
            }
        }
        foreach ($aParam as $k => $v) {
            if (empty($v)) {
                unset($aParam[$k]);
            }
        }
        return self::updData($aParam);
    }
    /*
     * 检测户型编号是否重复
     * @param string $number 户型编号
     * @param string $iHxInfoID
     */
    public static function checkNumberExist($iHxInfoID,$number)
    {

        $aWhere = array(
            'sHuXingID' => $number,
            'iHxInfoID' => $iHxInfoID,
            'iStatus' => 1
        );
        $aParam['where'] = $aWhere;
        $result = self::getOrm()->fetchAll($aParam);
        if (!empty($result)) {
            return true;
        }
        return false;
    }

    /*
     * 通过hxInfoID获取户型
     */
    public static function getHxTypeByInfoID($infoId)
    {
        if (empty($infoId)) {
            return null;
        }
        if (!is_array($infoId)) {
            $infoId = array();
        }
        $aParam = array(
            'where' => array(
                'iHxInfoID in' => implode(',',$infoId),
                'iStatus' => 1
            ),
            'order' => 'iCreateTime DESC',
        );
        return self::getOrm()->fetchAll($aParam);
    }
    /*
    * 显示
    */
    public static function getHxTypeList($iEvaluationID) {
        // 获取 户型数据
        $HxInfo = Model_EvaluationHxInfo::getHxInfoByEID($iEvaluationID);
        $config = self::getConfig();
        $infoList = array();
        if (!empty($HxInfo)) {
            $arr = array();
            foreach($HxInfo as $item) {
                $arr[$item['iHxInfoID']] = $item;
            }
            // 获取 户型编号数据
            $infoList = Model_EvaluationHxType::getHxTypeByInfoID(array_keys($arr));
            if (!empty($infoList)) {
                foreach($infoList as $k => $item) {
                    $index = $arr[$item['iHxInfoID']]['iRoomNum'];
                    $infoList[$k]['iRoomNum'] = $config['hx']['iRoom'][$index];
                    $index = $arr[$item['iHxInfoID']]['iHallNum'];
                    $infoList[$k]['iHallNum'] = $config['hx']['iHall'][$index];
                    $index = $arr[$item['iHxInfoID']]['iToiletNum'];
                    $infoList[$k]['iToiletNum'] =$config['hx']['iToilet'][$index];
                    $infoList[$k]['iMasterRoomToward'] =$config['hx']['iRoomToward'][$item['iMasterRoomToward']];
                    $infoList[$k]['iSecondRoomToward'] =$config['hx']['iRoomToward'][$item['iSecondRoomToward']];
                    $infoList[$k]['iIsNorthSouth'] =$item['iIsNorthSouth'] ==1?'是':'否';

                    $infoList[$k]['iFloorNum'] = $item['iFloorNum'] == '0.00' ? '-' : $item['iFloorNum'];

                    $infoList[$k]['iHallWidth'] = $item['iHallWidth'] == '0.00' ? '-' : $item['iHallWidth'];

                    $infoList[$k]['iMasterRoomWidth'] = $item['iMasterRoomWidth'] == '0.00' ? '-' : $item['iMasterRoomWidth'];
                    $infoList[$k]['iAreaRate'] = $item['iAreaRate'] == '0.00' ? '-' : $item['iAreaRate'];

                    $infoList[$k]['iStairsNum'] = empty($item['iStairsNum']) ? '-' : $item['iStairsNum'];

                    $infoList[$k]['iDoorNum'] = empty($item['iDoorNum']) ? '-' : $item['iDoorNum'];

                }
            }
        }
        return $infoList;
    }

    /**
     * 批量添加整体评价数据
     */
    public static function addBatchData($data)
    {
        if (!empty($data)) {
            $logId = array();
            foreach($data as $item) {
                if (!in_array($item['iAutoID'],$logId)) { // 存在重复数据
                    //$item['iIsNorthSouth'] = $item['iIsNorthSouth'] == '否' ? 0 : 1;
                    $item['iHxInfoID'] = Model_EvaluationHxInfo::getHxInfoPkId($item);
                    $item = self::getOrm()->filterFields($item);// 过滤数据
                    $item = self::formatHxInfo($item);
                    self::addData($item);
                    $logId[] = $item['iAutoID'];
                }
            }
        }
    }
    public static function formatHxInfo($item)
    {
        // 文字 转ID
        $hxConfig = Yaf_G::getConf('hx',null,'evaluation');
        $hxConfig['iRoomToward'] =array_flip($hxConfig['iRoomToward']);
        if (!empty($item['iMasterRoomToward'])) {
            $item['iMasterRoomToward'] = $hxConfig['iRoomToward'][$item['iMasterRoomToward']];
        } else {
            $item['iMasterRoomToward'] =0;
        }
        if (!empty($item['iSecondRoomToward'])) {
            $item['iSecondRoomToward'] = $hxConfig['iRoomToward'][$item['iSecondRoomToward']];
        } else {
            $item['iSecondRoomToward'] =0;
        }
        if (!empty($item['iIsNorthSouth'])) {
            $item['iIsNorthSouth'] = $item['iIsNorthSouth'] == '是' ? 1:0;
        } else {
            $item['iIsNorthSouth'] =0;
        }
        return $item;
    }
    /**
     * 批量修改整体评价数据
     *
     */
    public static function updateBatchData($data)
    {
        if (!empty($data)) {
            foreach($data as $item) {
                $item['iHxInfoID'] = Model_EvaluationHxInfo::getHxInfoPkId($item);
                //$item['iIsNorthSouth'] = $item['iIsNorthSouth'] == '否' ? 0 : 1;

                $item = self::getOrm()->filterFields($item);// 过滤数据
                $item = self::formatHxInfo($item);
                self::updData($item);
            }
        }
    }
}