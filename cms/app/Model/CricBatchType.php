<?php
/**
 * Created by PhpStorm.
 * User: yaobiqing
 * Date: 15/4/8
 * Time: 下午3:10
 */


class Model_CricBatchType extends Model_Base
{

    const TABLE_NAME = 'BatchType';
    const DB_NAME = 'cric_xf';

    public static function getBatchType($sUnitID)
    {
        $sql = "select * FROM ".self::TABLE_NAME." where UnitId='".$sUnitID."'";
        return self::query($sql);
    }

}