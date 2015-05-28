<?php

class Model_CricCityPhoneNumber extends Model_Base
{
    const DB_NAME = 'cric_xf';

    const TABLE_NAME = 'CityPhoneNumber';

    /*
     *
     * 获取城市400的分机号
     */
    public static function getCity400($CityName,$RegionName)
    {
        if (empty($CityName)) {
            return '';
        }
        $sql = "SELECT * FROM CityPhoneNumber WHERE City ='".$CityName."'";
        $result = self::query($sql);
        if (!empty($result)) {
            $city400 = '';
            foreach($result as $item) {
                if (!empty($RegionName) && $item['RegionName'] == $RegionName) {
                    $city400 =  $item['ExtNumber'];
                    break;
                }
            }
            if ($city400!='') {
                return $city400;
            }
            return $result[0]['ExtNumber'];
        }
        return '';
    }

}