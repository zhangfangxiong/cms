<?php

class Model_Project extends Model_Base
{

    const DB_NAME = 'permission';

    const TABLE_NAME = 't_project';
    
    /**
     * 取得所有的项目
     * @return multitype:multitype:unknown
     */
    public static function getAllProject ()
    {
        $aRet = array();
        $aList = self::getAll(array('where' => array('iStatus' => 1)));
        foreach ($aList as $k => $v) {
            $aRet[$v['iProjectID']] = array('sProjectName' => $v['sProjectName'], 'sUrl' => $v['sUrl']);
        }
        return $aRet;
    }
}