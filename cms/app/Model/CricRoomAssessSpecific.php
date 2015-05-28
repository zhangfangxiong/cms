<?php

class Model_CricRoomAssessSpecific extends Model_Base
{
    const DB_NAME = 'cric_xf';

    const TABLE_NAME = 'RoomAssessSpecific';

    /*
     *  获取楼盘详情
     */
    public static function getAssess($AssessID)
    {
        $where = array(
            'ID IN' => $AssessID,
        );

        return self::getAll(['where' => $where]);
    }

}