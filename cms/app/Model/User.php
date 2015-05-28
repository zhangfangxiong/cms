<?php

class Model_User extends Model_Base
{

    const DB_NAME = 'permission';

    const TABLE_NAME = 't_user';

    public static function getUserByName ($sUserName)
    {
        return self::getRow(array(
            'where' => array(
                'sUserName' => $sUserName,
                'iStatus' => self::STATUS_VALID
            )
        ));
    }

    //显示当前作者能看到的城市
    public static function getCitiesByUser($iUserID)
    {
        $aUser = self::getDetail($iUserID);
        if (!empty($aUser)) {
            if ($aUser['sCityID'] == "-1") {
                return Model_City::getPairCitys();
            }
            $aUserCityID = explode(',',$aUser['sCityID']);
            return Model_City::getPairCitysByUser($aUserCityID);
        }
    }

    /*
     * 模糊查询用户
     * @author cjj
     */
    public static  function searchUserByName($sUserName)
    {
        if (empty($sUserName)) {
            return null;
        }
        $aWhere['sUserName LIKE'] = '%' . $sUserName . '%';
        $aWhere['iStatus'] = self::STATUS_VALID;

        $aParam = array(
            'where' => $aWhere,
        );
        return self::getOrm()->fetchAll($aParam);
    }
}