<?php
/**
 * CricUser 手机api
 * @author chasel
 *
 */
class Model_CricSnsUser
{
    const TABLE_NAME = 'SnsAccount';

    private $msdbh;

    public function __construct ()
    {
        $this->msdbh = Util_Common::getMsSQLDB('FangJiaDpUser', 'master');
    }

    public function GetSnsAccountWithIdAndSnsName($uid, $name)
    {
        $uid = addslashes($uid);
        $name = addslashes($name);

        $sSQL = "SELECT * FROM SnsAccount WHERE userId = '$uid' AND SnsName= '$name' AND State = 1";

        return $this->msdbh->getRow($sSQL);
    }

    public function addUser($snsName, $openID, $userName, $sex, $userId){
        $createTime = date("Y-m-d H:i:s");
        $sSQL = "INSERT INTO SnsAccount(snsId,snsName,snsOpenId,userName,userAvatar,userSex,createDate,userId,state) values(NEWID(),'$snsName','$openID','$userName','',$sex,'$createTime','$userId',1)";
        $this->msdbh->query($sSQL);
    }

}