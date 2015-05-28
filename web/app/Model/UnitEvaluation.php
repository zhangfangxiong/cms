<?php

/**
 * Created by PhpStorm.
 * User: cjj
 * Date: 15/4/7
 * Time: 下午1:44
 */
class Model_UnitEvaluation
{

    public static $eID = '';

    public static $evalSubLen = 38;
    // Sdk 中的 方法
    public static $chapter = array(
        1 => 'evaluationhxanalyse',
        2 => 'evaluationhxanalysehx',
        3 => 'zxstandardIndex',
        4 => 'getAnalysis',
        5 => 'sqpzIndex',
        6 => 'sqpzScenery',
        7 => 'sqpzBuild',
        8 => 'sqpzPublic',
        9 => 'sqpzConfig',
        10 => 'sqpzParking',
        11 => 'wyfwIndex',
        12 => 'wyfwService',
        13 => 'evaluationtrafficindex',
        14 => 'evaluationtrafficrail',
        15 => 'evaluationtrafficbus',
        16 => 'evaluationregionindex',
        17 => 'evaluationregioneducate',
        18 => 'evaluationregionmedical',
        19 => 'evaluationregionbusiness',
        20 => 'evaluationregionpublic',
        21 => 'evaluationbadfactor',
        22 => 'evaluationoutside',
    );

    public static function getEvaluationId($unitID)
    {
        if (empty(self::$eID)) {
            $result = Sdk_Cms_Evaluation::evaluationId($unitID);
            if ($result['status'] == 1) {
                self::$eID = $result['data'];
            }
        }
    }

    public static function evaluationChapter($unitID)
    {
        self::getEvaluationId($unitID);
        if (empty(self::$eID)) {
            return null;
        }
        // 章节分类
        $result = Sdk_Cms_Evaluation::getMenus(array('eID' => self::$eID));
        if (empty($result['data'])) {
            return null;
        }
        $parentMenu = array();
        $chapterData = array();
        if ($result['status'] == 1) {
            foreach ($result['data']['parent'] as $key => $item) {
                $parentMenu[$key]['ParentId'] = $key;
                $parentMenu[$key]['ParentName'] = $item;
                $parentMenu[$key]['Status'] = $result['data']['parentIfNull'][$key];
            }
            // 章节数据
            foreach ($result['data']['child'] as $key => $item) {
                foreach ($item as $kc => $vc) {
                    $chapterData[$kc]['ParentId'] = $key;
                    $chapterData[$kc]['ParentName'] = $parentMenu[$key]['ParentName'];
                    $chapterData[$kc]['ChildId'] = $kc;
                    $chapterData[$kc]['ChildName'] = $vc;
                    $chapterData[$kc]['Status'] = $result['data']['sonIfNull'][$kc];
                    $method = self::$chapter[$kc];
                    $param = array(
                        'eID' => self::$eID,
                        'pChapterID' => $key,
                        'cChapterID' => $kc
                    );
                    $chapterResult = Sdk_Cms_Evaluation::$method($param);
                    if ($chapterResult['status']) {
                        $chapterData[$kc]['data'] = $chapterResult['data'];
                    }
                }
            }
        }
        return array('parentMenu' => $parentMenu, 'chapterData' => $chapterData);
    }

    // 获取主力户型
    public static function getMainRoomType(&$chapterData)
    {
        $mainRoomType = '';
        // 主力户型
        if (isset($chapterData['InfoList']) && !empty($chapterData['InfoList'])) {
            $mainRoomType = current($chapterData['InfoList'])['iRoomNum'];
        }
        return $mainRoomType;
    }

    // 户型分析小章节的分析师评论
    public static function getAnalystsComment(&$chapterData)
    {

        $analystsComment = '';
        $simpleAnalystsComment = '';
        if (isset($chapterData['sComment'])) {
            $analystsComment = $chapterData['sComment'];
            if (mb_strlen($analystsComment) > self::$evalSubLen) {
                $simpleAnalystsComment = mb_substr($analystsComment, 0, self::$evalSubLen) . '...';
            } else {
                $simpleAnalystsComment = $analystsComment;
            }
        }
        return array('analystsComment' => $analystsComment, 'simpleAnalystsComment' => $simpleAnalystsComment);
    }

    // 户型分析小章节的图片数据
    public static function getHxfxImages(&$chapterData)
    {
        $hxImages = array();
        $spImages = array();
        if (!empty($chapterData['hxImgs'])) {
            foreach ($chapterData['hxImgs'] as $key => $item) {
                foreach ($item as $kc => $vc) {
                    if ($vc["iTypeID"] == "101") {
                        $hxImages[] = $vc;
                    }
                    if ($vc["iTypeID"] == "102") {
                        $spImages[] = $vc;
                    }
                }
            }
        }
        return array('hx' => $hxImages, 'sp' => $spImages);
    }

    // 装修标准 - 品牌配置的厨房图片和卫浴图片
    public static function getZxbzImages(&$chapterData)
    {
        $cfImages = array();
        $wyImages = array();
        foreach ($chapterData as $key => $item) {
            if (substr($key, 0, 2) == 'cf' && substr($key, -5) == 'Image' && !empty($item)) {
                foreach ($item as $kc => $vc) {
                    $cfImages[] = $vc;
                }
            }
            if (substr($key, 0, 2) == 'wy' && substr($key, -5) == 'Image' && !empty($item)) {
                foreach ($item as $kc => $vc) {
                    $wyImages[] = $vc;
                }
            }
        }
        return array('cf' => $cfImages, 'wy' => $wyImages);
    }



    // 社区品质 社区景观图片
    public static function getSqjgImages(&$chapterData)
    {
        $images = array();
        if (!empty($chapterData['sqxgImage'])) {
            foreach ($chapterData['sqxgImage'] as $item) {
                $images[] = $item;
            }
        }
        if (!empty($chapterData['sqspImage'])) {
            foreach ($chapterData['sqspImage'] as $item) {
                $images[] = $item;
            }
        }
        return $images;
    }

    // 社区品质 整体规划图片
    public static function getSqghImages(&$chapterData)
    {
        $images = array();
        if (!empty($chapterData['sqpmImage'])) {
            foreach ($chapterData['sqpmImage'] as $item) {
                $images[] = $item;
            }
        }
        if (!empty($chapterData['sqnkImage'])) {
            foreach ($chapterData['sqnkImage'] as $item) {
                $images[] = $item;
            }
        }
        return $images;
    }

    // 教育资源 的 学校名称 和图片
    public static function getEducateInfo(&$chapterData)
    {
        $schoolName = array();
        $jyzyImages = array();
        if (!empty($chapterData)) {
            $type = array('mInfo', 'pInfo', 'kInfo');
            foreach ($type as $key) {
                if (!empty($chapterData[$key])) {
                    foreach ($chapterData[$key] as $item) {
                        if (count($schoolName) < 2) {
                            $schoolName[] = $item['sSchoolName'];
                        }
                        if (!empty($item['images'])) {
                            foreach ($item['images'] as $vc) {
                                $jyzyImages[] = $vc;
                            }
                        }
                    }
                }
            }
        }
        return array('shoolName' => implode('/', $schoolName), 'jyzyImages' => $jyzyImages);
    }

    // 诊所 医院的名称
    public static function getMedicalInfo(&$chapterData)
    {
        $arr = array("sHospital", "sClinic", "sPharmacy");
        $medicalName = array();
        if (!empty($chapterData)) {
            foreach ($chapterData as $key => $item) {
                if (in_array($key, $arr) && !empty($item)) {
                    foreach ($item as $vc) {
                        if (count($medicalName) < 2) {
                            $medicalName[] = $vc['name'];
                        }
                    }
                }
            }
        }
        return implode('/', $medicalName);
    }

    // 周边商圈的 前两个的图片名称 和所有图片数组
    public static function getBusinessInfo(&$chapterData)
    {

        $arr = array("zbmcImage", "zbyhImage", "zbcyImage", "zbfwImage", "zbccImage", "sqqtImage");
        $imgTitle = array();
        $zbsqImages = array();
        if (!empty($chapterData)) {
            foreach ($chapterData as $key => $item) {
                if (in_array($key, $arr) && !empty($item)) {
                    foreach ($item as $vc) {
                        if (count($imgTitle) < 2) {
                            $imgTitle[] = $vc['sTitle'];
                        }
                        $zbsqImages[] = $vc;
                    }
                }
            }
        }
        return array('imgTitle' => implode('/', $imgTitle), 'zbsqImages' => $zbsqImages);
    }

    // 轨交公交 对应的站点名,距离和图片
    public static function getRailInfo(&$metroData, &$busData)
    {
        $distance = 1000000;
        $tLine = '';
        $lineCnt = 1;
        $metroImages = array();
        $busImages = array();
        if (!empty($metroData) && $metroData['iHasMetro'] != '0' && $metroData['aMetroInfo'] != null) {
            foreach ($metroData['aMetroInfo'] as $key => $item) {
                if ($item['iDistance'] < $distance) {
                    $tLine = vsprintf("<cite class=\"cite_txt3\"><span title=\"%s %s\">%s %s</span><em title=\"%s米\">%s米</em></cite>",
                        array(
                            $lineCnt,
                            $item["sStation"],
                            $lineCnt,
                            $item["sStation"],
                            $item['iDistance'],
                            $item['iDistance']
                        ));
                    $distance = $item['iDistance'];
                }
                if (!empty($item["images"])) {
                    foreach ($item["images"] as $kc => $vc) {
                        $metroImages[] = $vc;
                    }
                }
            }
            $lineCnt++;
        }
        if (!empty($busData) && !empty($busData["busInfo"])) {
            foreach ($busData["busInfo"] as $key => $item) {
                if ($lineCnt <= 3) {
                    $tLine .= vsprintf("<cite class=\"cite_txt3\"><span title=\"%s %s\">%s %s</span><em title=\"%s米\">%s米</em></cite>",
                        array(
                            $lineCnt,
                            $item["sBusName"],
                            $lineCnt,
                            $item["sBusName"],
                            $item['iDistance'],
                            $item['iDistance']
                        ));
                }
                if (!empty($item["images"])) {
                    foreach ($item["images"] as $kc => $vc) {
                        $busImages[] = $vc;
                    }
                }
                $lineCnt++;
            }
        }
        return array(
            'tLine' => $tLine,
            'metroImages' => $metroImages,
            'busImages' => $busImages,
        );
    }

    // 不利内部因素 的相关信息
    public static function getBadfactorInsideInfo(&$insideData)
    {
        $pCnt = 0;
        $effectStr = '';
        if (!empty($insideData) && !empty($insideData["sGdblys"])) {
            foreach ($insideData["sGdblys"] as $key => $item) {
                if ($pCnt <= 3) {
                    if ($item["checked"] == "1") {
                        $effectStr = $effectStr . vsprintf("<cite class=\"cite_txt3\"><b title=\"影响源：%s\">影响源：%s</b><b title=\"%s\">%s</b></cite>",
                                array(
                                    $item["ysy"],
                                    $item["ysy"],
                                    $item["blys"],
                                    $item["blys"]
                                ));
                        $pCnt++;
                    }
                }
            }
        }
        return $effectStr;
    }

    // 不利外部因素的相关信息
    public static function getBadfactorOutsideInfo(&$outSideData)
    {
        $sqwbImages = array();
        if (!empty($outSideData) && !empty($outSideData['badTagImgs'])) {
            foreach($outSideData['badTagImgs'] as $key => $item) {
                foreach($item as $images) {
                    $sqwbImages[] = $images;
                }
            }
        }
        return $sqwbImages;
    }

    // 从乐居获取评测报告
    public static function getGetEvaluationReportFormLeju($iUnitid,$cityInfo,$page=1,$pageSize = 25)
    {
         if (empty($iUnitid)) {
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
            'filter3' =>  "{hid@eq}" . $iUnitid,// + ,102452
            'filter4' => "{deleted@eq}0",
            'filter5' => "{t_id@eq}" .$cityInfo['CMSMBID'],
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

    // 从cric获取评测报告
    public static function getGetEvaluationReportFormCric()
    {

    }
}