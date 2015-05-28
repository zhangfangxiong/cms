<?php
/**
 * Created by PhpStorm.
 * User: yaobiqing
 * Date: 15/4/8
 * Time: 下午3:10
 */


class Model_CricBatchTypeBlock extends Model_Base
{

    const TABLE_NAME = 'BatchTypeBlock';
    const DB_NAME = 'cric_xf';


    public static function getBatchTypeBlockByUID($unitID)
    {
        $sql = "select Batch_Id, Block,block_id from ".self::TABLE_NAME."  where Unit_ID = '".$unitID."'";
        return self::query($sql);
    }


}