<?php
/**
 * 短信 add by cjj
 */
class Model_SendSms extends Model_Base
{


    public static function send($phone,$Msg)
    {
        if (empty($phone)) {
            return;
        }
        $smsConfig =  Yaf_G::getConf('sms');
        if (empty($smsConfig)) {
            return;
        }
        $client = new SoapClient($smsConfig['url']);
        $param = array("userCode"=> $smsConfig['account']['sUserCode'],
                        "userPass"=> $smsConfig['account']['sUserPass'],
                        "DesNo"=> $phone,
                        "Msg"=> $Msg,
                        "Channel"=>$smsConfig['channel']);
        $p = $client->__soapCall('SendMes',array('parameters' => $param));
        //print_r($p);die;

    }


}