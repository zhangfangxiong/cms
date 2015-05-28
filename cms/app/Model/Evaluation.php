<?php

/**
 * 评测报告业务类
 * author: cjj
 */
class Model_Evaluation extends Model_Base
{

    const DB_NAME = 'cms';

    const TABLE_NAME = 't_evaluation';

    const PUBLISH_N = 0; // 评测报告未发布
    const SFINISHED = 0; // 完成章节 0 表示 未开始


    /*
     * 添加 评测草稿
     * @param array $aData
     * @return bool
     */
    public static function addDraft($aData)
    {
        if (empty($aData)) {
            return false;
        }
        $aData['iPublish'] = self::PUBLISH_N;
        $cookie = Util_Cookie::get(Yaf_G::getConf('authkey', 'cookie'));
        if (empty($cookie)) {
            return false;
        }
        $aData['iUpdateUserID'] = $aData['iCreateUserID'] = $cookie['iUserID'];
        if (self::addData($aData) > 0) {
            return true;
        } else {
            return false;
        }
    }


    /*
    * 获取所需配置
    *
    */
    public static function getConfig()
    {
        // 章节
        $config['chapter'] = Yaf_G::getConf('chapter', null, 'evaluation');
        return $config;
    }


    /*
     * 评测草稿列表
     * @param array $aParam
     * @$iType 1 正式 2 草稿
     * @return array
     */
    public static function getEvaluationList($aParam, $iPage, $iType)
    {
        if (empty($aParam)) {
            return null;
        }
        $userIdArr = array();
        // 先搜索作者
        if (!empty($aParam['author'])) {
            $aList = Model_User::searchUserByName($aParam['author']);
            if (!empty($aList)) {
                foreach ($aList as $k => $v) {
                    $userIdArr[] = $v['iUserID'];
                }
            }
        }
        // 楼盘
        if (isset($aParam['lpName'])) {
            $aWhere ['sUnitName like'] = '%' . $aParam['lpName'] . '%';
        }
        // 作者 当前作者为 cms后台 操作人员
        if (!empty($userIdArr)) {
            $aWhere['iCreateUserID in'] = implode(',', $userIdArr);
        }
        if (!empty($aParam['iCreateTime'])) {
            $aWhere['iCreateTime >='] = strtotime($aParam['iCreateTime']);
        }
        if (!empty($aParam['iUpdateTime'])) {
            $aWhere['iUpdateTime <='] = strtotime($aParam['iUpdateTime']);
        }
        if (!empty($aParam['iCityID'])) {
            $aWhere['iCityID'] = $aParam['iCityID'];
        }
        $aWhere['iStatus'] = 1; // 未删除
        if ($iType == 1) {
            $aWhere['sFinished <> '] = '';
        } else {
            $aWhere['sFinished ='] = '';
        }
        return self::getList($aWhere, $iPage, 'iCreateTime DESC', 20, '', $aParam);
    }


    /*
     *  最后编辑章节 和 已完成的章节的修改
     *
     * */
    public static function  updateChapter($eId, $chapter)
    {
        if (empty($eId) || empty($chapter)) {
            return;
        }
        $result = self::getDetail($eId);
        $sFinished = $result['sFinished'];
        if (!empty($sFinished)) {
            $sFinished = explode(',', $sFinished);
            if (!in_array($chapter, $sFinished)) {
                array_push($sFinished, $chapter);
            }
        } else {
            $sFinished = array($chapter);
        }
        $aParam = array(
            'iEvaluationID' => $eId,
            'sLastEditChapter' => $chapter,
            'sFinished' => implode(',', $sFinished),
        );
        return self::updData($aParam);
    }

    /*
     * 获取章节的控制器访问地址
     *
     * */
    public static function getChapterUrl()
    {
        $config = self::getConfig();
        $chapterUrlArr = array();
        if (isset($config['chapter']['action'])) {
            foreach ($config['chapter']['action'] as $i => $item) {
                foreach ($item as $k => $v) {
                    $chapterUrlArr[$k] = '/evaluation/' . $config['chapter']['controller'][$i] . '/' . $v;
                }
            }
        }
        return $chapterUrlArr;
    }

    /*
    * 发布评测报告
    *
    * */
    public static function publishEvaluation($eID)
    {
        if (self::isFinished($eID)) {
            $rs = self::updData(array(
                'iEvaluationID' => $eID,
                'iPublish' => 1
            ));
            if ($rs) {
                return true;
            } else {
                throw new Exception('评测报告发布失败!');
            }
        } else {
            throw new Exception('评测报告未全部完成!');
        }
    }

    /*
     * 评测报告是否全部完成
     *
     * */
    public static function isFinished($eID)
    {
        $chapterNum = 22; // 总章节数
        // 查找 品牌配置表是否有记录 同时 是否勾选了毛坯交付
        $evaluationInfo = self::getDetail($eID);
        if (empty($evaluationInfo['sFinished'])) {
            return false;
        }
        $arr = explode(',', $evaluationInfo['sFinished']);
        $sFinishedNum = sizeof($arr);
        $brandInfo = Model_EvaluationZxBrand::getBrandByEID($eID);
        if (isset($brandInfo['iIsBlank']) && $brandInfo['iIsBlank'] == 1) { // 勾选了毛坯交付，装修分析的章节可不填
            if ($sFinishedNum == $chapterNum) { // 全部完成
                return true;
            } else {
                if ($sFinishedNum == ($chapterNum - 1)) {  // 21 个
                    if (in_array(Model_EvaluationZxAnalysis::CHAPTER, $arr)) {
                        return false;
                    }
                } else {
                    return false;
                }
            }
        } else {
            if ($sFinishedNum != $chapterNum) {
                return false;
            }

        }
        return true;
    }

    /**
     * 批量添加评测报告
     */
    public static function addBatchData($data)
    {
        if (!empty($data)) {
            foreach ($data as $item) {
                $item['sFinished'] = self::aFilterChapter($item['sFinished']);
                $item['iSyncTime'] = time();
                self::addData($item);
            }
        }
    }

    /**
     * 批量修改评测报告
     *
     */
    public static function updateBatchData($data)
    {
        if (!empty($data)) {
            foreach ($data as $item) {
                if (!empty($item['sFinished'])) {
                    $dbArr = self::getDetail($item['iEvaluationID']);
                    $inFinished = explode(',', $item['sFinished']);
                    // 有空的章节的交集
                    $intersect = self::uFilterChapter($inFinished, $dbArr['sFinished']);
                    $inFinished = array_merge($inFinished, $intersect);
                    if (!empty($inFinished)) {
                        $item['sFinished'] = implode(',', $inFinished);
                    } else {
                        $item['sFinished'] = '';
                    }
                }
                $item['iSyncTime'] = time();
                unset($item['iCreateUserID']);
                unset($item['iUpdateUserID']);
                self::updData($item);
            }
        }
    }

    /**
     * 接口 评测报告新增时 章节的过滤
     */
    private static function aFilterChapter($sFinished)
    {
        $filterChapter = Yaf_G::getConf('filterChapter', null, 'evaluation');
        if (!empty($sFinished)) {
            $sFinished = explode(',', $sFinished);
            foreach ($sFinished as $k => $v) {
                if (in_array($v, $filterChapter)) {
                    unset($sFinished[$k]);
                }
            }
            if (!empty($sFinished)) {
                return implode(',', $sFinished);
            } else {
                return '';
            }
        }
    }

    /**
     * 接口 评测报告修改时 章节的过滤
     * param @$inFinished 接口过来的
     * param @$dbFinished 数据库已有的
     */
    private static function uFilterChapter(&$inFinished, $dbFinished)
    {
        $filterChapter = Yaf_G::getConf('filterChapter', null, 'evaluation');
        $inFilterArr = array();
        $dbFilterArr = array();
        if (!empty($inFinished)) {
            foreach ($inFinished as $k => $v) {
                if (in_array($v, $filterChapter)) {
                    $inFilterArr[] = $v;
                    unset($inFinished[$k]);
                }
            }
        }
        if (!empty($dbFinished)) {
            $dbFinished = explode(',', $dbFinished);
            foreach ($dbFinished as $k => $v) {
                if (in_array($v, $filterChapter)) {
                    $dbFilterArr[] = $v;
                }
            }
        }
        return $dbFilterArr;
        return array_intersect($inFilterArr, $dbFilterArr);
    }

    /**
     * @param $iLoupanID
     * Mapi 楼盘详情获取楼盘对应的评测报告状态信息
     */
    public static function getEvaluationInfo($iLoupanID, $isApp = false)
    {
        $row = self::getRow(
            [
                'where' => [
                    'iUnitID' => $iLoupanID,
                    'iStatus' => self::STATUS_VALID,
//                    'iPublish' => 1
                    'sFinished !=' => ''
                ],
                'order' => 'iUpdateTime DESC'
            ]
        );
        $parent = Yaf_G::getConf('parent', 'chapter', 'evaluation');
        unset($parent[5]);
        $child = Yaf_G::getConf('child', 'chapter', 'evaluation');
        $result = ['sCompletePageUrl' => '', 'iEvaluateID' => 0, 'chapter' => [], 'analystID' => ''];

        if ($row) {
            $city = $row['iCityID'];
            $city = Model_City::getDetail($city);
            $cityName = $city['sFullPinyin'];
            $result['iEvaluateID'] = $row['iEvaluationID'];
            $result['analystID'] = $row['iAnalysisID'];

            $aFinished = explode(',', $row['sFinished']);
            foreach ($parent as $key => $value) {
                $aChild = $child[$key];
                $tmp = [
                    'sChapterCode' => $key,
                    'iEnable' => 0,
                    'sPageUrl' => ''
                ];
                foreach ($aChild as $k => $v) {
                    if (in_array($k, $aFinished)) {
                        $tmp = [
                            'sChapterCode' => $key,
                            'iEnable' => 1,
                            'sPageUrl' => Util_Uri::getEvaluationDetailUrl($row['iEvaluationID'], $k, $cityName, 2, $isApp)
                        ];
                        if (empty($result['sCompletePageUrl'])) {
                            $result['sCompletePageUrl'] = $tmp['sPageUrl'];
                        }
                        break;
                    }
                }
                array_push($result['chapter'], $tmp);
            }

            foreach ($child[5] as $ck => $cv) {
                if (in_array($ck, $aFinished)) {
                    if (empty($result['sCompletePageUrl'])) {
                        $sUrl = Util_Uri::getEvaluationDetailUrl($row['iEvaluationID'], $ck, $cityName, 2, $isApp);
                        $result['sCompletePageUrl'] = $sUrl;
                    }
                    break;
                }
            }

        } else {
            foreach ($parent as $key => $value) {
                $aChild = $child[$key];
                foreach ($aChild as $k => $v) {
                    $tmp = [
                        'sChapterCode' => $key,
                        'iEnable' => 0,
                        'sPageUrl' => ''
                    ];
                    array_push($result['chapter'], $tmp);
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * @param $iCityID
     * 根据iCityID 获取最新的评测报告作为app首页推荐评测
     */
    public static function getNewest($iCityID)
    {

        //$sql = 'select * from t_loupan where iCityID ='. $cityID. ' and iStatus = 1 and iTotalScore > 0';

        $aLoupan = Model_Loupan::getPair(
            [
                'where' => [
                    'iCityID' => $iCityID,
                    'iStatus' => 1,
                    'iTotalScore >' => 0
                ]
            ],
            'iAutoID',
            'sMapUnitID'
        );

        if ($aLoupan) {
            $aLoupanDetail = Model_LoupanDetail::getPair(
                [
                    'where' => [
                        'iLoupanID IN' => array_keys($aLoupan),
                        'sHouseRemark >' => ''
                    ]
                ],
                'iLoupanID',
                'sHouseRemark'

            );
            $aLoupanID = array_keys($aLoupanDetail);
            foreach($aLoupan as $key => $value) {
                if (!in_array($key, $aLoupanID)) {
                    unset($aLoupan[$key]);
                }
            }
            $aLoupanMapID = array_values($aLoupan);
        }

        $aUnit = Model_CricUnit::getPair(
            [
                'where' => ['UnitID IN' => "'" . implode("','", $aLoupanMapID) . "'"]
            ],
            'ID',
            'UnitID'
        );

        $aCricID = [];
        if (!empty($aUnit)) {
            $aCricID = array_keys($aUnit);
        }

        if ($aCricID) {
            $where = [
                'iCityID' => $iCityID,
                'iStatus' => self::STATUS_VALID,
                'iUnitID IN' => $aCricID,
                'sFinished !=' => ''
            ];
        } else {
            $where = [
                'iCityID' => $iCityID,
                'iStatus' => self::STATUS_VALID,
                'sFinished !=' => ''
            ];
        }
        $evaluationList = self::getAll(
            [
                'where' => $where,
                'order' => 'iUpdateTime DESC',
                'limit' => '0,6'
            ]
        );

        $city = Model_City::getDetail($iCityID);

        $result = [];
        if (!empty($evaluationList)) {
            foreach ($evaluationList as $key => $value) {
                $aLoupan = array();

                $aUnit = Model_CricUnit::getLoupanByID($value['iUnitID']);
                if (!empty($aUnit)) {
                    $aLoupan = Model_Loupan::getRow([
                        'where' => [
                            'sMapUnitID' => $aUnit['UnitID']
                        ]
                    ]);
                    $aLoupanDetail = Model_LoupanDetail::getDetail($aLoupan['iAutoID']);
                } else {
                    continue;
                }
                $analyst = Model_Analysts::getDetail($value['iAnalysisID']);
                if (!empty($aLoupan)) {
                    $sFinished = $value['sFinished'];
                    $sFinished = explode(',', $sFinished);
                    sort($sFinished);

                    $result[] = [
                        'iCommonID' => $aLoupan['iAutoID'],
                        'iEvaluateID' => $value['iEvaluationID'],
                        'sBuildName' => $aLoupan['sName'],
                        'sBuildImgUrl' => Model_Loupan::getZongPinPic($aLoupan['sZongPinPic'], 600, 338),
                        'sRegion' => $aLoupan['sRegionName'] . '-' . $aLoupan['sZoneName'],
                        'sRateNum' => $aLoupan['iTotalScore'],
                        'sCommentText' => $aLoupanDetail['sHouseRemark'],
                        'sAvatarImgUrl' => empty($analyst['AnalystHead']) ? Util_Uri::getDefaultImg(2) : Util_Uri::getCricViewURL($analyst['AnalystHead'], 120, 120, 0, 0, 1),
                        'sPrice' => !empty($aLoupan['iBidingPrice']) ? number_format($aLoupan['iBidingPrice']) : '待定',
                        'sUnit' => !empty($aLoupan['iBidingPrice']) ? '元/平' : '',
                        'sEvaluateUrl' => Util_Uri::getEvaluationDetailUrl($value['iEvaluationID'], intval($sFinished[0]), $city['sFullPinyin'], 2),
                        'iCssNum' => self::_GetCssBysRateNum($aLoupan['iTotalScore'])//zfx加20150506
                    ];
                }
            }
        }
        return $result;
    }

    //zfx加20150506
    public static function  _GetCssBysRateNum($sRateNum)
    {
        $iCssNum = 1;
        switch($sRateNum){
            case 0<$sRateNum && $sRateNum <= 0.5 :
                $iCssNum = 2;
                break;
            case 0.5<$sRateNum && $sRateNum <= 1 :
                $iCssNum = 3;
                break;
            case 1<$sRateNum && $sRateNum <= 1.5 :
                $iCssNum = 4;
                break;
            case 1.5<$sRateNum && $sRateNum <= 2 :
                $iCssNum = 5;
                break;
            case 2<$sRateNum && $sRateNum <= 2.5 :
                $iCssNum = 6;
                break;
            case 2.5<$sRateNum && $sRateNum <= 3 :
                $iCssNum = 7;
                break;
            case 3<$sRateNum && $sRateNum <= 3.5 :
                $iCssNum = 8;
                break;
            case 3.5<$sRateNum && $sRateNum <= 4 :
                $iCssNum = 9;
                break;
            case 4<$sRateNum && $sRateNum <= 4.5 :
                $iCssNum = 10;
                break;
            case 4.5<$sRateNum && $sRateNum <= 5 :
                $iCssNum = 11;
                break;
        }
        return $iCssNum;
    }
}