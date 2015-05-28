<?php
//登录cookie配置
$config['cookie']['prefix'] = 'fjdp';
$config['cookie']['authkey'] = 'auth';
$config['cookie']['cryptkey'] = 'kdi##20.83(&%$63ldwl';

//URL规则
$config['route']['type'] = 'static';
$config['route']['rewrite'] = array();

//项目配置
$config['project']['id'] = 1;
$config['project']['name'] = 'CMS';
// 短信配置
$config['sms']['account'] = array(
    'sUserCode' => 'shfwxs',
    'sUserPass' => 'shfwxs588',
);

$config['xinfang']['priceTrends'] = [
    1 => '跌',
    2 => '涨',
    3 => '平'

];

$config['xinfang']['unitImgType'] = [
    1 => '实景图',
    2 => '效果图',
    3 => '总平图',
    4 => '外立面',
    5 => '周边图'
];

$config['sms']['url'] = 'http://yes.itissm.com/api/MsgSend.asmx?WSDL';
$config['sms']['channel'] = 78;


$config['logger']['evaluation'] = array(
    'sSplitType' => 'day',
    'sDir' => 'evaluation',
    'iLevel' => 0
);

$config['verifySms']['account'] = [
    'sUserCode' => 'shfwxs',
    'sUserPass' => 'shfwxs123'
];
$config['verifySms']['url'] = 'http://h.1069106.com:1210/Services/MsgSend.asmx';
$config['verifySms']['channel'] = 1;


return $config;