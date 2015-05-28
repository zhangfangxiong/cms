<?php
/**
 * Created by PhpStorm.
 * User: xiejinci
 * Date: 2015/1/27
 * Time: 11:25
 */

class Controller_CricInevaluation_Base extends Yaf_Controller
{
    const POSTFIX = 'Image';

    const POSTFIX_B = 'Tp';


    public function actionBefore ()
    {
        $this->autoRender(false);
    }
    /**
     * 执行Action后的操作
     * @see Yaf_Controller::actionAfter()
     */
    public function actionAfter ()
    {
    }


    protected function httpRequest($chapter)
    {
        if (empty($chapter)) {
            return null;
        }
        // 时间
        $datetime = $this->getParam('datetime');
        $dateFilter = '';
        if ($datetime) {
            $dateFilter = '&datetime='.urlencode($datetime);
        }
        // 楼盘
        $unitid = $this->getParam('unitid');
        $unitFilter = '';
        if ($unitid) {
            $unitFilter = '&unitid='.$unitid;
        }
        $url =  Yaf_G::getConf('cricEvaluationUrl',null).'?type='.$chapter.$dateFilter.$unitFilter;
        $http = new Util_HttpClient();
        $code = $http -> get($url);
        //echo $url;
        $this -> requestLog($url,$code,$http->content);
        if ($code == 200) {
            return json_decode($http->content,true);
        }
        return null;
    }

    // 请求日志
    protected function requestLog($url,$code,$data)
    {

        global $app;
        $this -> request = $app ->getDispatcher()->getRequest();
        // 脚本自动跑的时候 才执行日志记录
        if ($this -> request->getMethod() == 'CLI') {
            Model_EvaluationCricinLog::addData(
                array(
                    'sUrl' => $url,
                    'sData' => $data,
                    'iCode' => $code,
                    'sTime' => time(),
                )
            );
        }

    }

    // 优劣势标签 文字转换成ID
    protected function convertTag(&$data,$pChapter,$cChapter)
    {
        if (empty($data)) {
            return null;
        }
        $goodTagArr = array();
        $badTagArr = array();
        foreach ($data as $item) {
            if (isset($item['sGoodTag']) && !empty($item['sGoodTag'])) {
                $goodTagArr = array_merge($goodTagArr,$item['sGoodTag']);
            }
            if (isset($item['sBadTag']) && !empty($item['sBadTag'])) {
                $badTagArr = array_merge($badTagArr,$item['sBadTag']);
            }
        }
        // 优势
        if (!empty($goodTagArr)) {
            $dbGoodTagArr = array();
            $where = array();
            foreach($goodTagArr as $item) {
                $where[] = "'".$item."'";
            }
            $result = Model_EvaluationTag::getAll(
                array(
                    'where' => array(
                        'iCatID' => $pChapter,
                        'iSubCatID' => $cChapter,
                        'sName in' => implode(',',$where),
                        'sType' => '优势',
                    )
                )
            );
            if (!empty($result)) {
                foreach($result as $item) {
                    $dbGoodTagArr[$item['sName']] = $item['iTagID'];
                }
            }
        }
        // 劣势
        if (!empty($badTagArr)) {
            $dbBadTagArr = array();
            $where = array();
            foreach($badTagArr as $item) {
                $where[] = "'".$item."'";
            }
            $result = Model_EvaluationTag::getAll(
                array(
                    'where' => array(
                        'iCatID' => $pChapter,
                        'iSubCatID' => $cChapter,
                        'sName in' => implode(',',$where),
                        'sType' => '劣势',
                    )
                )
            );
            if (!empty($result)) {
                foreach($result as $item) {
                    $dbBadTagArr[$item['sName']] = $item['iTagID'];
                }
            }
        }
        foreach($data as &$item) {
            if (isset($item['sGoodTag']) && !empty($item['sGoodTag'])) {
                $goodId = array();
                foreach($item['sGoodTag'] as $v) {
                   if (isset($dbGoodTagArr) && !empty($dbGoodTagArr) && isset($dbGoodTagArr[$v])) {
                           $goodId[] = $dbGoodTagArr[$v];
                   } else {
                       //  数据库没有的，章节设为0，视为其他图片。
                       $insert_id = Model_EvaluationTag::addData(
                           array(
                                'iCatID' => 0,
                                'iSubCatID' => 0,
                                'sName' => $v,
                                'sType' => '优势'
                           )
                       );
                       if ($insert_id> 0) {
                           $dbGoodTagArr[$v] = $insert_id;
                       }
                       $goodId[] = $insert_id;

                   }
               }
                $item['sGoodTag'] = $goodId;
            }

            if (isset($item['sBadTag']) && !empty($item['sBadTag'])) {
                $badId = array();
                foreach($item['sBadTag'] as $v) {
                    if (isset($dbBadTagArr) && !empty($dbBadTagArr) && isset($dbBadTagArr[$v])) {
                        $badId[] = $dbBadTagArr[$v];
                    } else {
                        //  数据库没有的，章节设为0，视为其他图片。
                        $insert_id = Model_EvaluationTag::addData(
                            array(
                                'iCatID' => 0,
                                'iSubCatID' => 0,
                                'sName' => $v,
                                'sType' => '劣势'
                            )
                        );
                        if ($insert_id> 0) {
                            $dbBadTagArr[$v] = $insert_id;
                        }
                        $badId[] = $insert_id;

                    }
                }
                $item['sBadTag'] = $badId;
            }
        }
    }

    /**
     * 获取新增ID 和修改ID
     *
     */
    protected function getAddAndUpdateIdArr(&$data,$class,$autoIdName='iAutoID') {

        $autoIdArr = array();
        if (!empty($data)) {
            foreach ($data as $item) {
                $autoIdArr[] = $item[$autoIdName];
            }
        }

        if (empty($autoIdArr)) {
            return null;
        }
        // 区分新增和修改
        if ($autoIdName == 'iCricId') {
            foreach ($autoIdArr as $item) {
                $idWhere[] = "'".$item."'";
            }
        } else {
            $idWhere = $autoIdArr;
        }

        $result = $class::getAll(array(
            'where' => array(
                "{$autoIdName} in " => implode(',',$idWhere)
            )
        ));
        if (empty($result)) {
            return array('add' => $autoIdArr, 'update' => array());
        }
        $dbIdArr = array();
        foreach($result as $item) {
            $dbIdArr[] = $item[$autoIdName];
        }
        $addArr = array_diff($autoIdArr,$dbIdArr);
        $updateArr = array_diff($autoIdArr,$addArr);
        return array('add' => $addArr, 'update' => $updateArr);
    }

    protected function getAddAndUpdateData(&$data,$IdArr,&$addArray,&$updateArray,$autId = 'iAutoID') {
        if (!empty($data)) {
            foreach ($data as $k => $item) {
                if (isset($item['sGoodTag']) && !empty($item['sGoodTag'])) {
                    $item['sGoodTag'] = implode(',',$item['sGoodTag']);
                } else {
                    $item['sGoodTag'] = '';
                }
                if (isset($item['sBadTag']) && !empty($item['sBadTag'])) {
                    $item['sBadTag'] = implode(',',$item['sBadTag']);
                }  else {
                    $item['sBadTag'] = '';
                }
                if ( empty($item['sComment'])) {
                    $item['sComment'] = '';
                }
                if (empty($item['sOtherComment'])) {
                    $item['sOtherComment'] = '';
                }
                if (empty($item['sContent'])) {
                    $item['sContent'] = '';
                }
                if (isset($IdArr['add']) && in_array($item[$autId],$IdArr['add'])) {
                    $addArray[] = $item;
                }
                if (isset($IdArr['update']) && in_array($item[$autId],$IdArr['update'])) {
                    $updateArray[] = $item;
                }
            }
        }
    }
    /**
     * 保存图片
     */
    protected function saveImages(&$data,$postfix)
    {
        if (!empty($data)) {
            $len = strlen($postfix);
            $images = array();
            foreach($data as $key => $item) {
                foreach($item as $field => $v) {
                    if (((strlen($field)>$len) && (substr($field,-$len)==$postfix) && is_array($v)) || (substr($field,-6)=='Images')) {
                        foreach ($v as $arr) {
                            $arr['iCricId'] = $arr['iAutoID'];
                            unset($arr['iAutoID']);
                            $images[] = $arr;
                        }
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

    /**
     * 过滤字段 把图片的字段都去掉
     */
    protected function filterFields(&$data,$postfix)
    {
        if (!empty($data)) {
            $len = strlen($postfix);
            foreach($data as $key => $item) {
                foreach($item as $field=>$v) {
                    if ((strlen($field)>$len) && (substr($field,-$len)==$postfix) && is_array($v) ) {
                        unset($data[$key][$field]);
                    }
                }
            }
        }
    }
    protected function exitPrograms()
    {
        //exit();
    }
}