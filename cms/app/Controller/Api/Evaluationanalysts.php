<?php

/**
 * 评测报告分析师接口
 * User: cjj
 * Date: 2015/4/10
 * Time: 11:17
 */
class Controller_API_Evaluationanalysts extends Controller_Api_Base
{

    public function actionBefore()
    {
       // parent::actionBefore();
    }

     /*
     *
     */
    public function indexAction()
    {
        $eID = intval($this -> getParam('eID'));
        $result = Model_Evaluation::getDetail($eID);
        if (empty($result)) {
            return $this->showMsg('评测报告ID有误', false);
        }
        $analysts = Model_Analysts::getDetail($result['iAnalysisID']);
        if (empty($analysts)) {
            return $this->showMsg('分析师不存在', false);
        } else {
            $result = array();
            $result['AnalystsName'] = $analysts['AnalystsName'];
            $result['AnalystLevel'] = $analysts['AnalystLevel'];
            $result['AnalystHead'] = !$analysts['AnalystHead'] ? '' :Model_EvaluationImage::getMobileImageUrl(1,$analysts['AnalystHead'],160,160,0, 0, 1);
            return $this->showMsg($result, true);
        }

    }
}