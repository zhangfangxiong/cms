<?php

/**
 * Created by PhpStorm.
 * User: chasel
 * Date:  2015/4/17
 * Time:  15:40
 */
class Controller_H5_Houseloan extends Controller_Base
{
    /**
     * 执行动作之前
     */
    public function actionBefore () {
        parent::actionBefore();
        $this->setFrame('newsh5frame.phtml');
    }

    /**
     * 房贷计算器首页
     */
    public function startAction()
    {
    }

    /**
     * 房贷计算器首页
     */
    public function resultsAction()
    {
    }

    /**
     * 房贷计算器首页
     */
    public function resultsDetailAction()
    {
    }

    /**
     * 房贷计算器首页
     */
    public function resultsFundAction()
    {
    }

    /**
     * 房贷计算器首页
     */
    public function resultsSyndicatedAction()
    {
    }

}