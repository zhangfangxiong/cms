<?php

class Model_News extends Model_Base
{

    const DB_NAME = 'cms';

    const TABLE_NAME = 't_news';

    const GUIDE_NEWS = 1;    //导购文章

    const EVALUATION_NEWS = 2;    //评测文章


    /**
     * 跟据标题取得资讯信息
     * @param unknown $sTagName
     * @return Ambigous <number, multitype:, mixed>
     */
    public static function getNewsByName($sTitle)
    {
        return self::getRow(array(
            'where' => array(
                'sTitle' => $sTitle
            )
        ));
    }

    /**
     * 跟据文章ID取得资讯信息
     * @param unknown $sTagName
     * @return Ambigous <number, multitype:, mixed>
     */
    public static function getNewsByID($iNewsID)
    {
        return self::getDetail($iNewsID);
    }


    public static function searchAutoComplete($p_sKey, $p_iCityID)
    {
        $aWhereCity = '';
        if ($p_iCityID) {
            $aWhereCity = ' AND iCityID=' . $p_iCityID;
        }
        $iTime = time();
        $sSQL = "SELECT
                *
                FROM " . self::TABLE_NAME . "
                WHERE iPublishStatus=1 AND iPublishTime <= " . $iTime . " AND iStatus=1 AND (sShortTitle LIKE '" . addslashes($p_sKey) . "%' OR sTitle LIKE '%" . addslashes($p_sKey) . "%'){$aWhereCity}
                LIMIT 10
            ";
        return self::query($sSQL);
    }

    /**
     * @param $p_iNewsID
     *
     * 获取已发布文章详情
     */
    public static function getPublishNewsDetail($p_iNewsID)
    {
        $aWhere = ['iNewsID' => $p_iNewsID, 'iStatus' => 1, 'iPublishStatus' => 1, 'iPublishTime <=' => time()];
        return self::getRow(['where' => $aWhere]);
    }


    public static function getPageList(
        $aParam,
        $iPage,
        $sOrder = 'iUpdateTime DESC',
        $iPageSize = 20,
        $sUrl = '',
        $aArg = array()
    ) {

        $iPage = max($iPage, 1);
        $sSQL = 'SELECT * FROM ' . self::TABLE_NAME . ' WHERE iStatus=' . self::STATUS_VALID;
        $sCntSQL = 'SELECT COUNT(*) as total FROM ' . self::TABLE_NAME . ' WHERE iStatus=' . self::STATUS_VALID;

        if (isset($aParam['iNewsID']) && !empty($aParam['iNewsID'])) {
            $sSQL .= ' AND iNewsID=' . $aParam['iNewsID'];
            $sCntSQL .= ' AND iNewsID=' . $aParam['iNewsID'];
        }

        if (isset($aParam['iCityID'])) {
            if (is_array($aParam['iCityID'])) {
                $sCityIDs = implode(',', $aParam['iCityID']);
                $sSQL .= ' AND iCityID IN (' . $sCityIDs .')';
                $sCntSQL .= ' AND iCityID IN (' . $sCityIDs .')';

            } else {
                $sSQL .= ' AND iCityID=' . $aParam['iCityID'];
                $sCntSQL .= ' AND iCityID=' . $aParam['iCityID'];

            }
        }


        if (isset($aParam['iCategoryID']) && $aParam['iCategoryID'] > 0) {
            $sSQL .= ' AND iCategoryID=' . $aParam['iCategoryID'];
            $sCntSQL .= ' AND iCategoryID=' . $aParam['iCategoryID'];
        }
        if (isset($aParam['iPublishStatus']) && $aParam['iPublishStatus'] != -1 && $aParam['iPublishStatus'] != '') {
            $sSQL .= ' AND iPublishStatus=' . $aParam['iPublishStatus'];
            $sCntSQL .= ' AND iPublishStatus=' . $aParam['iPublishStatus'];
        }
        if (!empty($aParam['sTitle'])) {
            $sSQL .= ' AND sTitle LIKE \'%' . addslashes($aParam['sTitle']) . '%\'';
            $sCntSQL .= ' AND sTitle LIKE \'%' . addslashes($aParam['sTitle']) . '%\'';
        }

        //如果有作者ID就不用作者名
        if (!empty($aParam['iAuthorID']) && $aParam['iAuthorID'] > 0) {
            $sSQL .= ' AND iAuthorID=' . $aParam['iAuthorID'];
            $sCntSQL .= ' AND iAuthorID=' . $aParam['iAuthorID'];
        } else {
            if (!empty($aParam['sAuthor'])) {
                $sSQL .= ' AND sAuthor LIKE \'%' . addslashes($aParam['sAuthor']) . '%\'';
                $sCntSQL .= ' AND sAuthor LIKE \'%' . addslashes($aParam['sAuthor']) . '%\'';
            }
        }

        if (!empty($aParam['iLoupanID']) && $aParam['iLoupanID'] > 0) {
            $sSQL .= ' AND FIND_IN_SET(' . $aParam['iLoupanID'] . ', sLoupanID)';
            $sCntSQL .= ' AND FIND_IN_SET(' . $aParam['iLoupanID'] . ', sLoupanID)';
        }

        if (!empty($aParam['iTagID']) && $aParam['iTagID'] > 0) {
            $sSQL .= ' AND FIND_IN_SET(' . $aParam['iTagID'] . ', sTag)';
            $sCntSQL .= ' AND FIND_IN_SET(' . $aParam['iTagID'] . ', sTag)';
        }

        //时间换算
        if (!empty($aParam['iSTime'])) {
            $sSQL .= ' AND iPublishTime >=' . strtotime($aParam['iSTime']);
            $sCntSQL .= ' AND iPublishTime >=' . strtotime($aParam['iSTime']);
        }

        if (!empty($aParam['iETime'])) {
            $sSQL .= ' AND iPublishTime <=' . strtotime($aParam['iETime']);
            $sCntSQL .= ' AND iPublishTime <=' . strtotime($aParam['iETime']);
        }

        if (!empty($aParam['iTypeID'])) {
            $sSQL .= ' AND iTypeID=' . $aParam['iTypeID'];
            $sCntSQL .= ' AND iTypeID=' . $aParam['iTypeID'];
        }

        $sSQL .= ' Order by ' . $sOrder;
        $sSQL .= ' Limit ' . ($iPage - 1) * $iPageSize . ',' . $iPageSize;

        $aRet['aList'] = self::getOrm()->query($sSQL);
        if ($iPage == 1 && count($aRet['aList']) < $iPageSize) {
            $aRet['iTotal'] = count($aRet['aList']);
            $aRet['aPager'] = null;
        } else {
            unset($aParam['limit'], $aParam['order']);
            $ret = self::getOrm()->query($sCntSQL);
            $aRet['iTotal'] = $ret[0]['total'];
            $aRet['aPager'] = Util_Page::getPage($aRet['iTotal'], $iPage, $iPageSize,'',self::_getNewsPageParam($aParam)); // update by cjj 2015-02-13 分页增加query 参数
        }
        return $aRet;

    }

    // add by cjj 2015-02-13 分页增加query 参数
    private static function _getNewsPageParam(&$aParam) {
        $pageParam = array(
            'sTagName' => isset($aParam['sTagName']) ? $aParam['sTagName'] : '',
            'iSTime' => isset($aParam['iSTime']) ? $aParam['iSTime'] : '',
            'iETime' => isset($aParam['iETime']) ? $aParam['iETime'] : '',
            'iAuthorID'=> isset($aParam['iAuthorID']) ? $aParam['iAuthorID'] : '',
            'sAuthor'=> isset($aParam['sAuthor']) ? $aParam['sAuthor'] : '',
            'sOrder' => isset($aParam['sOrder']) ? $aParam['sOrder'] : '',
            'sTitle' => isset($aParam['sTitle']) ? $aParam['sTitle'] : '',
            'iPublishStatus' => isset($aParam['iPublishStatus']) ? $aParam['iPublishStatus'] : '',
        );
        if (!empty($aParam['iCategoryID'])) {
            $pageParam['iCategoryID'] = $aParam['iCategoryID'];
        }
        if (!empty($aParam['iLoupanID'])) {
            $pageParam['iLoupanID'] = $aParam['iLoupanID'];
            $pageParam['sLoupanName'] = isset($aParam['sLoupanName']) ? $aParam['sLoupanName']: '';
        }
        if (!empty($aParam['sTagName'])) {
            $pageParam['sTagName'] = $aParam['sTagName'];
        }
        return $pageParam;

}

    /**
     * @param $tag
     *
     * 获取相关文章
     */
    public static function getRelatedNews($iCityID, $iNewsID, $iTagID, $pageSize)
    {
        $sSQL = null;
        $results = array();
        if (!empty($iTagID)) {
            $tags = explode(',', $iTagID);
            $len = sizeof($tags);
            $index = $len - 1;
            $avgNum = round($pageSize / $len);

            $perLen = $avgNum * $len;
            $lastNum = 0;
            if ($perLen < $pageSize) {
                $lastNum = $avgNum + ($pageSize - $perLen);
            } else {
                $lastNum = $avgNum - ($perLen - $pageSize);
            }

            $existId = array($iNewsID);

            //算法有问题，循环过程中可能有没查到
            for ($i = 0; $i < $len; $i++) {
                $ids = implode(',', $existId);

                if ($i == $index) {
                    $avgNum = $lastNum;
                }
                $sSQL = 'SELECT * FROM ' . self::TABLE_NAME . ' WHERE iCityID = '. $iCityID. ' and iPublishStatus=1 and iStatus=' . self::STATUS_VALID . ' and iNewsID not in (' . $ids . ') AND FIND_IN_SET(' . intval($tags[$i]) . ', sTag) Order by iVisitNum desc Limit ' . $avgNum;
                $news = self::getOrm()->query($sSQL);
                foreach ($news as $new) {
                    $results[] = $new;
                    $existId[] = $new['iNewsID'];
                }
            }
        }

        return $results;
    }

    /*
     * 根据tagID找到对应标签下的有效文章数
     * @param int $iTagID
     * @return int
     */
    public static function getNewsNumByTagID($iTagID)
    {
        $sql = "SELECT count(*) FROM t_news WHERE FIND_IN_SET(" . $iTagID . ",sTag) AND iStatus=1 AND iPublishStatus=1";
        return self::query($sql, 'one');
    }

    /**
     * 获取所有文章数目
     * 0:所有文章,1:未删除的文章,2:已发布的文章
     */
    public static function getNewsNum($type = 2)
    {
        if ($type == 0) {
            $where = 1;
        } elseif ($type == 1) {
            $where = "iStatus = 1";
        } elseif ($type == 2) {
            $where = "iStatus = 1 AND iPublishStatus = 1";
        }
        $sql = "SELECT COUNT(*) FROM t_news WHERE " . $where;
        return self::query($sql, 'one');
    }

    /**
     * 判断分类下是否有文章
     * @param int iCategoryID
     */
    public static function getNewsByCat($cat_id)
    {
        return self::getAll(array(
            'where' => array(
                'iCategoryID' => $cat_id,
                'iStatus' => 1
            )
        ));
    }

    /**
     * 更新文章当天的统计
     * @param $iType 1:访问量,2:分享量
     * @param $ikey的格式 :v20150204,s20150204
     */
    public static function updateNewsDayStatis($iNewsID, $iType = 1)
    {
        if (!$aNews = self::getNewsByID($iNewsID)) {
            return 0;
        }
        $iCityID = $aNews['iCityID'];
        switch ($iType) {
            case 1:
                $iRKey = $iCityID . '_v_' . date('Ymd');
                $sMKey = 'iVisitNum';
                break;
            case 2:
                $iRKey = $iCityID . '_s_' . date('Ymd');
                $sMKey = 'iShareTimes';
                break;
            default;
                $iRKey = $iCityID . '_v_' . date('Ymd');
                $sMKey = 'iVisitNum';
        }
        //mysql统计总访问量
        $aNews[$sMKey] += 1;
        if (!Model_News::updData($aNews)) {
            return 0;
        }
        //redis统计当天访问量
        $redis = Util_Common::getRedis();
        $iMember = $iNewsID;
        return $redis->ZINCRBY($iRKey, 1, $iMember);
    }

    /**
     * 获取当天热点文章(访问量前10)
     * @param $iSize 获取条数
     */
    public static function getDayHotNewsList($iCityID = 0, $iSize = 10)
    {
        $iKey = $iCityID . '_v_' . date('Ymd');
        $redis = Util_Common::getRedis();
        $aHotNews = $redis->ZREVRANGE($iKey, 0, $iSize - 1, 'WITHSCORES');                                              //当前城市的新闻
        if ($iCityID > 0 && count($aHotNews) < $iSize) {                                                                //全国的新闻排名
            $aHotNewsAlL = $redis->ZREVRANGE('0_v_' . date('Ymd'), 0, $iSize - 1, 'WITHSCORES');
            if (!empty($aHotNewsAlL)) {
                //array_merge,不适合这里的合并数组
                foreach ($aHotNewsAlL as $iKey => $value) {
                    if (isset($aHotNews[$iKey])) {
                        return array();                     //请求失败;
                    }
                    $aHotNews[$iKey] = $value;
                }
            }
        }
        if (count($aHotNews) < $iSize) {                                                //如果不足所需，通过总访问量判断
            $iNeedSize = $iSize - count($aHotNews);
            $aMHots = self::getHotNewsList($iCityID, $iNeedSize, array_keys($aHotNews));
            if (!empty($aMHots)) {
                $tmp = $aHotNews;
                $aHotNews = array();
                $aHotNews['redis'] = $tmp;
                $aHotNews['mysql'] = $aMHots;
            }
        }
        return $aHotNews;
    }

    /**
     * 获取当天文章分享量(分享量前10)
     */
    public static function getDayShareNewsList($iCityID = 0, $iSize = 10)
    {
        $iKey = $iCityID . '_s_' . date('Ymd');
        $redis = Util_Common::getRedis();
        $aHotNews = $redis->ZREVRANGE($iKey, 0, $iSize - 1, 'WITHSCORES');                                              //当前城市的新闻
        if ($iCityID > 0 && count($aHotNews) < $iSize) {                                                                //全国的新闻排名
            $aHotNewsAlL = $redis->ZREVRANGE('0_v_' . date('Ymd'), 0, $iSize - 1, 'WITHSCORES');
            if (!empty($aHotNewsAlL)) {
                //array_merge,不适合这里的合并数组
                foreach ($aHotNewsAlL as $iKey => $value) {
                    if (isset($aHotNews[$iKey])) {
                        return array();                     //请求失败;
                    }
                    $aHotNews[$iKey] = $value;
                }
            }
        }
        if (count($aHotNews) < $iSize) {                                                //如果不足所需，通过总访问量判断
            $iNeedSize = $iSize - count($aHotNews);
            $aMHots = self::getHotNewsList($iCityID, $iNeedSize, array_keys($aHotNews));
            if (!empty($aMHots)) {
                $tmp = $aHotNews;
                $aHotNews = array();
                $aHotNews['redis'] = $tmp;
                $aHotNews['mysql'] = $aMHots;
            }
        }
        return $aHotNews;
    }

    /**
     * 获取文章总访问量
     * @param $iSize 获取条数
     * $aNewsID,存redis中已有的新闻ID（redis获取每天前10数额不足10时使用）
     */
    public static function getHotNewsList($iCityID = 0, $iSize = 10, $aNewsID = array())
    {
        $aCityID = $iCityID > 0 ? array(0, $iCityID) : array();
        $param = array(
            'where' => array(
                'iPublishStatus' => 1,
                'iStatus' => 1
            ),
            'limit' => $iSize,
            'order' => 'iVisitNum DESC,iPublishTime'
        );

        if (!empty($aNewsID)) {
            $param['where']['iNewsID NOT'] = $aNewsID;
        }
        if (!empty($aCityID)) {
            $param['where']['iCityID IN'] = $aCityID;
        }
        return self::getPair($param, 'iNewsID', 'iVisitNum');
    }

    /**
     * 获取文章总分享量
     * @param $iSize 获取条数
     * @param $aNewsID ,存redis中已有的新闻ID（redis获取每天前10数额不足10时使用）
     */
    public static function getTopShareNewsList($iCityID = 0, $iSize = 10, $aNewsID = array())
    {
        $aCityID = $iCityID > 0 ? array(0, $iCityID) : array();
        $param = array(
            'where' => array(
                'iPublishStatus' => 1,
                'iStatus' => 1,
            ),
            'limit' => $iSize,
            'order' => 'iShareTimes DESC,iPublishTime'
        );

        if (!empty($aNewsID)) {
            $param['where']['iNewsID NOT'] = $aNewsID;
        }
        if (!empty($aCityID)) {
            $param['where']['iCityID IN'] = $aCityID;
        }
        return self::getPair($param, 'iNewsID', 'iShareTimes');
    }

    /**
     * 获取楼盘导购列表
     * @param $iPage 页数
     * @param $iRows 每页行数
     * $type 文章类型  1导购文章2评测文章
     */
    public static function getNewsByBID($iLoupanID, $iCityID, $iPage, $iRows, $type = 0)
    {
        $param = array(
            'iPublishStatus' => 1,
            'iStatus' => 1,
            'iPublishTime <=' => time()
        );
        if (in_array($type, [self::EVALUATION_NEWS, self::GUIDE_NEWS])) {
            $param['iTypeID'] = $type;
        }
        if (intval($iLoupanID) > 0) {
            $param['iLoupanID'] = $iLoupanID;
        }
        if (intval($iCityID) > 0) {
            $param['iCityID'] = [$iCityID,0];
        }

        $result = self::getPageList($param, $iPage, 'iPublishTime DESC', $iRows);
        if (isset($result['aPager'])) {
            unset($result['aPager']);
        }
        return $result;
    }

    /**
     * 修改文章中带楼盘名的内容
     * @param $aNews
     */
    public static function changeNewsLouName(&$aNews)
    {
        if ($aNews['sLoupanID']) {
            $sNewsIDs = explode(',',$aNews['sLoupanID']);
            //按楼盘名字长度进行排序
            $aIDsTemp = array();//ID排序后数组
            foreach ($sNewsIDs as $key => $value) {
                $sLouName = Model_CricUnit::getLoupanNames($value);
                $aLoupan[$value] = isset($sLouName[$value]) ? $sLouName[$value] : '';
            }
            uasort($aLoupan,array('self','_cmp'));
            $sNewsIDs = array_keys($aLoupan);

            foreach ($sNewsIDs as $key => $value) {
                $sContent = $aNews['sContent'];
                $aTmp = Model_CricUnit::getLoupanNames($value);
                if ( empty($aTmp)) {
                    continue;
                }
                $aLoupan = Model_CricUnit::getLoupanByID($value);
                $sUrl = Model_CricUnit::getUnitUrl($aLoupan['CityCode'],$aLoupan['RegionName'],$aLoupan['DistrictName'],$value);
                $sSubting = '<a target="_blank" href="'.$sUrl.'">'.$aTmp[$value].'</a>';
                $sLpName = $aTmp[$value];
                $sAddPattern = "/".$sLpName."(?!([^<]*?)(<\/a>|>|\/>))/";

                if (mb_strpos($sContent, $sLpName) !== false) {//存在楼盘名
                    $aNews['sContent'] = preg_replace($sAddPattern,$sSubting,$sContent);
                }
            }
        }
    }

    public static function _cmp($a, $b)
    {
        if (mb_strlen($a) == mb_strlen($b)) {
            return 0;
        }
        return (mb_strlen($a) > mb_strlen($b)) ? -1 : 1;
    }
}