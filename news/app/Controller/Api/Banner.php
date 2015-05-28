<?php

/**
 * Created by PhpStorm.
 * User: len
 * Date: 2015/1/27
 * Time: 11:17
 */
class Controller_Api_Banner extends Controller_Api_Base
{
    //首页广告位1
    public function getHomeIndexOneAction()
    {
        $sCurrAction = __FUNCTION__;
        return $this->_getApi($sCurrAction);
    }

    //首页广告位2
    public function getHomeIndexTwoAction()
    {
        $sCurrAction = __FUNCTION__;
        return $this->_getApi($sCurrAction);
    }

    //列表页广告位1
    public function getListIndexOneAction()
    {
        $sCurrAction = __FUNCTION__;
        return $this->_getApi($sCurrAction);
    }

    //楼盘资讯
    public function getLouNewsAction()
    {
        $sCurrAction = __FUNCTION__;
        return $this->_getApi($sCurrAction);
    }

    //首页轮播图
    public function getHomeCarouselAction()
    {
        $sCurrAction = __FUNCTION__;
        return $this->_getApi($sCurrAction);
    }

    //首页专题页
    public function getHomeVicepicAction()
    {
        $sCurrAction = __FUNCTION__;
        return $this->_getApi($sCurrAction);
    }

    private function _getApi($sActionName)
    {
        $sActionName = substr($sActionName,0,$sActionName-6);
        $sCity = $this->getParam('sCity');
        if (!$sCity) {
            echo "";
            return false;
        }
        $aRet = Sdk_Cms_Banner::$sActionName($sCity);
        echo $aRet['data'];
        return false;
    }


}