<?php
/**
 * Created by PhpStorm.
 * User: yaobiqing
 * Date: 15/4/8
 * Time: 下午3:10
 */


class Model_CricBatchTypeFullNew extends Model_Base
{

    const TABLE_NAME = 'BatchTypeFullNew';
    const DB_NAME = 'cric_xf';

    public static  function GetBatchTypeNewList($HomeId)
    {
        $sql = "SELECT * FROM dbo.BatchTypeFullNew WHERE BuildingID = '".$HomeId."' AND State = 1";
        return self::query($sql);
    }

}