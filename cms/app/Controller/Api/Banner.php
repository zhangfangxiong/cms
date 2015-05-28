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
        $aTypeID = explode(',', $this->getParam('iTypeID'));//iTypeID可一次性传多个
        $sCity = $this->getParam('sCity');

        $aCity = Model_City::getCityByFullPinyin($sCity);
        if (empty($aCity)) {
            return $this->showMsg('', false);//城市不存在
        }
        $iCityID = $aCity['iCityID'];
        $sResult = $this->_getTemplate($iModuleID, $aTypeID, $iCityID);
        return $this->showMsg($sResult, true);
    }

    /**
     * @param $iModuleID
     * @param $iTypeID
     * @param $iCityID
     * @return bool
     */
    private function _checkTempData($iModuleID, $iTypeID, $iCityID, $iDataType = 0)
    {
        $aData = $this->_getBannerData($iModuleID, $iTypeID, $iCityID, $iDataType);
        if (empty($aData)) {
            return '';//请先添加广告位
        }
        if (isset($aData['sError'])) {
            return $aData['sError'];
        }
        if (isset($aData['sImage'])) {
            if ($iModuleID == 4 && empty($aData['sImage'])) {
                $aData['sImage'] = 'http://' . Yaf_G::getConf('static', 'domain') . '/img/news/not_exists.jpg';
            } else {
                $aData['sImage'] = $iModuleID == 4 ? Util_Uri::getNewsImgUrl($aData['sImage']) : Util_Uri::getNewsImgUrl($aData['sImage'], 0, 0, 0, "banner");
            }
        }
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
    private function _getBannerData($iModuleID, $iTypeID, $iCityID, $iDataType)
    {
        if ($iModuleID == 5) {
            $ilimit = 5;
        } elseif ($iModuleID == 6) {
            $ilimit = 3;
        } else {
            $ilimit = 1;
        }
        $aParam = array(
            'where' => array(
                'iModuleID' => $iModuleID,
                'iTypeID' => $iTypeID,
                'iCityID' => $iCityID,
                'iStatus' => 1,
                'iPublishStatus' => 1
            ),
            'order' => 'iUpdateTime DESC',
            'limit' => $ilimit
        );
        if ($iModuleID == 5) {
            $aData = Model_Banner::getAll($aParam);
        } elseif ($iModuleID == 6) {
            $aData = Model_Banner::getAll($aParam);
        } else {
            $aData = Model_Banner::getRow($aParam);
        }

        if (!$iDataType) {
            return $aData;
        } else {
            if (empty($aData)) {
                return $aData;
            } else {
                if ($aData['iNewsID']) {
                    $aNews = Model_News::getDetail($aData['iNewsID']);
                    if (empty($aNews)) {
                        $aData['sError'] = "";//文章ID不存在
                        return $aData;
                    }
                    $aCity = Model_City::getPair(array('where' => array('iShow' => 1, 'iCityID' => $aNews['iCityID'], 'iStatus' => 1)), 'iCityID',
                        'sFullPinyin');
                    $sUrl = "http://" . Yaf_G::getConf('news',
                            'domain') . Util_Page::getUrl('/{city}/news/detail/{iNewsID}',
                            ['city' => $aCity[$aNews['iCityID']], 'iNewsID' => $aData['iNewsID']]);
                    $aNews['sUrl'] = $sUrl;
                    $desc = $aNews['sAbstract'];
                    if (mb_strlen($desc) > 50) {
                        $desc = mb_substr($desc, 0, 50);
                        $desc .= '...';
                    };
                    $aNews['sContent'] = $desc;
                    return $aNews;
                } else {
                    $aData['sError'] = "";//请先添加广告位中的文章ID
                    return $aData;
                }
            }
        }
    }

    /**
     * 获取模板并填充
     * @param $iModuleID
     * @param $iTypeID
     * @return string
     */
    private function _getTemplate($iModuleID, $aTypeID, $iCityID)
    {
        $sTemp = '';
        if ($iModuleID == 3) {//首页广告位1,2,列表页广告位3
            foreach ($aTypeID as $key => $value) {
                $aData = $this->_checkTempData($iModuleID, $value, $iCityID);//得到模板数据
                if (is_array($aData)) {
                    $sImgTemp = $this->_getImgTemplate();
                    $sImgTemp = $this->_fillTmplate($sImgTemp, $aData);
                } else {
                    $sImgTemp = $aData;
                }
                if ($value == 1 || $value == 2) {
                    $sTemp = '<section id="adImg' . $value . '" class="ad_wrap_con"><div class="wrap_con clearfix">' . $sImgTemp . '</div></section>';
                } else if ($value == 3) {
                    $sTemp = '<div id="adImg3" style="margin: auto 0px;" class="row mb20">' . $sImgTemp . '</div>';
                }

            }
        } else if ($iModuleID == 4) {//评测报告
            foreach ($aTypeID as $key => $value) {
                $aData = $this->_checkTempData($iModuleID, $value, $iCityID, 1);//得到模板数据
                if (is_array($aData)) {
                    if ($value == 1) {
                        $sImgTemp = $this->_getMessageTemplateTwo();
                        $sMessage[$value] = $this->_fillTmplate($sImgTemp, $aData, 2);
                    } else if ($value == 2) {
                        $sImgTemp = $this->_getMessageTemplateOne();
                        $sMessage[$value] = $this->_fillTmplate($sImgTemp, $aData, 1);
                    } else if ($value == 3) {
                        $sImgTemp = $this->_getMessageTemplateOne();
                        $sMessage[$value] = $this->_fillTmplate($sImgTemp, $aData, 1);
                    }
                } else {
                    $sMessage[$value] = $aData;
                }
            }
            $sTemp = '<div class="dynamic_property_l">
                            <div id="child1" class="left_shows">' . $sMessage[1] . '</div>
                            <div class="right_list">
                                <div id="child2" class="one_children">' . $sMessage[2] . '</div>
                                <div id="child3"  class="one_children bdn">' . $sMessage[3] . '</div>
                            </div>
                        </div>';
        } elseif ($iModuleID == 5) {//首页轮播图广告位
            foreach ($aTypeID as $key => $value) {
                $aData = $this->_checkTempData($iModuleID, $value, $iCityID);//得到模板数据
                $sTrigger = '<ul class="triggers">';
                if (is_array($aData)) {
                    $sImgTemp = $this->_getImgTemplate();
                    $sTemps = '';
                    $i = 1;
                    foreach ($aData as $k => $val) {
                        if (!empty($val['sImage'])) {
                            $val['sImage'] = Util_Uri::getNewsImgUrl($val['sImage'], 0, 0, 0, "banner");
                        }
                        if ($i == 1) {
                            $sTrigger .= '<li class="current">'.$i.'</li>';

                        } else {
                            $sTrigger .= '<li class="">'.$i.'</li>';

                        }
                        $i++;
                        $sTemps .= $this->_fillTmplate($sImgTemp, $val);
                    }
                    $sImgTemp = $sTemps;
                } else {
                    $sImgTemp = $aData;
                }
                $sTrigger .= '</ul>';

                $sTemp = '<div id="picSlider" class="slider_con clearfix">' . $sImgTemp . '</div>'.$sTrigger;

            }
        } elseif ($iModuleID == 6) {//首页副图广告位
            foreach ($aTypeID as $key => $value) {
                $aData = $this->_checkTempData($iModuleID, $value, $iCityID);//得到模板数据
                if (is_array($aData)) {
                    $sImgTemp = $this->_getLiTemplate();
                    $sTemps = '';
                    foreach ($aData as $k => $val) {
                        if (!empty($val['sImage'])) {
                            $val['sImage'] = Util_Uri::getNewsImgUrl($val['sImage'], 0, 0, 0, "banner");
                        }
                        $sTemps .= $this->_fillTmplate($sImgTemp, $val,3);
                    }
                    $sImgTemp = $sTemps;
                } else {
                    $sImgTemp = $aData;
                }
                $sTemp = '<div id="index_mid_banner"><ul class="index_mid_ul">' . $sImgTemp . '</ul></div>';
            }
        }
        $sStatic = "http://" . Yaf_G::getConf('static', 'domain');
        $sTemp = '<link rel="stylesheet" type="text/css" href="' . $sStatic . '/css/news/homeNews.css">' . $sTemp;
        return $sTemp;
    }

    /**
     * 填充模板内容
     * @param $sTemp
     * @param $aData
     * @param int $iTempType ,0:普通文字和图片广告位,1:文章广告位1,2:文章广告位2,3:li广告位
     * @return string
     */
    private function _fillTmplate($sTemp, $aData, $iTempType = 0)
    {
        $aParm = array();
        if (!$iTempType) {
            $sHref = $aData['sUrl'];
            $sImgUrl = $aData['sImage'];
            $sResult = sprintf($sTemp, $sHref, $sImgUrl);
        } else {
            if ($iTempType == 1) {
                $aParm[] = $aData['sUrl'];
                $aParm[] = $aData['sImage'];
                $aParm[] = $aData['sUrl'];
                $aParm[] = $aData['sShortTitle'];
                $aParm[] = $aData['sShortTitle'];
                $aParm[] = $aData['sAbstract'];
                $aParm[] = $aData['sContent'];
                $aParm[] = $aData['sAuthor'];
                $aParm[] = date("Y-m-d", $aData['iPublishTime']);
                $sResult = vsprintf($sTemp, $aParm);
            } elseif ($iTempType == 2) {
                $aParm[] = $aData['sUrl'];
                $aParm[] = $aData['sImage'];
                $aParm[] = $aData['sTitle'];
                $aParm[] = $aData['sTitle'];
                $aParm[] = $aData['sAuthor'];
                $aParm[] = date("Y-m-d", $aData['iPublishTime']);
                $sResult = vsprintf($sTemp, $aParm);
            } elseif ($iTempType == 3) {
                $aParm[] = $aData['sUrl'];
                $aParm[] = $aData['sImage'];
                $aParm[] = $aData['sDesc'];
                $sResult = vsprintf($sTemp, $aParm);
            }
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
     * li标签广告位
     * @return string
     */
    private function _getLiTemplate()
    {
        return '<li title=""><a href="%s" ><img src="%s"></a><p class="font_name">%s</p></li>';
    }

    /**
     * 文章广告位1
     */
    private function _getMessageTemplateOne()
    {
        return '<div class="child_img">
                    <a href="%s" target="_blank" class="hd"><img src="%s"></a>
                </div>
                <div class="child_content">
                    <a href="%s" class="child_content_title" target="_blank" title="%s">%s</a>
                    <div class="child_content_content" title="%s">%s</div>
                     <div class="author" ><span>%s</span> <span>%s</span></div>
                </div>';
    }

    /**
     * 文章广告位2
     */
    private function _getMessageTemplateTwo()
    {
        return '<a href="%s" target="_blank">
            <img src="%s">
           <div  class="art_title">
               <div class="art_title_title" title="%s">%s</div>
               <div class="art_auther" ><span>%s</span> <span>%s</span></div>
           </div>
        </a>';
    }


    public function newscarouselAction()
    {
        $iCityID = intval($this->getParam('iCityID'));
        $aBannerList = Model_Banner::getAll(
            [
                'where' => [
                    'iCityID' => $iCityID,
                    'iStatus' => Model_Banner::STATUS_VALID,
                    'iPublishStatus' => 1,
                    'iModuleID' => Model_Banner::MODULE_NEWS_HOME_CAROUSEL,
                    'iPublishTime <=' => time()
                ],
                'order' => 'iUpdateTime DESC'
            ]
        );
        $aList = $this->_formatNewsCarousel(Model_Banner::formatData($aBannerList));
        return $this->showMsg($aList, true);
    }

    protected function _formatNewsCarousel($aList)
    {
        if ($aList) {
            foreach ($aList as $key => $value) {
                unset($aList[$key]['sDesc']);
                unset($aList[$key]['sContent']);
                unset($aList[$key]['sTitle']);
                unset($aList[$key]['iOrder']);
                unset($aList[$key]['iStatus']);
                unset($aList[$key]['iCreateUserID']);
                unset($aList[$key]['iUpdateUserID']);
                unset($aList[$key]['iPublishStatus']);
            }
        }
        return $aList;
    }


    public function newsadvertiseAction()
    {
        $iCityID = intval($this->getParam('iCityID'));
        $aBannerList = Model_Banner::getAll(
            [
                'where' => [
                    'iCityID' => $iCityID,
                    'iStatus' => Model_Banner::STATUS_VALID,
                    'iPublishStatus' => 1,
                    'iModuleID' => Model_Banner::MODULE_NEWS_HOME_AD,
                    'iTypeID' => Model_Banner::TYPE_RIGHT_AD,
                    'iPublishTime <=' => time()
                ],
                'order' => 'iUpdateTime DESC'
            ]
        );
        $aRightAD = [];
        if ($aBannerList) {
            $aRightAD = $aBannerList[0];
            unset($aRightAD['sContent']);
            unset($aRightAD['sTitle']);
            unset($aRightAD['iOrder']);
            unset($aRightAD['iStatus']);
            unset($aRightAD['iCreateUserID']);
            unset($aRightAD['iUpdateUserID']);
            unset($aRightAD['iPublishStatus']);

        }

        return $this->showMsg($aRightAD, true);
    }
}