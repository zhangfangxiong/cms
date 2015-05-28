<?php
//登录cookie配置
$config['cookie']['prefix'] = 'fjdp';
$config['cookie']['authkey'] = 'auth';
$config['cookie']['cryptkey'] = 'kdi##20.83(&%$63ldwl';

//URL规则
$config['route']['type'] = 'static';
$config['route']['rewrite'] = array();

$config['sdkdomain']["cms"] = 'testapi.dev.ipo.com';


//日志配置
$config['logger']['path'] = '/data/log';

$config['logger']['api'] = array(
    'sSplitType' => 'day',
    'sDir' => 'api'
);

$config['route']['type']    = 'static';
$config['route']['rewrite'] = array(
    '/^(\/?)$/i' => '/h5/index/index',

);
//    '/^(?<city>[a-z]+)\/news\/index\/previewcarousel(\/?)/i' => '/news/index/previewcarousel',
//    '/^(?<city>[a-z]+)\/news\/index\/previewadv(\/?)/i' => '/news/index/previewadv',
//    '/^(?<city>[a-z]+)\/news\/index(\/)?(?<page>\d+)?(\/?)/i' => '/news/index/index',
//    '/^(?<city>[a-z]+)\/news\/list\/tag\/(?<tagId>\d+)(\/)?(?<page>\d+)?(\/?)/i' => '/news/index/list',
//    '/^(?<city>[a-z]+)\/news\/detail\/(?<iNewsID>\d+)(\/?)/i' => '/news/index/detail',
//    '/^(?<city>[a-z]+)\/news\/detail\/preview(\/?)/i' => '/news/index/previewdetail',
//
//    '/^(?<city>[a-z]+)\/h5\/Evaluation\/(\/?)/i' => '/h5/Evaluation/',
//    '/^(?<city>[a-z]+)\/h5\/Evaluation\/index(\/?)/i' => '/h5/Evaluation/index',
//    '/^(?<city>[a-z]+)\/h5\/Evaluation\/hxanalyseIndex(\/?)/i' => '/h5/Evaluation/hxanalyseIndex',
//    '/^(?<city>[a-z]+)\/h5\/Evaluation\/hxanalyseHx(\/?)/i' => '/H5/Evaluation/hxanalyseHx',
//    '/^(?<city>[a-z]+)\/h5\/Evaluation\/zxstandardIndex(\/?)/i' => '/H5/Evaluation/zxstandardIndex',
//    '/^(?<city>[a-z]+)\/h5\/Evaluation\/zxstandardAnalysis(\/?)/i' => '/H5/Evaluation/zxstandardAnalysis',
//
//    '/^(?<city>[a-z]+)\/h5\/Evaluation\/sqpzIndex(\/?)/i' => '/H5/Evaluation/sqpzIndex',
//    '/^(?<city>[a-z]+)\/h5\/Evaluation\/sqpzScenery(\/?)/i' => '/H5/Evaluation/sqpzScenery',
//    '/^(?<city>[a-z]+)\/h5\/Evaluation\/sqpzBuild(\/?)/i' => '/H5/Evaluation/sqpzBuild',
//    '/^(?<city>[a-z]+)\/h5\/Evaluation\/sqpzPublic(\/?)/i' => '/H5/Evaluation/sqpzPublic',
//    '/^(?<city>[a-z]+)\/h5\/Evaluation\/sqpzConfig(\/?)/i' => '/H5/Evaluation/sqpzConfig',
//    '/^(?<city>[a-z]+)\/h5\/Evaluation\/sqpzParking(\/?)/i' => '/H5/Evaluation/sqpzParking',
//
//    '/^(?<city>[a-z]+)\/h5\/Evaluation\/wyfwIndex(\/?)/i' => '/H5/Evaluation/wyfwIndex',
//    '/^(?<city>[a-z]+)\/h5\/Evaluation\/wyfwService(\/?)/i' => '/H5/Evaluation/wyfwService',
//
//    '/^(?<city>[a-z]+)\/h5\/Evaluation\/trafficIndex(\/?)/i' => '/H5/Evaluation/trafficIndex',
//    '/^(?<city>[a-z]+)\/h5\/Evaluation\/trafficRail(\/?)/i' => '/H5/Evaluation/trafficRail',
//    '/^(?<city>[a-z]+)\/h5\/Evaluation\/trafficBus(\/?)/i' => '/H5/Evaluation/trafficBus',
//
//    '/^(?<city>[a-z]+)\/h5\/Evaluation\/regionIndex(\/?)/i' => '/H5/Evaluation/regionIndex',
//    '/^(?<city>[a-z]+)\/h5\/Evaluation\/regionEducate(\/?)/i' => '/H5/Evaluation/regionEducate',
//    '/^(?<city>[a-z]+)\/h5\/Evaluation\/regionMedical(\/?)/i' => '/H5/Evaluation/regionMedical',
//    '/^(?<city>[a-z]+)\/h5\/Evaluation\/regionBusiness(\/?)/i' => '/H5/Evaluation/regionBusiness',
//    '/^(?<city>[a-z]+)\/h5\/Evaluation\/regionPublic(\/?)/i' => '/H5/Evaluation/regionPublic',
//
//    '/^(?<city>[a-z]+)\/h5\/Evaluation\/badfactorIndex(\/?)/i' => '/H5/Evaluation/badfactorIndex',
//    '/^(?<city>[a-z]+)\/h5\/Evaluation\/badfactorOutside(\/?)/i' => '/H5/Evaluation/badfactorOutside',
//
//    '/^(?<city>[a-z]+)\/h5\/news\/detail\/(?<iNewsID>\d+)(\/?)/i' => '/H5/News/detail',
//
//    '/^(?<city>[a-z]+)\/h5\/guid\/index\/(?<iPage>\d+)(\/?)/i' => '/H5/News/detail',
//);


return $config;