<?php

/**
 * 评测报告章节菜单接口
 * User: len
 * Date: 2015/1/27
 * Time: 11:17
 */
class Controller_API_Evaluationchapter extends Controller_Api_Base
{

    public function actionBefore()
    {
        // parent::actionBefore();
    }


    /**
     * 章节菜单
     */
    public function indexAction()
    {
        $chapter =  Yaf_G::getConf('chapter',null,'evaluation');
        // 获取已完成章节
        $sFinished = $this -> getFinishedChapter();
        $arr = $this -> setDisable($sFinished,$chapter['action']);
        $chapter['parentIfNull']= '';
        $chapter['sonIfNull'] = '';
        if (isset($arr['parentIfNull'])) {
            $chapter['parentIfNull'] = $arr['parentIfNull'];
            $chapter['sonIfNull'] = $arr['sonIfNull'];
        }
        return $this->showMsg($chapter, true);
    }

    public function getFinishedChapter()
    {
        $eID = $this->getParam('eID');
        $result = Model_Evaluation::getDetail($eID);
        if (empty($result)) {
            return $this->showMsg('评测报告ID有误', false);
        }
        $sFinished = $result['sFinished'];
        if (empty($sFinished)) {
            $sFinished = array();
        } else {
            $sFinished = explode(',',$sFinished);
        }
        return $sFinished;
    }

    public function setDisable($sFinished,$chapter)
    {
        if (empty($chapter) || empty($sFinished)) {
            return ;
        }
        foreach ($chapter as $pChapter => $item) {
            $index = 0;
            foreach ($item as $cChapter => $v) {
                if (!in_array($cChapter,$sFinished)) {
                    $index++;
                    $arr['sonIfNull'][$cChapter] = 0;
                } else {
                    $arr['sonIfNull'][$cChapter] = 1;
                }
            }
            if (count($item) == $index) {
                $arr['parentIfNull'][$pChapter] = 0;
            } else {
                $arr['parentIfNull'][$pChapter] = 1;
            }
        }
        return $arr;
    }
}