<?php

/**
 * 评论页
 * Date: 2015/2/25
 * author:xcy
 * Time: 11:27
 */
class Controller_Newhouse_Comment extends Controller_NewHouseBase
{

    //评论首页
    public function IndexAction() {
    	$id = intval($this->getParam('unitid'));
       	
    	//获取楼盘分析列表
    	$aAnalysts = Sdk_Cms_Xf::getAnalyst($id);
    	$aAnalysts = ((1 == $aAnalysts['status']) && $aAnalysts['data']) ? $aAnalysts['data']['list'] : [];

    	//所有点评列表
    	$aComment = Sdk_Cms_Xf::getAllComments($id);
    	$aComment = (1 == $aComment['status']) ? $aComment['data'] : [];
    	
    	//楼盘状况
    	$aInfo = Sdk_Cms_Newhouse::unitDetail($id);
    	$aInfo = (1 == $aInfo['status']) ? $aInfo['data'] : [];

    	//感兴趣的楼盘
    	
    	$this->assign('aAnalysts', $aAnalysts);
    	$this->assign('aComment', $aComment);
    	$this->assign('aInfo', $aInfo);
    }
}


?>