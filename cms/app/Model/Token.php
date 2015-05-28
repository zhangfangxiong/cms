<?php

class Model_Token extends Model_Base
{

    const DB_NAME = 'cms';

    const TABLE_NAME = 't_token';

    /*
    * 根据token值改变token状态
    * @author chasel
    */
    public static  function changStatus($token, $status)
    {
        $curentTime = time();
        $sql = "update t_token set iStatus = $status, iUpdateTime= $curentTime where sToken = '".addslashes($token)."'";

        return self::getOrm()->query($sql);
    }

    /*
    * 根据token值获得token详情
    * @author chasel
    */
    public static  function getToken($token)
    {
        $curentTime = time();
        $sql = "select * from t_token where sToken = '".addslashes($token)."' and iStatus = 1";

        return self::query($sql, 'row');
    }

    /*
        * 检查token值是否被篡改
        * @author chasel
        */
    public static function checkToken($token, $uid){
        if(empty($token) || empty($uid)) {
            return false;
        }

        $key = Yaf_G::getConf('token',null,'mapi');

        $token = Util_Crypt::decrypt($token, $key);
        $token = explode(',', $token);

        if(!empty($token) && !empty($token[2])) {
            if($token[2] == $uid) {
                return true;
            }
        }

        return false;
    }

    /*
        * 检查token值是否被篡改
        * @author chasel
        */
    public static function getMobileBYToken($token){
        $key = Yaf_G::getConf('token',null,'mapi');

        $token = Util_Crypt::decrypt($token, $key);
        $token = explode(',', $token);

        if(!empty($token) && !empty($token[0])) {
            return $token[0];
        }

        return false;
    }

}