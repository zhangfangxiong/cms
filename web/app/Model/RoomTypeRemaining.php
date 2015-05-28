<?php

/**
 * Created by PhpStorm.
 * User: cjj
 * Date: 15/4/7
 * Time: 下午1:44
 */
class Model_RoomTypeRemaining
{

    // 从接口中获取房源数据
    public static  function getRoomTypeRemaining($homeId)
    {
         $result = Sdk_Cms_Newhouse::getRoomTypeRemaining($homeId);
        if ($result['status'] == 1) {
            return $result['data'];
        }
        return null;
    }

    // 剩余房源
    public static  function getRemainingRooms(&$data)
    {
        $num = 0;
        if (empty($data)) {
            return $num;
        }
        foreach ($data as $item) {
            $num += $item['HomeRemainingNum'];
        }
        return $num;
    }
}