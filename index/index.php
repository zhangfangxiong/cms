<?php
$sIndexPath = __DIR__;
$sHost = $_SERVER['HTTP_HOST'];
$aHost = explode('.', $sHost);
define('ENV_CHANNEL', array_shift($aHost));
if (count($aHost) == 4) {
    define('ENV_CLOUDNAME', $aHost[0]);
    define('ENV_SCENE', $aHost[1]);
    define('STATIC_VERSION', '');
    define("APP_PATH", '/data/www/' . ENV_CLOUDNAME . '/' . ENV_CHANNEL . '/app');
    define("LIB_PATH", '/data/www/' . ENV_CLOUDNAME . '/' . ENV_CHANNEL . '/library');
} else {
    define('ENV_CLOUDNAME', '');
    if (isset($_COOKIE['fjdp_version']) && $_COOKIE['fjdp_version'] == 'beta') {
        define('ENV_SCENE', 'beta');
    } else {
        define('ENV_SCENE', 'ga');
    }

    $sVersionFile = $sIndexPath.'/version/'.ENV_CHANNEL.'.' . ENV_SCENE;

    if (!file_exists($sVersionFile)) {
        die('No version for ' . ENV_CHANNEL);
    }

    define('VERSION', trim(file_get_contents($sVersionFile)));

    define("APP_PATH", '/data/app/wwwroot/' . ENV_CHANNEL . '/' . VERSION . '/app');
    define("LIB_PATH", '/data/app/wwwroot/' . ENV_CHANNEL . '/' . VERSION . '/library');

    $sStaticVersionFile = $sIndexPath . '/version/static.' . ENV_SCENE;
    if (! file_exists($sStaticVersionFile)) {
        die('File ' . $sStaticVersionFile . ' not found!');
    }

    define('STATIC_VERSION', trim(file_get_contents($sStaticVersionFile)));
    header('fjdp: v='. VERSION);
}

define('ENV_DOMAIN', join('.', $aHost));
define("CONF_PATH", $sIndexPath . '/config/');

try {
    require_once LIB_PATH . '/loader.php';
    $app = new Yaf_Application();
    $app->bootstrap()->run();
} catch (Exception $e) {
    echo $e->getMessage();
}
