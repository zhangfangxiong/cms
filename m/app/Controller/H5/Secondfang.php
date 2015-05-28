<?php

/**
 * Created by PhpStorm.
 * User: len
 * Date: 2015/4/10
 * Time: 11:33
 */
class Controller_H5_Secondfang extends Controller_Base
{
    /**
     * 执行Action前执行
     * @see Yaf_Controller::actionBefore()
     */
    public function actionBefore()
    {
        parent::actionBefore();
    }


    /**
     * 执行Action后的操作
     * @see Yaf_Controller::actionAfter()
     */
    public function actionAfter()
    {
        parent::actionAfter();
    }

    /**
     * 二手房
     */
    public function indexAction()
    {
        //获取城市列表
        $aCityList = Sdk_Cms_Ucenter::city(array('withCode' => 1));
        $aCurrCity = $this->getCurrCity();
        $this->assign('aCityList', $aCityList['data']['list']);
        $this->assign('aCurrCity', $aCurrCity);
        $this->aMeta['title'] .= '二手房';
    }

}