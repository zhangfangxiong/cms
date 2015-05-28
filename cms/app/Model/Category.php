<?php

class Model_Category extends Model_Base
{

    const DB_NAME = 'cms';

    const TABLE_NAME = 't_category';

    const CATEGORY_GUIDE_NEWS = 1;    //导购文章

    const CATEGORY_EVALUATION_NEWS = 2;    //评测文章

    const CATEGORY_FEED = 3;    //动态

    const CATEGORY_PICTORIAL = 4; //画报

    /**
     * 跟据分类名称取得分类
     *
     * @param unknown $sCategoryName
     * @return Ambigous <number, multitype:, mixed>
     */
    public static function getCategoryByName($iTypeID, $sCategoryName)
    {
        return self::getRow(array(
            'where' => array(
                'iTypeID'       => $iTypeID,
                'sCategoryName' => $sCategoryName,
                'iStatus' => 1
            )
        ));
    }

    /**
     * 取得所有启用分类的ID => Name数组
     *
     * @return Ambigous <number, multitype:multitype:, multitype:unknown >
     */
    public static function getPairCategorys($iTypeID)
    {
        return self::getPair(array(
            'where' => array(
                'iTypeID' => $iTypeID,
                'iStatus' => 1
            )
        ), 'iCategoryID', 'sCategoryName');
    }
}