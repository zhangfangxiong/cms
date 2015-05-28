<?php
/*
 * 交通出行，自驾信息模型类
 * @author chasel
 */
class Model_EvaluationBusInfo extends Model_Base
{

    const DB_NAME = 'cms';
    const TABLE_NAME = 't_evaluation_jt_bus_info';

    /**
     * 根据id获取相关车辆站点信息
     * @param $ids， 以，分割的字符串
     * @return int
     */
    public static function getRelatedBusById($id)
    {
        $option = array();
        $option['where'] = array('iJtBusId' => $id, 'iStatus' => 1);
        $option['order'] = 'iAutoId ASC';
        if (!empty($id)) {
            return self::getAll($option);
        }
        return null;
    }


}