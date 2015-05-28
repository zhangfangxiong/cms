<?php

/**
 * Created by PhpStorm.
 * User: cjj
 * Date: 15/4/7
 * Time: 下午1:44
 */
define("SCRIPT_PATH",'http://'.Yaf_G::getConf('static', 'domain'));

class Model_Bundle
{

    public static function  getHighchartScripts()
    {
        $scripts = array(
                "highcharts/highcharts.js",
                "highcharts/highcharts.ext.js"
        );
        return  self::getHtml($scripts);
    }

    public static function  getSwitchableScripts()
    {
        $scripts = array(
            "switchable.js",
        );
        return  self::getHtml($scripts);
    }

    public static  function getHtml($arr)
    {
        $html = '';
        foreach ($arr as $item) {
            $html .= "<script src=\"".SCRIPT_PATH."/js/web/".$item."\"></script>\n\r";
        }
        return $html;
    }


}
