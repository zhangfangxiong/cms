<?php

/**
 * Created by PhpStorm.
 * User: cjj
 * Date: 15/4/7
 * Time: 下午1:44
 */
class Model_HomeNewReport
{



    public static  function getHomeNewReport($homeId)
    {
        if (empty($homeId)) {
            return null;
        }
        $result = Sdk_Cms_Newhouse::getHomeNewReport($homeId);
        if (!empty($result['data']) && $result['status'] == 1) {
           return $result['data'];
        }
        return null;
    }


}