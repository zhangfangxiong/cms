<?php

/**
 * 新房详情
 * Date: 2015/2/2
 * author:cjj
 * Time: 13:27
 */
class Controller_Newhouse_Evaluation extends Controller_NewHouseBase
{

    public $p = 1;

    public function IndexAction()
    {

        $this -> p = intval($this->getParam('p')) <= 0 ? 1 :intval($this->getParam('p'));
        $chapterData = Model_UnitEvaluation::getChapterData($this->unitInfo['unit']['ID'],$this->p);
        $this->assign('chapterData', $chapterData);
        $this->assign('curPage', $this -> p);

    }
}