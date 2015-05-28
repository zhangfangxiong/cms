<?php

/**
 * Created by PhpStorm.
 * User: cjj
 * Date: 15/4/7
 * Time: 下午1:44
 */
class Model_Util
{

    public static $cityCode;

    public static function setCityCode($cityCode)
    {
        self::$cityCode = $cityCode;
    }


    /**
     * @param $unitd
     * @param int $type
     * @param string $control control名
     * @return string
     */
    public static function getDetailUrl($unitd,$type=1,$control='detail')
    {
        if($type==2 && !empty($unitd)) {
           $info = Sdk_Cms_Xfsearch::getIdByUnitId($unitd);
            return '/' . self::$cityCode . '/Newhouse/'.$control.'?unitid=' . intval(isset($info['data']['ID'])?$info['data']['ID']:0);
        }else{
            return '/' . self::$cityCode . '/Newhouse/'.$control.'?unitid=' . intval($unitd);
        }
    }


    public static function getSearchUrl($searchParam)
    {

        $param = self::getSearchParam();
        if (!empty($searchParam)) {
            foreach ($searchParam as $k => $v) {
                if (isset($param[$k])) {
                    $param[$k] = $v;
                }
            }
        }
        return '/' . self::$cityCode . '/Newhouse/index?' . http_build_query($param);
    }

    public static function getShortSearchUrl($searchParam)
    {
        return '/' . self::$cityCode . '/Newhouse/index?' . http_build_query($searchParam);
    }

    public static function getSearchParam()
    {

        return array(
            'r' => isset($_GET['r']) ? addslashes($_GET['r']) : '', // 区域
            'd' => isset($_GET['d']) ? addslashes($_GET['d']) : '',// 地区
            'p' => isset($_GET['p']) ? addslashes($_GET['p']) : '', // 售价
            'f' => isset($_GET['f']) ? intval($_GET['f']) : 0,
            'prev' => isset($_GET['prev']) ? addslashes($_GET['prev']) : '', // 预算
            'rt' => isset($_GET['rt']) ? addslashes($_GET['rt']) : '', // 户型
            's' => isset($_GET['s']) ? addslashes($_GET['s']) : '', // 建议
            'ev' => isset($_GET['ev']) ? intval($_GET['ev']) : 0, // 是否有评测
            'pr' => isset($_GET['pr']) ? intval($_GET['pr']) : 0, // 是否有价目表
            'k' => isset($_GET['k']) ? addslashes($_GET['k']) : '',
            'st' => isset($_GET['st']) ? intval($_GET['st']) : 0,// 排序方式
            'ord' => isset($_GET['ord']) ? intval($_GET['ord']) : 0,// 价格 升 降
            'pg' => isset($_GET['pg']) ? intval($_GET['pg']) : 1, // 当前页

        );

    }

    // 替换URL的pg 参数值
    public static function replaceUrlPageParam($url, $page)
    {
        return preg_replace('/pg=\d+/u', 'pg=' . $page, $url);
    }

    public static function pageHtml($totalPage, $currentPage, $url, $displayNum = 6)
    {
        $output = $currentPage == 1
            ? "<span class=\"current prev\">上一页</span>"
            : "<a href=" . self::replaceUrlPageParam($url, $currentPage - 1) . " class=\"prev\">上一页</a>";
        $count = $displayNum;
        $neHalf = floor($count / 2);
        $upperLimit = $totalPage - $count;
        $page = $currentPage - 1;
        $start = $page > $neHalf ? max(min($page - $neHalf, $upperLimit), 0) : 0;
        $end = $page > $neHalf ? min($page + $neHalf + ($count % 2), $totalPage) : min($count, $totalPage);
        if ($start >= 1) {
            $output .= "<a href='" . self::replaceUrlPageParam($url, 1) . "'>1</a>";
            if ($start > 1) {
                $output .= "<span>...</span>";
            }
        }
        for ($i = $start; $i < $end; $i++) {
            $j = $i + 1;
            if ($j == $currentPage) {
                $output .= "<span class=\"current\">" . $j . "</span>";
            } else {
                $output .= "<a href='" . self::replaceUrlPageParam($url, $j) . "'>" . $j . "</a>";
            }
        }
        if ($end < $totalPage) {
            if ($totalPage - 1 > $end) {
                $output .= "<span>...</span>";
            }
            $output .= "<a href='" . self::replaceUrlPageParam($url, $totalPage) . "'>" . $totalPage . "</a>";
        }
        $output .= $currentPage == $totalPage ? "<span class=\"current next\">下一页</span>" : "<a href='" . self::replaceUrlPageParam($url,
                $currentPage + 1) . "' class='next'>下一页</a>";
        return $output;
    }

    /**
     * 图片处理
     */
    public static function getFormatImag($p_sFileKey, $p_iWidth = 0, $p_iHeight = 0, $p_sOption = '', $sBiz = '')
    {
        if (strpos($p_sFileKey, '.') !== false) {
            if ($p_iWidth == 1 && $p_iHeight == 1) {
                return Util_Uri::getDFSViewURL($p_sFileKey, 0, 0, $p_sOption, $sBiz);
            }
            return Util_Uri::getDFSViewURL($p_sFileKey, $p_iWidth, $p_iHeight, $p_sOption, $sBiz);
        }
        if (!empty($p_sFileKey)) {
            return Util_Uri::getCricViewURL($p_sFileKey, $p_iWidth, $p_iHeight);
        } else {
            return null;
        }
    }

    public static function getNewsHtml($url)
    {
        $url = Yaf_G::getConf('newsUrl') . '/' . $url;
        $http = new Util_HttpClient();
        $code = $http->get($url);
        if ($code == 200) {
            return $http->content;
        }
        return '';
    }

    public static function getFormatPrice($price)
    {
        if (empty($price)) {
            return '-';
        } else {
            return number_format($price);
        }
    }


    // 校正评级
    public static function ToCorrectLv($original)
    {
        $value = -1;
        switch ($original) {
            case "-1":
                $value = 0;
                break;
            case "0":
                $value = 4;
                break;
            case "1":
                $value = 3;
                break;
            case "2":
                $value = 2;
                break;
            case "3":
                $value = 1;
                break;
            default:
                $value = 0;
                break;
        }
        return $value;
    }

    public static function subString($str,$len)
    {
        if(mb_strlen($str) > $len) {
            return  mb_substr($str,0,$len).'...';
        } else {
            return $str;
        }
    }

    public static function getFormatDate($date)
    {
        return date('Y-m-d',strtotime($date));
    }


    // 通过评分获取星星
    public static function getStar($score)
    {

        $scoreLevelCssNew = "";
        $str = '';
        if ( $score > 0) {
            for ( $i = 1; $i <= 5; $i++) {
                $diffValue = $score - $i;
                $scoreLevelCssNew = "";
                if ($diffValue <= -1) {
                    $scoreLevelCssNew = "";
                } else if ($diffValue > -1 && $diffValue < 0) {
                    $scoreLevelCssNew = "c";
                } if ($diffValue >= 0) {
                    $scoreLevelCssNew = "o";
                }
                $str .= "<i class='".$scoreLevelCssNew."'></i>";
             }
            return $str;
        } else {
            return  '<i></i><i></i><i></i><i></i><i></i>';
        }
    }
}