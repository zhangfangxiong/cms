<?php

/**
 * Created by PhpStorm.
 * User: cjj
 * Date: 15/4/7
 * Time: 下午1:44
 */
class Model_Room
{


    public static function getRoomsByUnitId($unitId)
    {
        $result = Sdk_Cms_Newhouse::getRoomsByUnitId($unitId);
        if ($result['status'] == 1 && !empty($result['data'])) {
            return $result['data'];
        }
        return array();
    }

    public static function getRoomPriceInfoByRoomId($blockId,$roomId)
    {
        $result = Sdk_Cms_Newhouse::getRoomPriceInfoByRoomId($blockId,$roomId);
        if ($result['status'] == 1 && !empty($result['data'])) {
            return $result['data'];
        }
        return array();
    }
}