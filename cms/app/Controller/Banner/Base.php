<?php
/**
 * Created by PhpStorm.
 * User: xiaoyao
 * Date: 2015/2/3
 * Time: 15:22
 */

abstract class Controller_Banner_Base extends Controller_Base
{

    protected function _getModule()
    {
    }

    protected function _getModuleType()
    {
    }

    protected function _assignUrl()
    {

    }

    public function actionAfter()
    {
        parent::actionAfter();
        $this->_assignUrl();
    }


    protected function _checkData()
    {
    }


}