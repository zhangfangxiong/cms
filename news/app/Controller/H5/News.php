<?php
/**
 * Created by PhpStorm.
 * User: len
 * Date: 2015/4/10
 * Time: 11:33
 */

class Controller_H5_News extends Controller_Base
{

	/**
     * 执行动作之前
     */
    public function actionBefore () {
    	parent::actionBefore();
        $this->setFrame('newsh5frame.phtml');
    }

    /**
     * 详情页
     * @return
     */
	public function detailAction() {
   	
		$iNewsID = $this->getParam('iNewsID') ? intval($this->getParam('iNewsID')) : 1;

        //更新文章点击数
        $guid = Util_Cookie::get('guid');
        $key = $guid. $iNewsID;
        $memocache = Util_Common::getCache();
        if(!$memocache->get($key)) {
            Sdk_Cms_News::updVisit($iNewsID);
            $memocache->set($key, true, 10);
        }

        $data = Sdk_Cms_News::getDetail($iNewsID);
        $detail = $data['data'];
        if ($data['status'] == false ) {
            throw new Exception('该文章不存在，请联系管理员！');
            return false;
        }

        if(!empty($detail)) {
            if($detail['iStatus'] != 1 || $detail['iPublishStatus'] != 1 || $detail['iPublishTime'] >= time()) {
                throw new Exception('该文章不存在，请联系管理员！');
                return false;
            }
        }

        $iShareTimes = ceil($detail['iVisitNum'] / 10);
        $iShareTimes += $detail['iShareTimes'];
        $detail['iShareTimes'] = $iShareTimes;

        $this->assign('detail', $detail);

        $title = $detail['sShortTitle'];
        $this->generateBreadcrumbs(array("资讯"=>'/'.$this->sCurrCityCode.'/news/index', $title=>0));
        $this->aMeta['title'] = $detail['sShortTitle']. ' - '. $this->aMeta['title'];
    }

}