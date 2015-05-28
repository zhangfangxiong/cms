<?php
/**
 * Created by PhpStorm.
 * User: yaobiqing
 * Date: 15/4/8
 * Time: 下午6:21
 */


class Controller_Cmd_Unittest extends Controller_Cmd_Base
{

    public function trenddateAction()
    {
        print_r(Model_CricBatchTypeFull::getPriceTrendData('4AFB260D-6216-4582-A3CA-D102F78714DF', '普通住宅'));
    }

    public function ListTypeAction()
    {
        var_dump(Model_CricUnitZT0114::getCurrYearListType('上海'));
        $typeList = Model_CricUnitZT0114::getListTypeByCityName('上海', '2014-01-01', '年度');
        var_dump($typeList);
        $iTime = strtotime('2014-01-01');
        $year = date('Y年', $iTime);
        $year2 = date('Y', $iTime);

        $regArr = [
            $year, $year2, '第一季度', '一季度', '楼盘', '性价比', 'TOP3', 'TOP5', 'TOP10', '测评榜'
        ];

        $replaceArr = [
            '', '', '', '', '', '', '', '', '', '榜单'
        ];
        $typeList = Model_CricUnitZT0114::replaceListTitle($typeList, $regArr, $replaceArr);
        var_dump($typeList);

        var_dump(Model_CricUnitZT0114::getUnitListType('7C1F2C5B-A0B0-47DE-A279-AAA676E5760D'));
    }
}