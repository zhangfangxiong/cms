<?php

/**
 * Created by PhpStorm.
 * User: len
 * Date: 2015/1/27
 * Time: 11:17
 */
class Controller_API_Banner extends Controller_Api_Base
{
    public function getBannerAction()
    {
        $iModuleID = intval($this->getParam('iModuleID'));
        $iTypeID = intval($this->getParam('iTypeID'));
        $iCityID = intval($this->getParam('iCityID'));
        $sResult =  $this->_getTemplate($iModuleID,$iTypeID,$iCityID);
        if ($sResult) {
            return $this->showMsg($sResult,true);
        } else {
            return $this->showMsg($sResult,false);
        }
    }

    /**
     * @param $iModuleID
     * @param $iTypeID
     * @param $iCityID
     * @return bool
     */
    private function _checkTempData($iModuleID,$iTypeID,$iCityID,$iDataType=0)
    {
        $aData = $this->_getBannerData($iModuleID,$iTypeID,$iCityID,$iDataType);
        if (empty($aData)) {
            return '请先添加广告位';
        }
        if (isset($aData['sError'])) {
            return $aData['sError'];
        }
        $aData['sImage']=Util_Uri::getNewsImgUrl($aData['sImage']);
        return $aData;
    }

    /**
     * 获取模板数据
     * @param $iModuleID
     * @param $iTypeID
     * @param $iCityID
     * $iTempType 0:广告位数据1:新闻数据
     * @return int
     */
    private function _getBannerData($iModuleID,$iTypeID,$iCityID,$iDataType)
    {
        $aData = Model_Banner::getRow(array(
            'where' => array(
                'iModuleID' => $iModuleID,
                'iTypeID' => $iTypeID,
                'iCityID' => $iCityID
            )
        ));
        if (!$iDataType) {
            return $aData;
        } else {
            if (empty($aData)) {
                return $aData;
            } else if ( $aData['iNewsID']) {
                $aNews = Model_News::getDetail($aData['iNewsID']);
                if (empty($aNews)) {
                    $aData['sError'] = "文章ID不存在";
                    return $aData;
                }
                $aCity = Model_City::getPair(array('where' => array('iShow' => 1, 'iStatus' => 1)), 'iCityID', 'sFullPinyin');
                $sUrl = "http://".Yaf_G::getConf('news', 'domain').Util_Page::getUrl('/{city}/news/detail/{iNewsID}', ['city' => $aCity[$iCityID], 'iNewsID' => $aData['iNewsID']]);
                $aNews['sUrl'] = $sUrl;
                $desc = strip_tags($aNews['sContent']);
                if(mb_strlen($desc) > 20){
                    $desc = mb_substr($desc, 0, 20);
                    $desc .= '...';
                };
                $aNews['sContent'] = $desc;
                return $aNews;
            } else {
                $aData['sError'] = "请先添加广告位中的文章ID";
                return $aData;
            }
        }
    }

    /**
     * 获取模板并填充
     * @param $iModuleID
     * @param $iTypeID
     * @return string
     */
    private function _getTemplate($iModuleID,$iTypeID,$iCityID)
    {
        $sTemp = '';
        if ( $iModuleID == 3 && $iTypeID == 1) {//首页广告位1
            $aData = $this->_checkTempData($iModuleID,$iTypeID,$iCityID);//得到模板数据
            if (is_array($aData)) {
                $sImgTemp = $this->_getImgTemplate();
                $sImgTemp = $this->_fillTmplate($sImgTemp,$aData);
            } else {
                return $aData;
            }
            $sTemp = '<section class="ad_wrap_con"><div class="wrap_con clearfix">'.$sImgTemp.'</div></section>';
        } else if ( $iModuleID == 3 && $iTypeID == 2) {//首页广告位2
            $aData = $this->_checkTempData($iModuleID,$iTypeID,$iCityID);//得到模板数据
            if (is_array($aData)) {
                $sImgTemp = $this->_getImgTemplate();
                $sImgTemp = $this->_fillTmplate($sImgTemp,$aData);
            } else {
                return $aData;
            }
            $sTemp = '<section class="ad_wrap_con"><div class="wrap_con clearfix">'.$sImgTemp.'</div></section>';
        } else if ( $iModuleID == 3 && $iTypeID == 3) {//首页列表页
            $aData = $this->_checkTempData($iModuleID,$iTypeID,$iCityID);//得到模板数据
            if (is_array($aData)) {
                $sImgTemp = $this->_getImgTemplate();
                $sImgTemp = $this->_fillTmplate($sImgTemp,$aData);
            } else {
                return $aData;
            }
            $sTemp = '<div style="margin: auto 0px;" class="row mb20">'.$sImgTemp.'</div>';
        } else if ( $iModuleID == 4) {//评测报告
            $aData[] = $this->_checkTempData($iModuleID,1,$iCityID,1);//得到模板数据
            $aData[] = $this->_checkTempData($iModuleID,2,$iCityID,1);//得到模板数据
            $aData[] = $this->_checkTempData($iModuleID,3,$iCityID,1);//得到模板数据

            if (is_array($aData[0])) {
                $sImgTemp = $this->_getMessageTemplateTwo();
                $sMessageOne = $this->_fillTmplate($sImgTemp,$aData[0],2);
            } else {
                return $aData[0];
            }

            if (is_array($aData[1])) {
                $sImgTemp = $this->_getMessageTemplateOne();
                $sMessageTwo = $this->_fillTmplate($sImgTemp,$aData[1],1);
            } else {
                return $aData[1];
            }

            if (is_array($aData[2])) {
                $sImgTemp = $this->_getMessageTemplateOne();
                $sMessageThree = $this->_fillTmplate($sImgTemp,$aData[2],1);
            } else {
                return $aData[2];
            }

            $sTemp = '<div class="con_left clearfix"><div class="common_title"><s class="red_line"></s>楼盘资讯<a target="_blank" class="more_link" href="http://www.fangjiadp.com/shanghai/newslist/1/1">更多<s></s></a></div><div class="dynamic_property_l"><div class="left_shows">'.$sMessageOne.'</div><div class="right_list"><div class="one_children">'.$sMessageTwo.'</div><div class="one_children bdn">'.$sMessageThree.'</div></div></div></div>';
        }
        return $sTemp;
    }

    /**
     * 填充模板内容
     * @param $sTemp
     * @param $aData
     * @param int $iTempType,0:普通文字和图片广告位,1:文章广告位1,2:文章广告位2
     * @return string
     */
    private function _fillTmplate($sTemp,$aData,$iTempType = 0)
    {
        $aParm = array();
        if (!$iTempType) {
            $sHref = $aData['sUrl'];
            $sImgUrl = $aData['sImage'];
            $sResult = sprintf($sTemp,$sHref,$sImgUrl);
        } else if ($iTempType == 1) {
            $aParm[] = $aData['sImage'];
            $aParm[] = $aData['sUrl'];
            $aParm[] = $aData['sTitle'];
            $aParm[] = $aData['sContent'];
            $aParm[] = $aData['sAuthor'];
            $aParm[] = date("Y-m-d",$aData['iCreateTime']);
            $sResult = vsprintf($sTemp,$aParm);
        } else if ($iTempType == 2) {
            $aParm[] = $aData['sUrl'];
            $aParm[] = $aData['sImage'];
            $aParm[] = $aData['sUrl'];
            $aParm[] = $aData['sTitle'];
            $sResult = vsprintf($sTemp,$aParm);
        }
        return $sResult;
    }

    /**
     * 文字广告位
     */
    private function _getTxtTemplate()
    {
        return '<a target="_blank" href="%s">%s</a>';
    }

    /**
     * 图片广告位
     */
    private function _getImgTemplate()
    {
        return '<a target="_blank" href="%s"><img src="%s"></a>';
    }

    /**
     * 文章广告位1
     */
    private function _getMessageTemplateOne()
    {
        return '<div class="child_img"><img src="%s"></div><div class="child_content"><a href="%s" class="child_content_title">%s</a><div class="child_content_content">%s</div><div class="child_content_content author">%s %s</div></div>';
    }

    /**
     * 文章广告位2
     */
    private function _getMessageTemplateTwo()
    {
        return '<a href="%s"><img src="%s"></a><a class="art_title" href="%s">%s</a>';
    }
}