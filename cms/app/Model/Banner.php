<?php

/**
 * Created by PhpStorm.
 * User: xiaoyao
 * Date: 2015/1/29
 * Time: 13:43
 */
class Model_Banner extends Model_Base
{
    const DB_NAME = 'cms';
    const TABLE_NAME = 't_banner';
    const WHERE_CACHE = false;

    const MODULE_NEWS_HOME_CAROUSEL = 1; //资讯首页轮播图
    const MODULE_NEWS_HOME_AD = 2; //资讯首页广告
    const MODULE_HOME_AD = 3;//首页广告位
    const MODULE_HOME_LOUARTICLE_AD = 4;//首页资讯广告位
    const MODULE_HOME_CAROUSEL_AD = 5;//首页轮播图广告位
    const MODULE_HOME_CICEPIC_AD = 6;//首页副图广告位


    const TYPE_NEWS = 1;
    const TYPE_TOPIC = 2;
    const TYPE_RIGHT_AD = 3;

    const TYPE_HOME_INDEX_ONE = 1;//首页banner1
    const TYPE_HOME_INDEX_TWO = 2;//首页banner2
    const TYPE_HOME_LIST_ONE = 3;//列表页banner1

    const TYPE_HOME_LOUARTICE_ONE = 1;//首页资讯广告位1
    const TYPE_HOME_LOUARTICE_TWO = 2;//首页资讯广告位2
    const TYPE_HOME_LOUARTICE_THREE = 3;//首页资讯广告位3

    const TYPE_HOME_CICEPIC_ONE = 1;//首页副图广告位1
    const TYPE_HOME_CICEPIC_TWO = 2;//首页副图广告位2
    const TYPE_HOME_CICEPIC_THREE = 3;//首页副图广告位3

    const PUBLISH_INIT = 0;
    const PUBLISH_ONLINE = 1;
    const PUBLISH_OFFLINE = 2;

    public static $aModuleType = [
        self::MODULE_NEWS_HOME_CAROUSEL => [
            self::TYPE_NEWS => ['sTitle' => '资讯文章轮播图', 'iMin' => 2, 'iMax'=> 5]
        ],

        self::MODULE_NEWS_HOME_AD => [
            self::TYPE_RIGHT_AD => ['sTitle' => '资讯首页-右下广告位', 'iMin' => 1, 'iMax'=> 1]
        ],

        self::MODULE_HOME_AD => [
            self::TYPE_HOME_INDEX_ONE => ['sTitle' => '首页-广告位1', 'iMin' => 1, 'iMax'=> 1],
            self::TYPE_HOME_INDEX_TWO => ['sTitle' => '首页-广告位2', 'iMin' => 1, 'iMax'=> 1],
            self::TYPE_HOME_LIST_ONE => ['sTitle' => '列表页-广告位1', 'iMin' => 1, 'iMax'=> 1]
        ],

        self::MODULE_HOME_LOUARTICLE_AD => [
            self::TYPE_HOME_LOUARTICE_ONE => ['sTitle' => '首页-资讯广告位1', 'iMin' => 1, 'iMax'=> 1],
            self::TYPE_HOME_LOUARTICE_TWO => ['sTitle' => '首页-资讯广告位2', 'iMin' => 1, 'iMax'=> 1],
            self::TYPE_HOME_LOUARTICE_THREE => ['sTitle' => '首页-资讯广告位3', 'iMin' => 1, 'iMax'=> 1]
        ],
        self::MODULE_HOME_CAROUSEL_AD => [
            self::TYPE_NEWS => ['sTitle' => '首页轮播图', 'iMin' => 2, 'iMax'=> 5]
        ],
        self::MODULE_HOME_CICEPIC_AD => [
            self::TYPE_HOME_CICEPIC_ONE => ['sTitle' => '首页-副图广告位', 'iMin' => 1, 'iMax'=> 3]
        ],
    ];

    public static $aPublishStatus = [
        self::PUBLISH_INIT => '未发布',
        self::PUBLISH_ONLINE => '已发布',
        self::PUBLISH_OFFLINE => '已下架'
    ];

    public static function getBanner($p_aParam, $p_iLimit = 100, $p_sOrder = 'iUpdateTime DESC')
    {
        $aWhere = ['iStatus' => 1];

        if (isset($p_aParam['iModuleID'])) {
            $aWhere['iModuleID'] = intval($p_aParam['iModuleID']);
        }

        if (isset($p_aParam['iCityID'])) {
            $aWhere['iCityID'] = intval($p_aParam['iCityID']);
        }

        if (isset($p_aParam['iTypeID'])) {
            $aWhere['iTypeID'] = intval($p_aParam['iTypeID']);
        }

        if (isset($p_aParam['iPublishStatus'])) {
            $aWhere['iPublishStatus'] = intval($p_aParam['iPublishStatus']);
        }
        $aList = self::getAll(
            [
                'where' => $aWhere,
                'order' => $p_sOrder,
                'limit' => $p_iLimit
            ]
        );
        return $aList;
    }

    public static function formatData($aList) {
        if ($aList) {
            foreach ($aList as $key => $value) {
                switch ($value['iTypeID']) {
                    case self::TYPE_NEWS:
                    default:
                        $aList[$key] = self::formatNews($value);
                        break;
                }
            }
        }
        return $aList;
    }

    public static function formatNews($value)
    {
        $aNews = Model_News::getDetail(intval($value['sContent']));
        $value['aNews'] = [];
        if (!empty($aNews)) {
            unset($aNews['sContent']);
            $aNews['aTag'] = [];
            if(!empty($aNews['sTag'])) {
                $aNews['aTag'] = Model_Tag::getTagByIDs($aNews['sTag']);
            }
            $value['aNews'] = $aNews;
        }

        return $value;
    }
    

}