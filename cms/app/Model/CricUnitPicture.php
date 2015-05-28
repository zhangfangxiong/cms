<?php

class Model_CricUnitPicture extends Model_Base
{

    const DB_NAME = 'cric_xf';

    const TABLE_NAME = 'Unit_Picture';



    public static function getUnitPictures($unitID)
    {

        $sql = "SELECT * FROM Unit_Picture  WHERE UnitID = '".$unitID."' AND (PictureCode<>null or PictureCode<>'' ) AND (PictureType ='xgt' OR PictureType ='zpt'OR PictureType ='sjt' OR PictureType='ybft')";
        $result =  self::query($sql);
        if (!empty($result)) {
            foreach ($result as &$item) {
                $item['sImage'] =  Util_Uri::getCricViewURL($item['PictureCode'],600,450);
            }
        }
        return $result;
    }


}