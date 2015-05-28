<?php

class Model_LoupanPic extends Model_Base
{
    const DB_NAME = 'xinfang';

    const TABLE_NAME = 't_loupan_pic';

    /*
     * 根据楼盘id获取图片
     */
    public static function getPicsByBID($bLoupanID)
    {
        $where = array(
            'iLoupanID' => $bLoupanID,
            'iStatus' => 1
        );

        return self::getAll(['where' => $where]);
    }


}