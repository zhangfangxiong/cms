<?php
/**
 * 标签接口
 * User: cjj
 * Date: 2015/03/16
 * Time: 11:17
 */
class Controller_CricInevaluation_Tags extends Controller_CricInevaluation_Base
{


    /**
     * 标签
     */
    public function indexAction()
    {
        // 获取接口数据
        $request = $this->getRequest();
        $request->setParam('datetime','2015-01-01');
        $data = self::httpRequest(60);
        Model_EvaluationTag::addBatchData($data);
    }

}