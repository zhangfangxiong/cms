<?php
/**
 * Created by PhpStorm.
 * User: xiaoyao
 * Date: 2015/1/27
 * Time: 11:25
 */

class Controller_Ajax_Base extends Controller_Base
{
    /**
     * 是否进行登录检测
     * @var unknown
     */
    protected $bCheckLogin = true;

    /**
     * 执行Action前执行
     * @see Yaf_Controller::actionBefore()
     */
    public function actionBefore ()
    {
        $this->aCurrProject = Yaf_G::getConf('project');
        // 当前用户
        $aCookie = Util_Cookie::get(Yaf_G::getConf('authkey', 'cookie'));
        if (empty($aCookie)) {
            if ($this->bCheckLogin) {
                $this->showMsg('未登录', false);
            }
        } else {
            $this->aCurrUser = $aCookie;
            // 当前城市
            $this->iCurrCityID = Util_Cookie::get('city');
            if (empty($this->iCurrCityID)) {
                $this->iCurrCityID = $this->aCurrUser['iCityID'];
            }
        }
    }

    /**
     * 执行Action后的操作
     * @see Yaf_Controller::actionAfter()
     */
    public function actionAfter ()
    {
    }


    public function setCheckLogin($bCheckLogin)
    {
        $this->bCheckLogin = $bCheckLogin;
    }

}