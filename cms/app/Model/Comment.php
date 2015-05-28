<?php

/**
 * 分析师业务管理类
 * author: cjj
 */
class Model_Comment extends Model_Base
{

    const DB_NAME = 'xinfang';

    const TABLE_NAME = 't_loupan_comment';

    /*
     * 获取评论列表
     * return array
     */

    public static function getPageList($aParam, $iPage, $sOrder = 'iUpdateTime DESC', $iPageSize = 20, $sUrl = '', $aArg = array())
    {
        $iPage = max($iPage, 1);
        $sSQL = 'SELECT * FROM ' . self::TABLE_NAME . ' WHERE iStatus=' . self::STATUS_VALID;
        $sCntSQL = 'SELECT COUNT(*) as total FROM ' . self::TABLE_NAME . ' WHERE iStatus=' . self::STATUS_VALID;

        if (isset($aParam['iIsCheck']) && $aParam['iIsCheck'] > -1 && $aParam['iIsCheck'] !== '') {
            $sSQL .= ' AND iIsCheck=' . $aParam['iIsCheck'];
            $sCntSQL .= ' AND iIsCheck=' . $aParam['iIsCheck'];
        }

        if (!empty($aParam['sContent'])) {
            $sSQL .= ' AND sContent LIKE \'%' . addslashes($aParam['sContent']) . '%\'';
            $sCntSQL .= ' AND sContent LIKE \'%' . addslashes($aParam['sContent']) . '%\'';
        }

        if (!empty($aParam['iLoupanID']) && $aParam['iLoupanID'] > 0) {
            $sSQL .= ' AND FIND_IN_SET(' . $aParam['iLoupanID'] . ', sLoupanID)';
            $sCntSQL .= ' AND FIND_IN_SET(' . $aParam['iLoupanID'] . ', sLoupanID)';
        } else if (isset($aParam['sLoupanName']) && !empty($aParam['sLoupanName'])) {
            $sSQL .= ' AND sContent LIKE \'%' . addslashes($aParam['sLoupanName']) . '%\'';
            $sCntSQL .= ' AND sContent LIKE \'%' . addslashes($aParam['sLoupanName']) . '%\'';
        }

        //时间换算
        if (!empty($aParam['iSTime'])) {
            $sSQL .= ' AND iCreateTime >=' . strtotime($aParam['iSTime']);
            $sCntSQL .= ' AND iCreateTime >=' . strtotime($aParam['iSTime']);
        }

        if (!empty($aParam['iETime'])) {
            $sSQL .= ' AND iCreateTime <=' . strtotime($aParam['iETime']);
            $sCntSQL .= ' AND iCreateTime <=' . strtotime($aParam['iETime']);
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
            $aRet['aPager'] = Util_Page::getPage($aRet['iTotal'], $iPage, $iPageSize, '', self::_getNewsPageParam($aParam));
        }
        return $aRet;

    }

    private static function _getNewsPageParam(&$aParam)
    {
        $pageParam = array(
            'iIsCheck' => isset($aParam['iIsCheck']) ? $aParam['iIsCheck'] : '',
            'iSTime' => isset($aParam['iSTime']) ? $aParam['iSTime'] : '',
            'iETime' => isset($aParam['iETime']) ? $aParam['iETime'] : '',
            'sOrder' => isset($aParam['sOrder']) ? $aParam['sOrder'] : '',
            'sContent' => isset($aParam['sContent']) ? $aParam['sContent'] : '',
        );
        if (!empty($aParam['iLoupanID'])) {
            $pageParam['iLoupanID'] = $aParam['iLoupanID'];
            $pageParam['sLoupanName'] = isset($aParam['sLoupanName']) ? $aParam['sLoupanName'] : '';
        }
        return $pageParam;

    }

}