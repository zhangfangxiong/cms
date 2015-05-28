<?php

class Model_CrmCity extends Model_Base
{

    const DB_NAME = 'crm';

    const TABLE_NAME = 'mst_city';


    /**
     * 取得所有启用城市
     */
    public static function geAllCitys()
    {
        return self::getAll(array(
            'where' => array(
                'delete_flg' => 0,
            ),
        ));
    }

}