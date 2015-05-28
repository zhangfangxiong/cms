<?php

/**
 * Created by PhpStorm.
 * User: xiaoyao
 * Date: 2015/1/26
 * Time: 9:27
 */
class Model_Question extends Model_Base
{
    const DB_NAME = 'bd_qa';

    const TABLE_NAME = 't_question';

    const WHERE_CACHE = 0;

    public static function updateByID($questionID, $type, $param)
    {
        $row = self::getRow(array('where'=>array('sQuestionId' => $questionID, 'iQuestionType' => $type)));
        if(!empty($row)){
            $id = $row['iAutoID'];
            $param['iAutoID'] = $id;
            return self::updData($param);
        }

        return 0;
    }

    public static function batchChangeCity($questionID, $city)
    {
        $questionID = explode(',', $questionID);
        $error = 0;
        foreach($questionID as $id) {
            $error = self::updData(['iAutoID'=> $id,'sChangedCity'=>$city]);
        }
        return $error;
    }



}