<?php
/**
 * 评测标签控制器.
 * @author: cjj
 */
class Controller_Evaluation_Tag extends Controller_Base
{
    /**
     * 评测标签列表
     */
    public function indexAction ()
    {
        $aParam = array();
        $iPage = intval($this->getParam('page'));
        $aParam['iCatID'] = $this->getParam('iCatID');
        $aParam['iSubCatID'] = $this->getParam('iSubCatID');
        $aParam['sType'] = $this->getParam('sType'); // 优劣势
        $aParam['keywords'] = $this->getParam('keywords');
        $config = Model_EvaluationTag::getConfig();
        // 用于模板页面js
        $childChapterJson  = json_encode($config['chapter']['child']);
        $aList = Model_EvaluationTag::getTagList($aParam,$iPage);
        $this->assign('aParam',  $aParam);
        $this->assign('chapter',  $config['chapter']); // 章节
        $this->assign('yls',  $config['yls']); // 优劣势
        $this->assign('childChapterJson',  $childChapterJson);
        $this->assign('aList', $aList);
    }

    /**
     * 添加评测标签
     */
    public function addAction() {
        $config = Model_EvaluationTag::getConfig();
        if ($this->isPost()) {
            $aParam = $this->_checkData();
            if (empty($aParam)) return null;
            if (!empty($aParam)) {
                $aParam['sType'] = $config['yls'][$aParam['sType']];
                $temp = Model_EvaluationTag::getTagInfoByName($aParam['sName']);
                if (!empty($temp)) {
                    return $this->showMsg('标签名称已存在！', false);
                }
                if (Model_EvaluationTag::addData($aParam) > 0) {
                    return $this->showMsg('标签增加成功！', true);
                } else {
                    return $this->showMsg('标签增加失败！', false);
                }
            }
        } else {
            // 用于模板页面js
            $childChapterJson  = json_encode($config['chapter']['child']);
            $this->assign('chapter',  $config['chapter']); // 章节
            $this->assign('yls',  $config['yls']); // 优劣势
            $this->assign('childChapterJson',  $childChapterJson);
        }
    }

    /**
     * 修改评测标签
     *
     */
    public function editAction()
    {
        $config = Model_EvaluationTag::getConfig();
        if ($this->isPost()) {
            $aParam = $this->_checkData();
            if (empty($aParam)) return null;
            $aParam['iTagID'] = $this->getParam('iTagID');
            try {
                if (Model_EvaluationTag::updateTagInfoById($aParam)) {
                    $this->showMsg('标签信息更新成功！', true);
                } else {
                    $this->showMsg('标签信息更新失败！', false);
                }
            } catch(Exception $e) {
                return $this->showMsg($e->getMessage(), false);
            }
        } else {
            // 用于模板页面js
            $childChapterJson  = json_encode($config['chapter']['child']);
            $iTagId = intval($this->getParam('id'));
            $iTagArr = Model_EvaluationTag::getDetail($iTagId);
            $this->assign('iTagArr',  $iTagArr);
            $this->assign('chapter',  $config['chapter']); // 章节
            $this->assign('yls',  $config['yls']); // 优劣势
            $this->assign('childChapterJson',  $childChapterJson);
        }
    }

    /**
     * 删除评测标签
     */
    public function delAction()
    {
        $tagId = intval($this->getParam('id'));
        if (Model_EvaluationTag::delTagById($tagId)) {
            $this->showMsg('标签信息删除成功！', true);
        } else {
            $this->showMsg('标签信息删除失败！', false);
        }
    }


    /**
     * 请求数据检测
     *
     * @return mixed
     */
    private function _checkData ($sType = 'add')
    {
        $aParam = array();
        $aParam['iCatID'] = intval($this->getParam('iCatID'));
        $aParam['iSubCatID'] = intval($this->getParam('iSubCatID'));
        $aParam['sType'] = intval($this->getParam('sType'));
        $aParam['sName'] = htmlspecialchars($this->getParam('sName'));
        $aParam['sDesc'] = htmlspecialchars($this->getParam('sDesc'));
        if (empty( $aParam['iCatID'])) {
            return $this->showMsg('请选择章节！', false);
        }
        if (empty( $aParam['iSubCatID'])) {
            return $this->showMsg('请选择子章节！', false);
        }
        if (empty( $aParam['sType'])) {
            return $this->showMsg('请选择优劣势！', false);
        }
        if (! Util_Validate::isLength( $aParam['sName'], 2, 100)) {
            return $this->showMsg('请输入优势标签！', false);
        }
        if (! Util_Validate::isLength( $aParam['sDesc'], 2, 100)) {
            return $this->showMsg('请输入使用楼盘！', false);
        }
        return $aParam;

    }

}