<?php
/**
 * CricUser 手机api
 * @author chasel
 *
 */
class Model_CricUser
{
    const TABLE_NAME = 'DpUser';

    private $msdbh;


    public function __construct ()
    {
        $this->msdbh = Util_Common::getMsSQLDB('FangJiaDpUser', 'master');
    }

    public function addUser($mobile, $passwd, $city)
    {
        $mobile = addslashes($mobile);
        $passwd = addslashes($passwd);
        $city = addslashes($city);

        $sSQL = "INSERT INTO DpUser(UserId,UserName,Phone,Password,City) VALUES (NEWID(), '$mobile', '$mobile', '$passwd', '$city');";

        return $this->msdbh->query($sSQL);
    }

    public function addThirdUser($userName, $sex, $city, $openID, $source)
    {
        $userName = addslashes($userName);
        $city = addslashes($city);
        $openID = addslashes($openID);
        $source = addslashes($source);

        $time = date('Y-m-d H:i:s');
        $sSQL = "INSERT INTO DpUser(UserId,UserName,Sex,City,Sourse,LoginDate,OpenId,UserType) VALUES (NEWID(), '$userName', $sex, '$city', '$source','$time','$openID',4);";

        return $this->msdbh->query($sSQL);
    }

    public function getOneChecked($mobile, $passwd) {
        $mobile = addslashes($mobile);
        $passwd = addslashes($passwd);

        $result = $this->msdbh->getRow("SELECT * FROM DpUser where Password='$passwd' and (UserName = '$mobile' or Phone = '$mobile') and State = 1");
        return $result;
    }

    public function updateUser($data, $where='')
    {
        $result = $this->msdbh->update(self::TABLE_NAME, $data, $where);
        return $result;
    }

    public function changePwd($mobile, $pwd)
    {
        $mobile = addslashes($mobile);
        $pwd = addslashes($pwd);
        $sSQL = "update DpUser set Password ='". $pwd. "' where Phone = '".$mobile. "' and State = 1";
        return $this->msdbh->query($sSQL);
    }

    public function changePwdByID($userID, $Pwd)
    {
        $Pwd = addslashes($Pwd);
        $sSQL = "update DpUser set Password ='". $Pwd. "' where Id =".$userID. " and State = 1";

        return $this->msdbh->query($sSQL);
    }

    public function getUserByName($userName)
    {
        $usserName = addslashes($userName);
        $sql = 'select * from '. self::TABLE_NAME. " where UserName = '$userName' or Phone = '$userName' and State = 1";
        return $this->msdbh->getRow($sql);
    }

    public function getUserById($userId)
    {
        $sql = 'select * from '. self::TABLE_NAME. " where Id = $userId";
        return $this->msdbh->getRow($sql);
    }

    public function getUserByOpenID($openID)
    {
        $sql = 'select * from '. self::TABLE_NAME. " where OpenId = '$openID' and State = 1";
        return $this->msdbh->getRow($sql);
    }

    public static function genPasswd($userName, $passwd)
    {
        $http = new Util_HttpClient();
        $url = 'http://admin.fangjia.cric.com/DpUser/EncryptDpPwd';
        $code = $http->post($url, array('userName'=> $userName, 'passwd'=> $passwd));

        $pwd = null;
        if ($code == 200) {
            $result = json_decode($http->content,true);

            if(!$result['code']) {
                $pwd = $result['password'];
            }
        }

        return $pwd;
    }

    /*
     * 完善用户信息
     */
    public function improve($userName, $passwd, $city, $userID)
    {
        $userName = addslashes($userName);
        $city = addslashes($city);
        $sSQL = "update ".self::TABLE_NAME." set Phone ='" . $userName . "',Password = '" . $passwd . "',City = '" . $city . "' where Id = " . $userID . " and State = 1";
        return $this->msdbh->query($sSQL);
    }

    /*
     * 绑定手机号
     */
    public function changePhone($phone, $userID)
    {
        $phone = addslashes($phone);

        $sSQL = "update ".self::TABLE_NAME." set Phone ='" . $phone . "' where Id = " . $userID . " and State = 1";
        return $this->msdbh->query($sSQL);
    }

    //房价点评网用户检测 author:ddc
    public function checkUser($mobile){
        $mobile = addslashes($mobile);
        $result = $this->msdbh->getRow("SELECT count(*) as count FROM ".self::TABLE_NAME." where (UserName = '$mobile' or Phone = '$mobile') and State = 1");
        return $result;
    }

    //房价点评网个人用户中心
    public function getUserInfo($userName){
        $userName = addslashes($userName);
        $result = $this->msdbh->getRow("SELECT * FROM ".self::TABLE_NAME." where (UserName = '$userName' or Phone = '$userName') and State = 1");
        return $result;
    }

    //房价点评网更新个人信息
    public function updateUserInfo($array){
        $mobile = addslashes($array['mobile']);
        $realName = addslashes($array['realName']);
        $sex = $array['sex']=='M'? '0':'1';
        $budget = addslashes($array['budget']);
        $buyInfo = addslashes($array['buyInfo']);
        $birthday = addslashes($array['birthday']);
        $sSQL = "update ".self::TABLE_NAME." set RealName ='" . $realName . "',Sex = '" . $sex . "',HouseBudget = '" . $budget . "',
                                             BuyInfo = '" . $buyInfo . "',Birth = '" . $birthday . "'
              where UserName = '" . $mobile . "' and State = 1";
        return $this->msdbh->query($sSQL);
    }

    //设置用户头像
    public function setAvatar($userID, $avatar){
        $sSQL = "update ". self::TABLE_NAME. " set HeadImg ='". $avatar. "' where Id =".$userID. " and State = 1";
        return $this->msdbh->query($sSQL);
    }
}