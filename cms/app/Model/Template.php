<?php

/**
 * Created by PhpStorm.
 * User: xiaoyao
 * Date: 2015/1/29
 * Time: 13:43
 */
class Model_Template extends Model_Base
{
    const DB_NAME = 'cms';
    const TABLE_NAME = 't_template';

    const MODULE_NEWS_HOME_CAROUSEL = 1; //资讯首页轮播图
    /**
     * 初始化模板
     * @param $aParam
     * @return int
     */

    public static function initTmplate($aParam)
    {
        $iModuleType = Model_Banner::$aModuleType[$aParam['iModuleID']];
        $aParam['sDesc'] = $iModuleType[$aParam['iTypeID']]['sTitle'];
        $aParam['iPublishStatus'] = 1;
        $aParam['iStatus'] = 1;
        $aParam['iCreateTime'] = $aParam['iUpdateTime'] = time();
        $aParam['iUpdateUserID'] = $aParam['iCreateUserID'];
        return self::addData($aParam);
    }

    /**
     * 根据模板ID获取模板
     * @param $sID
     * @return array
     */
    public static function getTmplateByID($sID)
    {
        return self::getDetail($sID);
    }

    /**
     * 根据模块ID和种类ID获取模板数据
     * @param $iModuleID 模块ID
     * @param $iTypeID 种类ID
     */
    public static function getTemlateData($iModuleID,$iTypeID)
    {
        return self::getRow(array(
            'where' => array(
                'iModuleID' => $iModuleID,
                'iTypeID' => $iTypeID
            )
        ));
    }
    /**
     * 获取模块代码
     */
    public static function getModulContent()
    {

    }

    /**
     * 填充模板内容
     */
    public static function fillTmplate()
    {
        $aData = Model_Banner::getBanner();
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
            $aRet['aPager'] = Util_Page::getPage($aRet['iTotal'], $iPage, $iPageSize,'',$aParam); // update by cjj 2015-02-13 分页增加query 参数
        }
        return $aRet;
    }
}