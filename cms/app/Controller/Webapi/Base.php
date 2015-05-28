<?php
/**
 * Created by PhpStorm.
 * User: xiejinci
 * Date: 2015/1/27
 * Time: 11:25
 */

class Controller_Webapi_Base extends Yaf_Controller
{
    /**
     * 执行Action前执行
     * @see Yaf_Controller::actionBefore()
     */
    public function actionBefore ()
    {

        $iTime = $this->getParam('_time', 0);
        if (time() - $iTime > 1800) {
            return $this->showMsg('验签失效!', false);
        }
        $sSign = $this->getParam('_sign');
        if ($sSign != md5($iTime . Yaf_G::getConf('signkey', 'sdk'))) {
            return $this->showMsg('验签失败!', false);
        }
    }

    /**
     * 执行Action后的操作
     * @see Yaf_Controller::actionAfter()
     */
    public function actionAfter ()
    {
    }
}