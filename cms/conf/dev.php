<?php
//Permission数据库配制
$config['database']['permission']['master'] = array(
		'dsn' => 'mysql:host=127.0.0.1;dbname=permission',
		'user' => 'root',
		'pass' => 'xjc.123',
		'init' => array(
			'SET CHARACTER SET utf8',
			'SET NAMES utf8'
		)
);
$config['database']['permission']['salve'] = array(
    'dsn' => 'mysql:host=127.0.0.1;dbname=permission',
    'user' => 'root',
    'pass' => 'xjc.123',
    'init' => array(
        'SET CHARACTER SET utf8',
        'SET NAMES utf8'
    )
);

//CMS数据库配制
$config['database']['cms']['master'] = array(
    'dsn' => 'mysql:host=127.0.0.1;dbname=cms',
    'user' => 'root',
    'pass' => 'xjc.123',
    'init' => array(
        'SET CHARACTER SET utf8',
        'SET NAMES utf8'
    )
);
$config['database']['cms']['salve'] = array(
    'dsn' => 'mysql:host=127.0.0.1;dbname=cms',
    'user' => 'root',
    'pass' => 'xjc.123',
    'init' => array(
        'SET CHARACTER SET utf8',
        'SET NAMES utf8'
    )
);
//crm数据库配制
$config['database']['crm']['master'] = array(
    'dsn' => 'mysql:host=127.0.0.1;dbname=crm',
    'user' => 'root',
    'pass' => 'xjc.123',
    'init' => array(
        'SET CHARACTER SET utf8',
        'SET NAMES utf8'
    )
);
$config['database']['crm']['salve'] = array(
    'dsn' => 'mysql:host=127.0.0.1;dbname=crm',
    'user' => 'root',
    'pass' => 'xjc.123',
    'init' => array(
        'SET CHARACTER SET utf8',
        'SET NAMES utf8'
    )
);

$config['database']['hotline']['master'] = array(
    'dsn' => 'mysql:host=127.0.0.1;dbname=hotline',
    'user' => 'root',
    'pass' => 'xjc.123',
    'init' => array(
        'SET CHARACTER SET utf8',
        'SET NAMES utf8'
    )
);
$config['database']['hotline']['salve'] = array(
    'dsn' => 'mysql:host=127.0.0.1;dbname=hotline',
    'user' => 'root',
    'pass' => 'xjc.123',
    'init' => array(
        'SET CHARACTER SET utf8',
        'SET NAMES utf8'
    )
);

//百度问答数据库配制
$config['database']['bd_qa']['master'] = array(
    'dsn' => 'mysql:host=127.0.0.1;dbname=bd_qa',
    'user' => 'root',
    'pass' => 'xjc.123',
    'init' => array(
        'SET CHARACTER SET utf8',
        'SET NAMES utf8'
    )
);
$config['database']['bd_qa']['salve'] = array(
    'dsn' => 'mysql:host=127.0.0.1;dbname=bd_qa',
    'user' => 'root',
    'pass' => 'xjc.123',
    'init' => array(
        'SET CHARACTER SET utf8',
        'SET NAMES utf8'
    )
);

//SFTP配置
$config['sftp']['topic'] = array(
    "host"          => "172.28.28.73",
    "user"          => "www",
    "port"          => "22",
    "pubkey_file"  => "/data/www/.ssh/id_rsa.pub",
    "privkey_file" => "/data/www/.ssh/id_rsa",
);
$config['topic']['path'] = "/data/www/topic/";

$config['cricEvaluationUrl'] = 'http://cric.yiju.org/NBXTi/CMSReport.ashx';
$config['cityPhone'] = array(
        'shanghai' => '18964842042', //
        'xiamen' => '18650003217',
        'fuzhou' => '13774505684',
        'hangzhou'=> array('13641984576','13588725577'),
        'ningbo' => '13736016644',
        'wuxi'   => '13961822214',
        'nanjing' => '13814210001',
        'hefei' => '17756083200',
        'suzhou'=> '15862478550',
        'changzhou' => '13916909276',
        'changsha' => '18602109016',
        'guangzhou' => '15920157995',
        'shenzhen' => array('18019563166','18688989810'),
        'nanning' => '13916189575',
        'haikou' => '18559313377',
        'wuhan' => '13720307700',
        'zhengzhou' => '18603851717',
        'beijing' => '18610096080',
        'qindao' => '18561977650',
        'tianjin'=> '18622129538',
        'changchun' => '13514461310',
        'shenyang' => '13700056200',
        'dalian' => '18641189985',
        'jinan' => '18602171171',
        'haerbin' => '15904604317',
        'chengdu' => '13018261777',
        'chongqing' => '18623333797',
        'xian' => '15829072663',
        'guiyang' => '13671906177',
        'kunming' => '13301631113',
);

//CRIC转换前的数据库配制
$config['mssql']['FangJiaDpUser'] = array(
    'host' => '172.28.28.40\sql2008',
    'user' => 'sa',
    'pass' => '1qaz!QAZ',
    'dbname' => 'FangJiaDpUser'
);

$config['database']['cric_xf'] = array(
    'dsn' => 'mysql:host=127.0.0.1;dbname=cric_xf',
    'user' => 'root',
    'pass' => 'xjc.123',
    'init' => array(
        'SET CHARACTER SET utf8',
        'SET NAMES utf8'
    )
);
$config['database']['xinfang']['master'] = array(
    'dsn' => 'mysql:host=127.0.0.1;dbname=xinfang',
    'user' => 'root',
    'pass' => 'xjc.123',
    'init' => array(
        'SET CHARACTER SET utf8',
        'SET NAMES utf8'
    )
);
$config['database']['xinfang']['slave'] = array(
    'dsn' => 'mysql:host=127.0.0.1;dbname=xinfang',
    'user' => 'root',
    'pass' => 'xjc.123',
    'init' => array(
        'SET CHARACTER SET utf8',
        'SET NAMES utf8'
    )
);

return $config;