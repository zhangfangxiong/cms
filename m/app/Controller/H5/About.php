<?php

/**
 * Created by PhpStorm.
 * User: hyb
 * Date: 2015/4/22
 * Time: 11:33
 */
class Controller_H5_About extends Controller_Base {

    /**
     * 执行动作之前
     */
    public function actionBefore() {
        parent::actionBefore();
        $this->setFrame('newsh5frame.phtml');
    }

    /**
     * 简介
     * @return
     */
    public function indexAction() {
        
    }

    /**
     * 服务说明
     * @return
     */
    public function agreementAction() {
        
    }

}
