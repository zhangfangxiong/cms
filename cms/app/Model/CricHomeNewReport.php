<?php

class Model_CricHomeNewReport extends Model_Base
{

    const DB_NAME = 'cric_xf';

    const TABLE_NAME = 'Home_NewReport';

    public static function getHomeNewReport($sHomeId)
    {
        if (empty($sHomeId)) {
            return null;
        }
        $sql = "SELECT * FROM Home_NewReport WHERE HomeId = '".$sHomeId."' AND State = 1 AND FileId <> '' ORDER BY RANK";
        return self::query($sql);
    }
}