<?php

/**
 * 评测报告分析师接口
 * User: xcy
 * Date: 2015/5/5
 * Time: 11:17
 */
class Controller_Ajax_Analyst extends Controller_Ajax_Base {

    /**
     * add message ajax
     * 分析师咨询提交
     */
    public function sendQuestionAction () {
        $aQuestion = [];
        $aParam = [];
        $aParam['analystID'] = intval($this->getParam('id'));
        $aParam['content'] = addslashes($this->getParam('content'));
        $aParam['userName'] = addslashes($this->getParam('username'));
        $aParam['mobile'] = addslashes($this->getParam('mobile'));

        foreach ($aParam as $key => $value) {
            $bool = true;
            if(!$value) {
                $bool = false;
                $msg = $key.'不能为空';
                return $this->showMsg($msg, false);   
            }
        }

        if($bool) {
            $aRet = Sdk_Cms_Analyst::sendQuestion($aParam);
            $aQuestion = $aRet['status'] ? $aRet['data'] : [];
            return $this->showMsg($aQuestion, true);
        }

        return $this->showMsg('提交失败', false);
    }
}