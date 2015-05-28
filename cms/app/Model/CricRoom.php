<?php

class Model_CricRoom extends Model_Base
{
    const DB_NAME = 'cric_xf';

    const TABLE_NAME = 'Room';

    /*
     * 根据楼盘和户型获取房间
     */
    public static function getRoomByType($unitID, $typCode)
    {
        $where = array(
            'UnitId' => $unitID,
            'TypeCode' => $typCode,
            'State' => 1
        );
        return self::getAll(['where' => $where]);
    }

    /*
         * 根据楼盘和户型获取房间
         */
    public static function getPriceByID($roomID)
    {
        $where = array(
            'RoomID' => $roomID,
        );
        return self::getRow(['where' => $where]);
    }

    public static function getLowestPrice($sUnitID)
    {
        $sSQL = "SELECT min(TotalPrice) total, BlockNumber, RoomNumber, TypeCode, TypeName, Area FROM ". self::TABLE_NAME. " WHERE UnitId='".addslashes($sUnitID)."' and State = 1";
        $result =  self::query($sSQL, 'row');

        return $result;
    }

    // 小区的所有房间号
    public static function getRoomsByUnitId($unitId)
    {
        $sql = "SELECT TypeName,Area,RoomNumber,BlockId,RoomID,UnitId FROM Room Where UnitId = '".$unitId."' AND State = 1 AND Area >0 ORDER BY RoomNumber";
        return self::query($sql);
    }


    public static function getRoomPriceInfoByRoomId($blockId,$roomId)
    {
        $sql = "SELECT Area,Price,TotalPrice FROM Room WHERE BlockID = '".$blockId."' AND RoomID = '".$roomId."'";
        return self::query($sql,'row');
    }



    //房价点评网价目表页
    public static function getRoomByUnitId($UnitID)
    {
        $where = array(
            array(
            'UnitId' => $UnitID,
            'State' => 1
            ),
            'order' => 'BlockNumber'
        );
        return self::getAll($where);
    }

    //房贷计算
    public static function getRoom($BlockID,$RoomID)
    {
        $sql = "SELECT * FROM ". self::TABLE_NAME. " WHERE State = 1 AND BlockID = '$BlockID' AND RoomID = '$RoomID'";
        return self::query($sql);
    }
}