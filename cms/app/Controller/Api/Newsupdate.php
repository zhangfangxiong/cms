<?php

/**
 * Created by PhpStorm.
 * User: len
 * Date: 2015/1/27
 * Time: 11:17
 * type:修改数据库API
 */
class Controller_API_Newsupdate extends Controller_Api_Base
{
    /**
     * 文章访问量统计
     * param iNewsID 文章ID
     */
    public function statisNewsVisitAction()
    {
        $iNewsID = intval($this->getParam('iNewsID'));
        $iCurr_num = Model_News::updateNewsDayStatis($iNewsID,1);
        if ($iCurr_num) {
            return $this->showMsg($iCurr_num."统计成功",true);
        } else {
            return $this->showMsg("统计失败",false);
        }
    }

    /**
     * 文章分享量统计
     * param iNewsID 文章ID
     */
    public function statisNewsShareAction()
    {
        $iNewsID = intval($this->getParam('iNewsID'));
        $iCurr_num = Model_News::updateNewsDayStatis($iNewsID,2);
        if ($iCurr_num) {
            return $this->showMsg($iCurr_num."统计成功",true);
        } else {
            return $this->showMsg("统计失败",false);
        }
    }

    /**
     * 文章点赞量统计
     * param iNewsID 文章ID
     */
    public function statisNewsGoodAction()
    {
        $iNewsID = intval($this->getParam('iNewsID'));

        if (!$aNews = Model_News::getNewsByID($iNewsID)) {
            return $this->showMsg("点赞失败", false);
        }

        //mysql统计总点赞量
        $aNews['iGoodNum'] += 1;
        if (!Model_News::updData($aNews)) {
            return $this->showMsg("点赞失败", false);
        }
        
        return $this->showMsg("点赞成功", true);
    }


}