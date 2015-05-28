<?php

class Controller_Cmd_Statistical extends Controller_Cmd_Base
{
    /**
     * 更新tag表下iNewsNum字段记录的文章数目
     */
    public function updatetagnewsnumAction()
    {
        /*
        $all_tags = Model_Tag::getAllTags();
        if (!empty($all_tags)) {
            foreach ($all_tags as $key => $value) {
                $tag_num = Model_News::getNewsNumByTagID($value['iTagID']);
                if ($value['iNewsNum'] == $tag_num) {
                    continue;
                }
                Model_Tag::updateTagNewsNum($value['iTagID'], $tag_num);
            }
        }
        */
        $iNewsNum = Model_News::getNewsNum();                           //有效文章数
        $iStart = 0;                                                    //取文章开始数
        $iLimit = 1000;                                                  //每次遍历文章数目
        $sWhere = "iStatus = 1 AND iPublishStatus = 1 ";                //条件
        $order = "ORDER BY iCreateTime";                                //排序
        $iCycleNum = ceil($iNewsNum / $iLimit);                         //遍历总次数
        $num_arr = array();                                             //统计的tag下文章数数组
        for ($i = 1; $i <= $iCycleNum; $i++) {
            $sSql = "SELECT sTag FROM t_news WHERE " . $sWhere . $order . " LIMIT " . $iStart . "," . $iLimit;
            $iStart = $i * $iLimit;
            $aNews = Model_News::query($sSql);
            foreach ($aNews as $key => $value) {
                if ($value['sTag']) {
                    $aTag = explode(',', $value['sTag']);
                    foreach ($aTag as $v) {
                        if (Model_Tag::getTagsByID($v)) {
                            if (isset($num_arr[$v])) {
                                $num_arr[$v] += 1;
                            } else {
                                $num_arr[$v] = 1;
                            }
                        }
                    }
                }
            }
        }
        if (!empty($num_arr)) {
            foreach ($num_arr as $k => $val) {
                //不用判断tagID是否存在，上面已经做了判断
                if (Model_Tag::getTagNewsNumByID($k) != $val) {
                    Model_Tag::updateTagNewsNum($k,$val);
                }
            }
        }
    }
}