<?php

class Model_CricRoomType extends Model_Base
{
    const DB_NAME = 'cric_xf';

    const TABLE_NAME = 'RoomType';

    public static function getRoomTypeByBID($UnitID)
    {
        $sql = "select `UnitID`, `UnitIndex`, `TypeName`, `TypeName`, `TypeCode`, `ImageCode`, `AssessA`, `AssessB`, ".
            "`AssessDescription`, `Area`, `MinArea`, `MaxArea`, `Price`, `MinPrice`, `MaxPrice`, `MinTotalPrice`, `MaxTotalPrice`, `BiddingPrice`, `RoomTotal`, ".
            "`Score`, `State`, `UpdateTime`, `AreaTotal`, `RecommendedRoomTypeFlag`, `RecommendedRoomTypeTitle`, `RecommendedRoomTypeRemark` from RoomType where ".
            "UnitID = '".addslashes($UnitID)."' and State =1";

        return self::query($sql, 'all');
    }

    public static function getRoomTypeDetail($sUnitID, $sTypeCode)
    {
        $sql = "SELECT * FROM ".self::TABLE_NAME." WHERE UnitID='".addslashes($sUnitID)."' AND TypeCode='".addslashes($sTypeCode)."' AND State=1";
        return self::query($sql, 'row');
    }


    public static function getRoomTypeByUnitID($sUnitID)
    {
        $sql = "SELECT * FROM ".self::TABLE_NAME." WHERE UnitID='".addslashes($sUnitID)."' AND State=1 order by TypeName,TypeCode";
        return self::query($sql);

    }

    //房价点评网（价目表页获取房型）
    public static function getRoomType($sUnitID)
    {
        $sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE UnitID='" . addslashes($sUnitID) . "' AND State=1  order by TypeName,TypeCode";
        return self::query($sql);
    }

    public static function  getRecommendedRoomTypes($sUnitID)
    {
        $sql = "SELECT * FROM RoomType  WHERE UnitId = '".addslashes($sUnitID)."' AND RecommendedRoomTypeFlag = 1 ORDER BY UpdateTime DESC";
        $result = self::query($sql);
        if (!empty($result)) {
            foreach ($result as &$item) {
                $item['imgUrl'] = '';
                if (!empty($item['ImageCode'])) {
                    $item['imgUrl'] = Model_EvaluationImage::getImageUrl(1,$item['ImageCode'],132,97);
                }
            }
        }
    }
}