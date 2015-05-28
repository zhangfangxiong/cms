<?php
/**
 * 房价点评网
 * @author ddc
 *
 */
class Model_CricLiXiangJia extends Model_Base
{
    const DB_NAME = 'cric_xf';

    const TABLE_NAME = 'LiXiangJia';

    public static function saveLiXiangJia($array){
        $result = self::addData($array);
        if($result){
            return array('code'=>1);
        }else{
            return array('code'=>0);
        }
    }
}