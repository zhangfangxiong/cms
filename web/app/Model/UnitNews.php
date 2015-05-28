<?php

/**
 * Created by PhpStorm.
 * User: cjj
 * Date: 15/4/7
 * Time: 下午1:44
 */
class Model_UnitNews
{

    // 楼盘动态
    public static function getUnitNewsFromLeju($iUnitId,$cityInfo,$page,$pageSize,$filter)
    {

        if (empty($iUnitId)) {
            return null;
        }
        $postData  = array(
            'appid' => "2014082702",
            'type' => "new_news",
            'format' => "json",
            'page' => $page,
            'pcount' => $pageSize,
            'count' => 1,
            'filter1' => "{c_id@eq}5894705310651984173",
            'filter2' => "{p_id@eq}" . $cityInfo['CMSCityID'],
            //c_id  屏道     p_id 项目   city  城市
            'filter3' =>  "{hid@eq}" . $iUnitId,// + ,102452
            'filter4' => "{deleted@eq}0",
            'filter5' => "{t_id@neq}" .$cityInfo['CMSMBID'],
            'Filter6' => $filter,
            'order' => "{createtime}desc",
            'ver' => "2.0",
        );
        $key = 'fed944d93182db84b85db30d6cbfafdf';
        $postData['sign'] = implode('',$postData).$key;
        $postData['sign'] = strtoupper(md5($postData['sign']));
        $url = 'http://info.leju.com/search/default/index';
        $http = new Util_HttpClient();
        $code = $http->post($url,$postData);
        if ($code == 200) {
            return json_decode($http->content,true);
        }
        return null;
    }



}