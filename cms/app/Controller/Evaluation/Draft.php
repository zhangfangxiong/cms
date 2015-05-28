<?php
/**
 * 评测草稿控制器.
 * @author: cjj
 */
class Controller_Evaluation_Draft extends Controller_Base
{

    const TP_PATH = '/Evaluation/Draft/evaluationProcess.phtml';

     /**
     *  评测草稿列表
     */
    public function indexAction()
    {
        $iPage = intval($this->getParam('page'));
        $aParam['lpName'] = $this->getParam('lpName'); // 楼盘名称
        $aParam['author'] = $this->getParam('author');
        $aParam['iCreateTime'] = $this->getParam('createTime');
        $aParam['iUpdateTime'] = $this->getParam('updateTime');
        $aParam['iCityID'] = $this->iCurrCityID;
        $aCity = Model_City::getDetail($this->iCurrCityID);
        $aList = Model_Evaluation::getEvaluationList($aParam,$iPage,2);
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

    /**
     * 评测报告完成情况
     *
     * */
    public function progressAction()
    {
        $eID = intval($this->getParam('eID'));
        $arr = Model_Evaluation::getDetail($eID);
        $sFinished = !empty($arr) && !empty($arr['sFinished']) ? explode(',',$arr['sFinished']) :array(); // 已完成章节
        $chapter = Yaf_G::getConf('chapter',null,'evaluation'); // 章节配置
        $isFinished = Model_Evaluation::isFinished($eID); // 是否全部完成
        $html =  $this->getView()->render(self::TP_PATH,array(
                'sFinished' => $sFinished,
                'chapter' => $chapter,
                'isFinished' => $isFinished,
                'eID' => $eID
            ));
        return  $this->showMsg($html, true);
    }

     /**
     * 发布评测报告
     *
     */
    public function publishAction() {
        $eID = intval($this->getParam('eID'));
        try
        {
            Model_Evaluation::publishEvaluation($eID);
            return  $this->showMsg('评测报告发布成功！', true);
        }catch (Exception $e) {
            return $this->showMsg($e->getMessage(), false);
        }

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
            $v['url'] = '/evaluation/Hxanalyse/?eID='.$v['iEvaluationID'];
            if (!empty($v['sLastEditChapter'])) {
                $v['url'] = $chapterUrl[$v['sLastEditChapter']]."/?eID=".$v['iEvaluationID']."&cID=".$v['sLastEditChapter'];
            }
        }

    }
    /**
     * 新增评测草稿
     *
     */
    public function addAction() {

        if ($this->isPost()) {
            $louPanId = $this->getParam('lpID');
            $sLouPanName = $this->getParam( 'sLoupanName');
            $sCityCode = $this->getParam( 'sCityCode');
            if (empty($louPanId)) { // 没选择 直接复制进去
                $arr = Model_CricUnit::getLoupanIDByName($sCityCode,trim($sLouPanName));
                if (empty($arr)) {
                    return $this->showMsg('楼盘信息不存在！', false);
                } else {
                    $louPanId = $aParam['iUnitID'] =  $arr['ID'];
                    $aParam['sUnitName'] =  $arr['UnitName'];
                }
            } else {
                $louPanArr = Model_CricUnit::getLoupanNames($louPanId);
                if (empty($louPanArr)) {
                    return $this->showMsg('楼盘信息不存在！', false);
                }
                $aParam['iUnitID'] = $louPanId;
                $aParam['sUnitName'] = $louPanArr[$louPanId];
            }
            $louPanCityCode = Model_CricUnit::getPair(
                [
                    'where' => [
                        'ID' => $louPanId
                    ]
                ],
                'ID', 'CityCode'
            );
            $aCity = Model_City::getCityByFullPinyin($louPanCityCode[$louPanId]);
            $aParam['iCityID'] = $aCity['iCityID'];
            $aParam['iAnalysisID'] = $this->getParam('sAnalystsID');
            if(Model_Evaluation::addDraft($aParam)) {
                return  $this->showMsg('评测报告添加成功！', true);
            } else {
                return $this->showMsg('评测报告添加失败！', false);
            }
        }
    }


     /**
     *  修改分析师
     *
     */
    public function editAnalystAction()
    {
        $aParam['iEvaluationID'] = intval($this->getParam('eID'));
        $aParam['iAnalysisID'] = intval($this->getParam('iAnalysisID'));
        Model_Evaluation::updData($aParam);
        return  $this->showMsg('success', true);
    }



}