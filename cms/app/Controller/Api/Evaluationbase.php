<?php
/**
 * Created by PhpStorm.
 * User: xiejinci
 * Date: 2015/1/27
 * Time: 11:25
 */

class Controller_Api_Evaluationbase extends Controller_Api_Base
{

    public $iEvaluationID = 0;

    CONST IMG_WIDTH = 600;
    /*获取GET参数*/
    protected function getID()
    {
        $this->iEvaluationID = intval($this->getParam('eID')); // 评测报告ID
        $this->iPchapter = intval($this->getParam('pChapterID')); // 父章节ID
        $this->iCchapter = intval($this->getParam('cChapterID')); // 子章节ID
        if(empty($this->iEvaluationID) || $this->iEvaluationID <= 0){
            return $this->showMsg('评测报告ID有误', false);
        }
    }
    /**
     * 执行Action前执行
     * @see Yaf_Controller::actionBefore()
     */
    public function actionBefore ()
    {
      // parent::actionBefore();
       $this ->getID();
    }

    /**
     * 执行Action后的操作
     * @see Yaf_Controller::actionAfter()
     */
    public function actionAfter ()
    {
    }

    protected function filterTag($Tag,$selectIdArr)
    {
        if (empty($selectIdArr)) {
            return null;
        }
        if (empty($Tag)) return null;
        foreach($Tag as $key => $item) {
            if (!in_array($key,$selectIdArr)) {
                unset($Tag[$key]);
            }
        }
        if (empty($Tag)) return null;
        return $Tag;
    }

    protected function formatImages(&$data,$postfix)
    {
        if (!empty($data)) {
            $len = strlen($postfix);
            foreach ($data as $field => $v) {
                if ((strlen($field) > $len) && (substr($field, -$len) == $postfix) && is_array($v)) {
                    if (empty($v)) {
                        continue;
                    }
                    foreach ($data[$field] as $k=> $arr) {
                        // 图片地址转换
                        $data[$field][$k]['sImage']  = Model_EvaluationImage::getMobileImageUrl($arr['iCricId'],$arr['sImage'],self::IMG_WIDTH,1,19,4);
                    }
                }

            }
        }
    }

    // 对所有图片自段 处理
    protected function groupAllImages(&$data,$postfix) {
        if (!empty($data)) {
            $len = strlen($postfix);
            foreach ($data as $field => $v) {
                if ((strlen($field) > $len) && (substr($field, -$len) == $postfix) && is_array($v)) {
                    if (empty($v)) {
                        continue;
                    }
                    // 图片数组 按照 iGroup排序
                    /*
                    $iGroup = array();
                    foreach ($v as $kc => $vc) {
                        $iGroup[$kc] = $vc['iGroup'];
                    }
                    array_multisort($iGroup,SORT_ASC,$data[$field]);
                    */

                    $iGroup = array();
                    foreach ($data[$field] as $k=> $arr) {
                        $iGroup[$arr['iGroup']][] = $k;
                    }
                    foreach ($iGroup as $item) {
                        $count = count($item);
                        if ($count > 1) {
                            unset($item[$count-1]); // 最后描述一个保留
                            foreach($item as  $vc) {
                                $data[$field][$vc]['sDesc'] = '';
                            }
                        }
                    }
                }

            }
        }
    }
   // 对单个图片字段处理
    protected function groupImages($Images) {
        if (empty($Images)) {
            return ;
        }

        /*
        $iGroup = array();
        foreach ($Images as $field => $v) {
            $iGroup[] = $v['iGroup'];
        }
        array_multisort($iGroup,SORT_ASC,$Images);

        $iGroup = array();
        foreach ($Images as $k=> $arr) {
            // 一个图片组 只用一个描述
            if (in_array($arr['iGroup'],$iGroup)) {
                if (isset($Images[$k-1])) {
                    $Images[$k-1]['sDesc'] = '';
                }
            }
            $iGroup[] = $Images[$k]['iGroup'];
        }
        */
        $iGroup = array();
        foreach ($Images as $k=> $arr) {
            $iGroup[$arr['iGroup']][] = $k;
        }
        foreach ($iGroup as $item) {
            $count = count($item);
            if ($count > 1) {
                unset($item[$count-1]); // 最后描述一个保留
                foreach($item as  $vc) {
                    $Images[$vc]['sDesc'] = '';
                }
            }
        }
        return $Images;
    }
}