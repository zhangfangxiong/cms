<?php
/**
 * Created by PhpStorm.
 * User: yaobiqing
 * Date: 15/4/7
 * Time: 下午1:01
 */

class Model_CricRoomTypeRemaining extends Model_Base
{

    const TABLE_NAME = 'RoomTypeRemaining';
    const DB_NAME = 'cric_xf';

    public static function getRemainingByHomeID($sHomeID)
    {

        $sSQL = 'SELECT * FROM '.self::TABLE_NAME.' WHERE HomeID=\''.addslashes($sHomeID).'\' AND STATE=1';
        return self::query($sSQL);
    }

    public static function getRemainingRoomOfUnit($sHomeID)
    {
        $sSQL = 'SELECT sum(HomeRemainingNum) as count FROM '.self::TABLE_NAME.' WHERE HomeID=\''.addslashes($sHomeID).'\' AND STATE=1';
        $row = self::query($sSQL, 'row');
        return intval($row['count']);
    }

    public static function getRoomOfType($sHomeID, $sHomeType)
    {
        $sSQL = 'SELECT * FROM '.self::TABLE_NAME.' WHERE HomeID=\''.addslashes($sHomeID).'\' AND STATE=1 AND HomeType=\''.addslashes($sHomeType).'\'';
        $row = self::query($sSQL, 'row');
        return $row;
    }
}