<?php
/*
 * 户型数据管理类(几室几厅等数据) 一个户型可对应多个户型基本信息（房子朝向，面积等数据）
 * @author cjj
 */
class Model_EvaluationHxInfo extends Model_Base
{

    const DB_NAME = 'cms';
    const TABLE_NAME = 't_evaluation_hx_info';



    /**
    * 获取所需配置
    */
    public static function getConfig()
    {
        // 户型
        $config['hx'] =  Yaf_G::getConf('hx',null,'evaluation');
        return  $config;
    }
    /*
     * 通过评测报告ID获取户型
     */
    public static function getHxInfoByEID($Id)
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
        return self::getOrm()->fetchAll($aParam);
    }

    /*
     * 查询 户型数据是否存在
     */
    public static function getHxInfo($aParam)
    {
        $aParam = array(
            'where' => array(
                'iEvaluationID' => $aParam['iEvaluationID'],
                'iRoomNum' => $aParam['iRoomNum'],
                'iHallNum' => $aParam['iHallNum'],
                'iToiletNum' => $aParam['iToiletNum'],
                'iStatus' => 1
            ),
        );
        return self::getRow($aParam);
    }

    /**
     *  通过 iRoomNum iHallNum iToiletNum iEvaluationID 组合 获取主键ID
     */
    public static function getPkID($data)
    {

         $iHxInfoID = self::getOne(array(
            'where' => array(
                'iRoomNum' => $data['iRoomNum'],
                'iHallNum' =>  $data['iHallNum'],
                'iToiletNum' =>  $data['iToiletNum'],
                'iEvaluationID' => $data['iEvaluationID']
            )),
            'iHxInfoID');
         if (empty($iHxInfoID)) {
             $iHxInfoID = self::addData(array(
                 'iRoomNum' => $data['iRoomNum'],
                 'iHallNum' =>  $data['iHallNum'],
                 'iToiletNum' =>  $data['iToiletNum'],
                 'iEvaluationID' => $data['iEvaluationID']
             ));
         }
        return $iHxInfoID;
    }

    /**
     *  接口过来的文字 转 ID
     */
    public static  function convertId($data)
    {
        $hxConfig = current(self::getConfig());
        $hxConfig['iRoom'] = array_flip($hxConfig['iRoom']);
        $hxConfig['iHall'] = array_flip($hxConfig['iHall']);
        $hxConfig['iToilet'] = array_flip($hxConfig['iToilet']);
        if (!empty($data['iRoomNum'])) {
            $data['iRoomNum'] = $hxConfig['iRoom'][$data['iRoomNum']];
        } else {
            $data['iRoomNum'] = 0 ;
        }
        if (!empty($data['iHallNum'])) {
            $data['iHallNum'] = $hxConfig['iHall'][$data['iHallNum']];
        } else {
            $data['iHallNum'] = 0 ;
        }
        if (!empty($data['iToiletNum'])) {
            $data['iToiletNum'] = $hxConfig['iToilet'][$data['iToiletNum']];
        } else {
            $data['iToiletNum'] = 0 ;
        }
        return $data;
    }

    public static function getHxInfoPkId($data) {
        //  获取 hxInfo表(几室几厅数据) ID
        $info = self::convertId(array(
            'iRoomNum' => $data['iRoomNum'],
            'iHallNum' => $data['iHallNum'],
            'iToiletNum' => $data['iToiletNum'],
        ));
        $info['iEvaluationID'] = $data['iEvaluationID'];
        return  self::getPkID($info);

    }
}