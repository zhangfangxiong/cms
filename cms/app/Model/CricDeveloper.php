<?php

class Model_CricDeveloper extends Model_Base
{
    const DB_NAME = 'cric_xf';

    const TABLE_NAME = 'Developer';



    public static function addDeveloperInfo($param)
    {
        $sql = "INSERT INTO Developer( UnitId ,UnitName ,DeveloperName ,LogoPicture ,Comment ,ContactName ,ContactPhone ,State ,CreateTime,AuditTime)
              VALUES  (
               '".addslashes($param['UnitId'])."',
              '".addslashes($param['UnitName'])."',
               '".addslashes($param['DeveloperName'])."',
               '".addslashes($param['LogoPicture'])."',
               '".addslashes($param['Comment'])."',
               '".addslashes($param['ContactName'])."',
              '".addslashes($param['ContactPhone'])."',
              0 ,
              '".date('Y-m-d H:i:s')."',
              '1900-01-01')";
        return self::query($sql);
    }



}