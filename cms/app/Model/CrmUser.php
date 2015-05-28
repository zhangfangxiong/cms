<?php

class Model_CrmUser extends Model_Base
{

    const DB_NAME = 'crm';

    const TABLE_NAME = 'user';


    public static function addUser($aParam)
    {

        $aParam['last_update'] = $aParam['create_datetime'] = date('Y-m-d H:i:s');
        //$aParam['serial_number'] = self::createSerialNumber($aParam['city']);
        return self::addData($aParam);

    }

    public static function createSerialNumber($city_id) {

        $short = Model_CrmCity::getOne(array(
            'where' => array(
                'id'=> $city_id,
            )
        ),'short');
        $count = self::query("select count(*) as num from user where city=".$city_id." and serial_number like '%".date('Ymd')."%'",'one');
        return $short.date('Ymd').sprintf("%04d", ($count+1));
    }
}