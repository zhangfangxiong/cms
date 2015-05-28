<?php
/**
 * 不利因素接口
 * User: cjj
 * Date: 2015/03/16
 * Time: 11:17
 */
class Controller_CricInevaluation_Badfactor extends Controller_CricInevaluation_Base
{

    const P_CHAPTER = 7; // 当前章节
    /**
     * 内部
     */
    public function inSideAction()
    {
        // 获取接口数据
        $data = self::httpRequest(Model_EvaluationBlInside::CHAPTER);
        // 标签文字转ID
        self::convertTag($data,self::P_CHAPTER,Model_EvaluationBlInside::CHAPTER);
        if (!empty($data)) {
            // 新增和修改ID
            $IdArr = $this->getAddAndUpdateIdArr($data, 'Model_EvaluationBlInside');
            $addArray = array();
            $updateArray = array();
            // 获取新增和修改数据
            $this->getAddAndUpdateData($data,$IdArr,$addArray,$updateArray);
            // 去除图片字段
            $this->filterFields($addArray,self::POSTFIX);
            $this->filterFields($updateArray,self::POSTFIX);
            // 保存数据
            Model_EvaluationBlInside::addBatchData($addArray);
            Model_EvaluationBlInside::updateBatchData($updateArray);
            // 保存图片
            parent::saveImages($data,self::POSTFIX);
        }
    }

    /**
     * 外部
     */
    public function outSideAction()
    {
        // 获取接口数据
        $data = self::httpRequest(Model_EvaluationBlOutside::CHAPTER);
        // 标签文字转ID
        self::convertTag($data,self::P_CHAPTER,Model_EvaluationBlOutside::CHAPTER);
        if (!empty($data)) {
            // 新增和修改ID
            $IdArr = $this->getAddAndUpdateIdArr($data, 'Model_EvaluationBlOutside');
            $addArray = array();
            $updateArray = array();
            // 获取新增和修改数据
            $this->getAddAndUpdateData($data,$IdArr,$addArray,$updateArray);
            // 保存数据
            Model_EvaluationBlOutside::addBatchData($addArray);
            Model_EvaluationBlOutside::updateBatchData($updateArray);
            // 保存图片
            self::saveImages($data);
        }
    }

    /**
     * 保存 相关图
     */
    public function saveImages(&$data,$postfix = '')
    {
        /*
             $data[aImage] => Array
             (
                 [1303] => Array
                 (
                     [101] => Array (
                         )
                )
            )
        */
        // 获取标签ID
        $tagId  = array();
        foreach($data as $item) {
            if (!empty($item['sBadTag'])) {
                $tagId = array_merge($tagId,$item['sBadTag']);
            }
        }

        $result = [];
        if (!empty($tagId)) {

            $result = Model_EvaluationTag::getAll(array(
                'where' => array(
                    'iTagID in ' => implode(',',$tagId)
                )
            ));
        }
        $tagArr = array();
        if (!empty($result)) {
            foreach($result as $item) {
                $tagArr[$item['sName']] = $item['iTagID'];
            }
        }
        unset($result);
        $images = array();
        foreach($data as $item) {
            if (!empty($item['aImage'])) {
                foreach ($item['aImage'] as $typeArr) {
                    if (isset($typeArr[702]) && !empty($typeArr[702])) {
                        foreach ($typeArr[702] as $v) {
                            $v['iCricId'] = $v['iAutoID'];
                            $v['iTargetID'] =  isset($tagArr[$v['iTargetID']]) ? $tagArr[$v['iTargetID']] :0; // 这里的$v['iTargetID']是字符串(接口过来的)
                            unset($v['iAutoID']);
                            $images[] = $v;
                        }
                    }
                }
            }
            if (!empty($item['OtherImages'])) { //其他图片
                foreach ($item['OtherImages'] as $v) {
                    $v['iCricId'] = $v['iAutoID'];
                    unset($v['iAutoID']);
                    $images[] = $v;
                }
            }
        }
        $addArray = array();
        $updateArray = array();
        // 获取新增和修改ID
        $IdArr = $this->getAddAndUpdateIdArr($images,'Model_EvaluationImage','iCricId');
        // 获取新增和修改数据
        $this->getAddAndUpdateData($images,$IdArr,$addArray,$updateArray,'iCricId');
        Model_EvaluationImage::addBatchData($addArray);
        Model_EvaluationImage::updateBatchData($updateArray);
    }

}