<?php
/**
 * Created by PhpStorm.
 * User: xiejinci
 * Date: 2015/1/27
 * Time: 11:25
 */

class Controller_Api_Evaluationimages extends Controller_Api_Base
{

    public function actionBefore()
    {
        // parent::actionBefore();
    }


    public function indexAction()
    {
        $eID = intval($this -> getParam('eID'));
        if (empty($eID)) {
            return $this->showMsg(null, false);
        }
        $result = Model_EvaluationImage::getAll(array(
            'where' => array(
                    'iEvaluationID' => $eID,
                    'iStatus' => 1,
            ),
            'order' => 'iCreateTime DESC',
        ));
        if (!empty($result)) {
            foreach($result as &$item) {
                $item['sImage']  = Model_EvaluationImage::getMobileImageUrl($item['iCricId'],$item['sImage'],600,1,19,4);
            }
        }
        return $this->showMsg($result, true);
    }
}