<?php

/**
 * Created by PhpStorm.
 * User: chasel
 * Date: 2015/4/26
 * Time: 9:27
 */
class Model_QuestionLog extends Model_Base
{
    const DB_NAME = 'bd_qa';

    const TABLE_NAME = 't_log';

    /**
     * 百度问答-操作日志
     */
    public static function writeLog($questionIDs, $operate, $content, $isSuccess = 1)
    {
        $aCurrUser = Util_Cookie::get(Yaf_G::getConf('authkey', 'cookie'));

        $user = Model_User::getDetail($aCurrUser['iUserID']);
        $data = array(
            'sQuestionIDs' => $questionIDs,
            'operate' => $operate,
            'sContent' => $content,
            'iUserId' => $aCurrUser['iUserID'],
            'sUserName' => $aCurrUser['sUserName'],
            'sRealName' => $aCurrUser['sRealName'],
            'iCityID' => $user['iCityID'],
            'iIsSuccess' => $isSuccess,
            'iCreateTime' => time(),
            'iUpdateTime' => time()
        );

        Model_QuestionLog::addData($data);
    }

}