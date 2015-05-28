<?php
//登录cookie配置
$config['cookie']['prefix'] = 'fjdp';
$config['cookie']['authkey'] = 'auth';
$config['cookie']['cryptkey'] = 'kdi##20.83(&%$63ldwl';

//URL规则
$config['route']['type'] = 'static';
$config['route']['rewrite'] = array();

//日志配置
$config['logger']['path'] = '/data/log';

$config['logger']['api'] = array(
    'sSplitType' => 'day',
    'sDir' => 'api'
);

$config['route']['type']    = 'static';
$config['route']['rewrite'] = array(
    '/^(?<city>[a-z]+)\/Newhouse\/index(\/?)/i' => '/Newhouse/index/',
    '/^(?<city>[a-z]+)\/Newhouse\/detail(\/?)/i' => '/Newhouse/detail/',
    '/^(?<city>[a-z]+)\/Newhouse\/detail\/getUnitDynamic(\/?)/i' => '/Newhouse/detail/getUnitDynamic',
    '/^(?<city>[a-z]+)\/Newhouse\/detail\/getUnitRooms(\/?)/i' => '/Newhouse/detail/getUnitRooms',
    '/^(?<city>[a-z]+)\/Newhouse\/detail\/getRoomPrice(\/?)/i' => '/Newhouse/detail/getRoomPrice',
    '/^(?<city>[a-z]+)\/Newhouse\/Price(\/?)/i' => '/Newhouse/Price',
    '/^(?<city>[a-z]+)\/Analysts(\/?)/i' => '/Analysts/index/',
//   '/^(?<city>[a-z]+)\/(?<letter>[a-z])/' => '/Index/index/letterSearch/',
    '/^(?<city>[a-z]+)\/(?<letter>[a-z])$/' => '/Index/index/letterSearch/',
    '/^(?<city>[a-z]+)\/([a-z]+)\/([a-z]+)\/(?<unitid>[0-9]+)_comment$/' => '/Newhouse/Comment/index/',
    '/^(?<city>[a-z]+)\/Newhouse\/Loudetail(\/?)/i' => '/Newhouse/Loudetail/',
    '/^(?<city>[a-z]+)\/Newhouse\/discount(\/?)/i' => '/Newhouse/discount/',
    '/^change\/(?<city>[a-z]+)/i' => '/City/Change/',
    '/^(?<city>[a-z]+)\/xinfang/i' => '/Map/Index/',
    '/^(?<city>[a-z]+)\/zufang/i' => '/Map/Index/',
    '/^(?<city>[a-z]+)\/esfang/i' => '/Map/Index/',

);
$config['newsUrl'] = 'http://news.fangjiadp.com';
$config['400Tel'] = '400-810-6999';
return $config;