<?php
/**
 * 评测列表控制器.
 * @author: cjj
 */
class Controller_Evaluation_Index extends Controller_Base
{

     /**
     * 评测列表
     *
     */
    public function listAction()
    {
        $iPage = intval($this->getParam('page'));
        $aParam['lpName'] = $this->getParam('lpName'); // 楼盘名称
        $aParam['author'] = $this->getParam('author');
        $aParam['iCreateTime'] = $this->getParam('createTime');
        $aParam['iUpdateTime'] = $this->getParam('updateTime');
        $aParam['iCityID'] = $this->iCurrCityID;
        $aCity = Model_City::getDetail($this->iCurrCityID);
        $aList = Model_Evaluation::getEvaluationList($aParam,$iPage,1);
        $config = Model_Evaluation::getConfig();
        $createUserArr = $this->_getUserName($aList['aList'],'iCreateUserID'); // 作者
        $updateUserArr = $this->_getUserName($aList['aList'],'iUpdateUserID'); // 编辑人
        $chapterUrl = Model_Evaluation::getChapterUrl();
        $this->_setListChapterUrl($aList['aList'],$chapterUrl); // 设置最后编辑章节的URL
        $this->assign('analysts',Model_Analysts::getAnalystsList($this->iCurrCityID)); // 当前城市分析师
        $this->assign('aCity', $aCity);
        $this->assign('createUserArr',$createUserArr);
        $this->assign('updateUserArr',$updateUserArr);
        $this->assign('childChapter',self::_getChildChapter($config['chapter']['child']));
        $this->assign('aParam',$aParam);
        $this->assign('aList', $aList);
    }

    /*
     *  设置列表中 编辑章节的跳转URl
     *
     * */
    private function _setListChapterUrl(&$list,&$chapterUrl)
    {
        if (empty($list)) {
            return null;
        }
        foreach ($list as &$v) {
            $v['url'] = '/evaluation/Hxanalyse/?eID=' . $v['iEvaluationID'];
            if (!empty($v['sLastEditChapter'])) {
                $v['url'] = $chapterUrl[$v['sLastEditChapter']] . "/?eID=" . $v['iEvaluationID'] . "&cID=" . $v['sLastEditChapter'];
            }
            $v['sFrontUrl'] = $this->_setFrontPageUrl($v['iUnitID']);

        }

    }

    /**
     * @param $unitID
     * 根据楼盘ID评测该评测报告的www页面地址
     */
    private function _setFrontPageUrl($unitID)
    {
        $aLoupan = Model_CricUnit::getLoupanByID($unitID);
        $sUrl = '';
        if (!empty($aLoupan)) {
            $sUrl = Model_CricUnit::getUnitEvaluationUrl($aLoupan['CityCode'], $aLoupan['RegionName'], $aLoupan['DistrictName'], $unitID);
        }
        return $sUrl;
    }
        /*
         *  获取子章节
         * */
    private function _getChildChapter(&$chapter)
    {
        $arr = array();
        foreach($chapter as $k => $item) {
            foreach($item as $kc => $vc) {
                $arr[$kc] = $vc;
            }
        }
        return $arr;
    }
    /*
     * 获取列表中的用户名
     */
    private function _getUserName(&$list,$field) {
        if (empty($list) || empty($field)) {
            return null;
        }
        $userIdArr = array();
        foreach ($list as $v) {
            $userIdArr[] = $v[$field];
        }
        $userNameArr = array();
        if (!empty($userIdArr)) {
            $userInfoArr = Model_User::getPKIDList($userIdArr);
            if (!empty($userInfoArr)) {
                foreach ($userInfoArr as $item) {
                    $userNameArr[$item['iUserID']] = $item['sUserName'];
                }
            }
        }
        return $userNameArr;

    }

}