<?php

/**
 * Created by PhpStorm.
 * User: xiaoyao
 * Date: 2015/1/27
 * Time: 9:29
 */
class Model_Author extends Model_Base
{

    const DB_NAME = 'cms';

    const TABLE_NAME = 't_author';

    const AUTHOR_GUIDE_NEWS = 1;

    const AUTHOR_EVALUATION_NEWS = 2;

    /**
     * 获取作者列表
     * @param $p_aParam
     * @param $p_iPage
     * @param $p_iTypeID 作者类型ID self::AUTHOR_GUIDE_NEWS self::AUTHOR_EVALUATION_NEWS
     */
    public static function getAuthorList(
        $p_aParam,
        $p_iPage,
        $p_iTypeID,
        $p_sOrder = 'iAuthorID DESC',
        $p_iPagesize = 20
    ) {
        $aWhere = [];
        $aWhere['iTypeID'] = $p_iTypeID;
        if (isset($p_aParam['iStatus']) && -1 != $p_aParam['iStatus']) {
            $aWhere['iStatus'] = intval($p_aParam['iStatus']);
        }

        if (isset($p_aParam['sAuthorName'])) {
            $aWhere['sAuthorName LIKE'] = '%' . $p_aParam['sAuthorName'] . '%';
        }

        if (isset($p_aParam['iCityID']) && -1 != $p_aParam['iCityID']) {
            $aWhere['iCityID'] = intval($p_aParam['iCityID']);
        }
        return self::getList($aWhere, $p_iPage, $p_sOrder, $p_iPagesize);
    }

    public static function searchAutoComplete($sKey, $iTypeID)
    {
        $aWhere = ['iTypeID' => $iTypeID];
        $aWhere['iStatus'] = Model_Author::STATUS_VALID;
        $aWhere['sAuthorName LIKE'] = '%' . $sKey . '%';
        $aAuthor = self::getAll(['where' => $aWhere, 'limit' => '0, 10']);
        foreach ($aAuthor as $key => $value) {
            $sCity = Model_City::getCityByID($value['iCityID']);
            $aAuthor[$key]['sCityName'] = $sCity['sCityName'];
        }
        return $aAuthor;
    }

    /**
     * 跟据名字取得作者信息
     * @param unknown $sTagName
     * @return Ambigous <number, multitype:, mixed>
     */
    public static function getAuthorByName($sAuthorName)
    {
        return self::getRow(array(
            'where' => array(
                'sAuthorName' => $sAuthorName
            )
        ));
    }

}