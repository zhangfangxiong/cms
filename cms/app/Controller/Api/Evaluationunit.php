<?php

/**
 * 评测报告 的楼盘信息
 * User: len
 * Date: 2015/1/27
 * Time: 11:17
 */
class Controller_API_Evaluationunit extends Controller_Api_Base
{


    public function actionBefore()
    {
        // parent::actionBefore();
    }

    public function indexAction()
    {
        $unitInfo = $this -> getEvaluationUnitID();
        return $this->showMsg($unitInfo, true);
    }

    // 通过楼盘ID 获取 评测报告ID
    public function evaluationIdAction()
    {
        $unitId  = intval($this -> getParam('unitId'));
        if (empty($unitId)) {
            return $this->showMsg('', false);
        }
        $iEvaluationID = Model_Evaluation::getOne(array(
            'where' => array(
                'iUnitID' => $unitId,
                'iStatus' => 1,
                'sFinished <> '=> '',
            )
        ),'iEvaluationID');
        if (empty($iEvaluationID)) {
            return $this->showMsg('', false);
        } else {
            return $this->showMsg($iEvaluationID, true);
        }
    }

    public function getEvaluationUnitID()
    {
       $result =  Model_Evaluation::getPair(array(
            'where' => array(
                'iStatus' =>1,
                'sFinished <> '=> '',
            )
        ),'iEvaluationID','iUnitID');
        if (empty($result)) {
            return null;
        }
        $untitInfoArr = Model_CricUnit::getPair(array(
            'where' => array(
                'iStatus' =>1,
                'ID in '=>implode(',',$result),
            )
        ),'ID','UnitID');
        if (empty($untitInfoArr)) {
            return  null;
        }
        foreach ($result as $k => $v) {
            if (isset($untitInfoArr[$v])) {
                $result[$k] = $untitInfoArr[$v];
            } else {
                unset($result[$k]);
            }
        }
        return $result;
    }


    /**
     * 根据评测报告ID获取楼盘名称
     */
    public function evaluationUnitNameAction()
    {
        $result = '';
        $eID = intval($this->getParam('eID'));
        if ($eID > 0) {
            $aEvaluation = Model_Evaluation::getDetail($eID);
            if ($aEvaluation) {
                $result = $aEvaluation['sUnitName'];
            }
        }
        $this->showMsg($result, true);
    }

}