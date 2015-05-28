<?php

/**
 * 新房详情
 * Date: 2015/2/2
 * author:cjj
 * Time: 13:27
 */
class Controller_Newhouse_Loudetail extends Controller_NewHouseBase
{

    // 楼盘详情
    public function IndexAction()
    {
        $this->assign('iMenuIndex', 6);
        $aLouDetail = Sdk_Cms_Newhouse::louDetail($this->louID);
        if (!$aLouDetail['status']) {
            $this->showMsg('请求失败', true);
        }
        //楼盘信息
        $this->assign('LouInfo', isset($aLouDetail['data']) ? $aLouDetail['data'] : array());
        //楼盘详细信息
        $this->assign('LouDetail', isset($aLouDetail['data']['detailInfo']) ? $aLouDetail['data']['detailInfo'] : array());
        //楼盘评分信息
        $this->assign('aComment', isset($aLouDetail['data']['comment']) ? $aLouDetail['data']['comment'] : array());
        //获取星星评分信息
        $this->assign('sStar', isset($aLouDetail['data']['comment']['sRate']) ? Model_Util::getStar($aLouDetail['data']['comment']['sRate']) : '');
        //获取户型图
        $aParam = array(
            'buildingID' => $this->louID,
        );
        $aHxImage = Sdk_Cms_Newhouse::gethximage($aParam);
        $aHxData = array();
        if (!empty($aHxImage['data'])) {
            foreach ($aHxImage['data']['list'] as $mkey => &$mvalue) {
                $temp = $mvalue['sImageUrl'];
                $mvalue['sImageUrl'] = $this->getImageUrl(1,$temp,160,160);
                $mvalue['sImageUrlBig'] = $this->getImageUrl(1,$temp,430,315);
                $aHxData[$mvalue['typeName']][] = $mvalue;
            }
        }
        //户型图信息
        $this->assign('aHxData', $aHxData);//分好格式的
        $this->assign('aHxImage', isset($aHxImage['data']['list']) ? $aHxImage['data']['list'] : array());//原始数据，用于全部
        //获取楼盘相册
        $aTypeArr = array(
            1 => '实景图',
            2 => '效果图',
            3 => '总平图',
            5 => '楼盘周边图'
        );
        $aParam = array(
            'buildingID' => $this->louID,
            'width' => 160,
            'height' => 160,
        );
        $aImages = array();
        $aImagesNum = 0;
        foreach ($aTypeArr as $key => $value) {
            $aParam['type'] = $key;
            $aImages[$key]['Url'] = array();
            $aImages[$key]['gigUrl'] = array();
            $aImages[$key]['sRemark'] = array();
            $aTmp = Sdk_Cms_Newhouse::getImageByType($aParam);
            if (!empty($aTmp['data'])) {
                $aImages[$key]['Url'] = $aTmp['data']['list'];
                $aImagesNum +=  count($aImages[$key]['Url']);
            }
            $aParam['width'] = 639;
            $aParam['height'] = 480;
            $aTmp1 = Sdk_Cms_Newhouse::getImageByType($aParam);
            if (!empty($aTmp1['data'])) {
                $aImages[$key]['bigUrl'] = $aTmp1['data']['list'];
            }
            $aImages[$key]['sRemark'] = $aTmp['data']['sRemark'];
        }
        $this->assign('aTypeArr', $aTypeArr);
        $this->assign('aImages', $aImages);
        $this->assign('aImagesNum', $aImagesNum);
        //获取效果图的轮播图
        $aParam['width'] = 97;
        $aParam['height'] = 70;
        $aLitterImage = Sdk_Cms_Newhouse::getImageByType($aParam);
        $this->assign('aLitterImage', isset($aLitterImage['data']['list']) ? $aLitterImage['data']['list'] : array());//轮播图小图
        $aParam['width'] = 500;
        $aParam['height'] = 300;
        $aLargeImage = Sdk_Cms_Newhouse::getImageByType($aParam);
        $this->assign('aLargeImage', isset($aLargeImage['data']['list']) ? $aLargeImage['data']['list'] : array());//轮播图大图
        $this->assign("aUnit",$this->unitInfo["unit"]);
    }
}