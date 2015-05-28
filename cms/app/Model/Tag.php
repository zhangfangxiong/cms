<?php

class Model_Tag extends Model_Base
{

    const DB_NAME = 'cms';

    const TABLE_NAME = 't_tag';

    const TAG_GUIDE_NEWS = 1;    //导购文章

    const TAG_EVALUATION_NEWS = 2;    //评测文章
    
    const TAG_TOPIC = 3;

    /**
     * 跟据分类名称取得分类
     * @param unknown $sTagName
     * @return Ambigous <number, multitype:, mixed>
     */
    public static function getTagByName ($iTypeID, $sTagName)
    {
        return self::getRow(array(
        'where' => array(
            'iTypeID' => $iTypeID,
            'sTagName' => $sTagName,
            'iStatus' => 1
        )
    ));
    }
    
    /**
     * 取得所有启用分类的ID => Name数组
     * @return Ambigous <number, multitype:multitype:, multitype:unknown >
     */
    public static function getPairTags ($iTypeID)
    {
        return self::getPair(array('where' => array('iTypeID' => $iTypeID, 'iStatus' => 1)), 'iTagID', 'sTagName');
    }


    public static function searchAutoComplete($iTypeID, $iCityID, $sKey)
    {
        $aWhere = ['iTypeID' => $iTypeID];
        $aWhere['iStatus'] = self::STATUS_VALID;
        if ($iCityID > 0) {
            $aWhere['iCityID IN'] = [$iCityID, 0];
        } else {
            $aWhere['iCityID'] = 0;
        }
        $aWhere['sTagName LIKE'] = '%' . $sKey . '%';
        return self::getAll(['where' => $aWhere, 'limit' => '0, 10']);
    }

    public static function getNewsTag($mCityID, $iTypeID)
    {

        $aWhere = ['iTypeID' => $iTypeID];
        $aWhere['iStatus'] = self::STATUS_VALID;
        if (is_array($mCityID)) {
            $aWhere['iCityID IN'] = $mCityID;
        } else {
            $aWhere['iCityID'] = $mCityID;
        }
        return self::getPair(['where' => $aWhere], 'iTagID', 'sTagName');
    }
    
    public static function getHotTag($iCityID, $iTypeID, $orderBy, $pageSize)
    {
        if(!empty($iTypeID) && $iTypeID > 0)
        {
            $aWhere = ['iTypeID' => $iTypeID];
        }
        $aWhere['iStatus'] = self::STATUS_VALID;
        $aWhere['iCityID'] = $iCityID;

        return self::getPair(['where' => $aWhere, 'order' => $orderBy. ' desc', 'limit' => '0, '.$pageSize], 'iTagID', 'sTagName');
    }


    /**
     * 找到tag表所有的有效标签信息
     * @return int
     */
    public static function getAllTags ()
    {
        return self::getAll(array(
            'where' => array(
                'iStatus' => 1,
            )
        ));
    }
    /**
     * 根据标签ID获取当前标签下的文章数
     * $iStatus 状态
     * @return int
     */
    public static function getTagNewsNumByID($iTagID)
    {
        return self::getOne(array(
            'where' => array(
                'iTagID' => $iTagID
            )
        ), 'iNewsNum');
    }

    /**
     * 更新tag表下的文章统计字段
     * @param int $iTagID
     * @param int $iNum
     * @return int
     */
    public static function updateTagNewsNum($iTagID,$iNum)
    {
        $aRow = array(
            'iTagID'=>$iTagID,
            'iNewsNum'=>$iNum
        );
        $iRet = Model_Tag::updData($aRow);
    }

    /**
     * 根据标签ID判断标签是否存在
     * $iStatus 状态
     * @return int
     */
    public static function getTagsByID ($iTagID,$iStatus=1)
    {
        return self::getRow(array(
            'where' => array(
                'iStatus' => $iStatus,
                'iTagID' => $iTagID
            )
        ));
    }

    /**
     * 根据标签ID取得标签名字
     * $iStatus 状态
     * @return int
     */
    public static function getTagByIDs ($iTagID,$iStatus=1)
    {
        return self::getPair(array('where' => array('iTagID IN' => $iTagID, 'iStatus' => $iStatus)), 'iTagID', 'sTagName');
    }

}