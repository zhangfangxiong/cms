<?php

/*
 * 评测相关图片管理类
 * @author cjj
 */

class Model_EvaluationImage extends Model_Base
{

    const DB_NAME = 'cms';

    const TABLE_NAME = 't_evaluation_image';

    /*
     * 添加评测相关图  不包含 标题和图片说明
     * @param int $eId 评测报告ID
     * @param int $iTargetID 目标表ID
     * @param int $type 图片所属类型
     * @param array $images 图片
     */
    public static function addImages($eId, $type, $images,$iTargetID=0)
    {
        if (empty($eId) || empty($type) || empty($images)) {
            return;
        }
        $aParam = array(
            'iEvaluationID' => $eId,
            'iTargetID' => $iTargetID,
            'iTypeID' => $type,
        );
        foreach ($images as $v) {
            $aParam['sImage'] = $v;
            self::addData($aParam);
        }
        return;
    }

    /*
     * 批量添加图片
     * @param $eID 评测报告ID
     * @param $images array('type' => 图片数组)
     */
    public static function addBatchImages($eID, $images)
    {
        if (empty($eID) || empty($images)) {
            return false;
        }
        foreach ($images as $type => $item) {
            if (empty($item)) continue;
            self::addImages($eID, $type, $item);
        }
    }

    /*
   * 批量添加图片 包含图片标题 和说明
   * @param $eID 评测报告ID
   * @param $images array('type' => 图片数组)
   */
    public static function addBatchImagesAndDesc($eID, $images, $title, $desc, $upIdArr = null)
    {
        $tempArr = array();
        if (!empty($images)) {
            $tempArr =  $images;
        }
        if (empty($tempArr) && !empty($title)) {
            $tempArr =  $title;
        }
        if (empty($tempArr) && !empty($desc)) {
            $tempArr =  $desc;
        }
        if (empty($tempArr)) {
            return ;
        }
        foreach ($tempArr as $type => $arr) {
            if (!isset($upIdArr[$type])) $upIdArr[$type] = null;
            if (!isset($title[$type])) $title[$type] = null;
            if (!isset($desc[$type])) $desc[$type] = null;
            if (!isset($images[$type])) $images[$type] = null;
            self::addImagesAndDesc($eID, $type, $images[$type], $title[$type], $desc[$type], $upIdArr[$type]);
        }

    }

    /*
     * 添加评测相关图 包含 标题和图片说明
     * @param int $eID 评测报告ID
     * @param int $type 图片所属类型
     * @param array $images 图片
     * @param array $desc 图片说明
     * @param array $upId 修改 是主键ID
     * @param int $targetId 目标表ID
     */
    public static function addImagesAndDesc($eID, $type, $images, $names, $desc, $upIdArr = null,$targetId=0)
    {

        if (empty($eID) || empty($type)) {
            return;
        }
        $aParam = array(
            'iEvaluationID' => $eID,
            'iTargetID'=> $targetId,
            'iTypeID' => $type,
        );

        $IDArr = array();
        // 只修改标题和描述
        if (!empty($upIdArr)) {
            foreach ($upIdArr as $k => $v) {
                $v = intval($v);
                if (empty($v)) {
                    continue;
                }
                $aUpdateParam['sDesc'] = '';
                $aUpdateParam['sTitle'] = '';
                $aUpdateParam['iAutoID'] = $v;
                if (isset($desc[$k])) {
                    $aUpdateParam['sDesc'] = $desc[$k];
                    unset($desc[$k]);
                }
                if (isset($names[$k])) {
                    $aUpdateParam['sTitle'] = $names[$k];
                    unset($names[$k]);
                }
                $IDArr[] = $v;
                self::updData($aUpdateParam);
            }
        }
        if (!empty($images)) {
            if (!empty($desc)) $desc = array_values($desc);
            if (!empty($names)) $names = array_values($names);
            unset($aParam['iAutoID']);
            // 添加
            foreach ($images as $k => $v) {
                if (empty($v)) {
                    continue;
                }
                $aParam['sImage'] = $v;
                $aParam['sDesc'] = '';
                $aParam['sTitle'] = '';
                if (isset($desc[$k])) $aParam['sDesc'] = $desc[$k];
                if (isset($names[$k])) $aParam['sTitle'] = $names[$k];
                $IDArr[] = self::addData($aParam);
            }
        }
        return $IDArr;
    }

 /*
 * 添加公共资源相关图 包含 标题和图片说明（只有区域配套中的公共资源添加修改才调用此方法）
 * @param int $eID 评测报告ID
 * @param int $type 图片所属类型
 * @param array $images 图片
 * @param array $desc 图片说明
 * @param array $upId 修改 是主键ID
 * @param int $targetId 公共资源建设状态
 */
    public static function addImagesPublic($eID, $type, $images, $names, $desc, $upIdArr = null,$iTypeImage)
    {

        if (empty($eID) || empty($type)) {
            return;
        }
        $aParam = array(
            'iEvaluationID' => $eID,
            //'iTypeImage'=> $iTypeImage,
            'iTypeID' => $type,
        );
        $IDArr = array();
        // 只修改标题和描述
        if (!empty($upIdArr)) {
            foreach ($upIdArr as $k => $v) {
                $v = intval($v);
                if (empty($v)) {
                    continue;
                }
                $aUpdateParam['sDesc'] = '';
                $aUpdateParam['sTitle'] = '';
                $aUpdateParam['iTypeImage'] = '';
                $aUpdateParam['iAutoID'] = $v;
                if (isset($desc[$k])) {
                    $aUpdateParam['sDesc'] = $desc[$k];
                    unset($desc[$k]);
                }
                if (isset($names[$k])) {
                    $aUpdateParam['sTitle'] = $names[$k];
                    unset($names[$k]);
                }
                if (isset($iTypeImage[$k])) {
                    $aUpdateParam['iTypeImage'] = $iTypeImage[$k];
                    unset($iTypeImage[$k]);
                }
                $IDArr[] = $v;
                self::updData($aUpdateParam);
            }
        }
        if (!empty($images)) {
            if (!empty($desc)) $desc = array_values($desc);
            if (!empty($names)) $names = array_values($names);
            if (!empty($iTypeImage)) $iTypeImage = array_values($iTypeImage);
            unset($aParam['iAutoID']);
            // 添加
            foreach ($images as $k => $v) {
                if (empty($v)) {
                    continue;
                }
                $aParam['sImage'] = $v;
                $aParam['sDesc'] = '';
                $aParam['sTitle'] = '';
                $aParam['iTypeImage'] = '';
                if (isset($desc[$k])) $aParam['sDesc'] = $desc[$k];
                if (isset($names[$k])) $aParam['sTitle'] = $names[$k];
                if (isset($iTypeImage[$k])) $aParam['iTypeImage'] = $iTypeImage[$k];
                $IDArr[] = self::addData($aParam);
            }
        }
        return $IDArr;
    }

    /*
     * 获取评测相关图片
     * @param int $eID 评测报告ID
     * @param int|array $type  类型
     */
    public static function getImages($eID, $type)
    {
        if (empty($eID) || empty($type)) {
            return null;
        }
        if (!is_array($type)) {
            $type = array($type);
        }
        $aParam = array(
            'where' => array(
                'iEvaluationID' => $eID,
                'iTypeID in' => implode(',', $type),
                'iStatus' => 1
            ),
            'order' => 'iIndex asc,iCreateTime asc',
        );
        $result = self::getOrm()->fetchAll($aParam);
        $newArr = array();
        if (sizeof($type) == 1) {
            return $result;
        } else {
            // 按类型分组
            if (!empty($result)) {
                foreach ($result as $item) {
                    $newArr[$item['iTypeID']][] = $item;
                }
            }
            return $newArr;
        }
    }

    /*
     * 删除图片
     * @param array $IDS 主键
     */
    public static function delImages($IDS)
    {
        if (empty($IDS)) {
            return false;
        }
        unset(self::getOrm()->sTitle);
        unset(self::getOrm()->sDesc);
        foreach ($IDS as $id) {
            self::delData(intval($id));
        }
    }
    /*
     * 删除图片
     * @param array $IDS 主键
     */
    public static function delImagesByTargetId($idArr,$typeArr)
    {
        if (empty($idArr) || empty($typeArr)) {
            return;
        }
        $aParam = array(
            'where' => array(
                'iTargetID in' => implode(',', $idArr),
                'iTypeID in' => implode(',', $typeArr),
                'iStatus' => 1
            )
        );
        $result = self::getOrm()->fetchAll($aParam);
        if (!empty($result)) {
            $arr = array();
            foreach($result as $k => $v) {
                $arr[] = $v['iAutoID'];
            }
            self::delImages($arr);
        }


    }

    /*
     * @param int $eID 评测报告ID
     * @param int $type 图片所属类型
     * @param array $images array('主键ID' => array(图片),) 图片数组
     * * @param array $title array('主键ID' => array(标题),) 图片标题
     * @param array $desc  array('主键ID' => array(说明),) 图片说明
     * @param array $upId 修改 是主键ID
    */
    public static function addGroupImages($eID,$type, $images, $title, $desc, $upIdArr = null)
    {
        $tempArr = array();
        if (!empty($images)) {
            $tempArr =  $images;
        }
        if (empty($tempArr) && !empty($title)) {
            $tempArr =  $title;
        }
        if (empty($tempArr) && !empty($desc)) {
            $tempArr =  $desc;
        }
        if (empty($tempArr)) {
            return ;
        }
        foreach ($tempArr as $pKID => $arr) {
            if (!isset($upIdArr[$pKID])) $upIdArr[$pKID] = null;
            if (!isset($title[$pKID])) $title[$pKID] = null;
            if (!isset($desc[$pKID])) $desc[$pKID] = null;
            if (!isset($images[$pKID])) $images[$pKID] = null;
            self::addImagesAndDesc($eID,$type,$images[$pKID], $title[$pKID], $desc[$pKID], $upIdArr[$pKID],$pKID);
        }

    }

    /*
     * 通过TargetId 获取图片
     * @param $targetId int|array
     * @param $type int|array
     * @param $eId int 评测报告ID
     * */
    public static function getImagesByTargetId($targetId, $type,$eId)
    {

        if (!is_array($targetId)) {
            $targetId = array($targetId);
        }
        if (!is_array($type)) {
            $type = array($type);
        }
        $aParam = array(
            'where' => array(
                'iTargetID in' => implode(',', $targetId),
                'iTypeID in' => implode(',', $type),
                'iEvaluationID'=> $eId,
                'iStatus' => 1
            ),
            'order' => 'iIndex asc,iCreateTime asc',
        );
        $result = self::getOrm()->fetchAll($aParam);
        $newArr = array();
        if ($result) {
            foreach ($result as $k => $item) {
                $newArr[$item['iTargetID']][$item['iTypeID']][] = $item;
            }
        }
        return $newArr;
    }

    /**
     * 批量添加图片数据 从接口中获取数据时调用
     */
    public static function addBatchData($data)
    {
        if (!empty($data)) {
            foreach($data as $item) {
                self::addData($item);
            }
        }
    }

    /**
     * 批量修改图片数据 从接口中获取数据时调用
     *
     */
    public static function updateBatchData($data)
    {
        if (!empty($data)) {
            foreach($data as $item) {
                $item['iAutoID'] = self::getOne(array(
                    'where' => array(
                        'iCricId'=>$item['iCricId'],
                    )
                ),'iAutoID');
                if (!empty($item['iAutoID'] )) {
                    self::updData($item);
                }
            }
        }
    }

    /**
     * 返回图片地址(区分cric的图片)
     */
    public static function getImageUrl($iCricId,$image,$w=200,$h=150,$p1=0,$p2=0)
    {
        if (!empty($iCricId)) {
            return Util_Uri::getCricViewURL($image,$w,$h,$p1,$p2);
        } else {
            return Util_Uri::getDFSViewURL ($image,250,210);
        }
    }

    /**
     * 返回手机上图片地址(区分cric的图片)
     */
    public static function getMobileImageUrl($iCricId,$image,$w=200,$h=150,$p1=0,$p2=0, $p3=0)
    {
        if (!empty($iCricId)) {
            return Util_Uri::getCricViewURL($image,$w,$h,$p1,$p2, $p3);
        } else {
            return Util_Uri::getDFSViewURL ($image,600,450);
        }
    }

    // 通过ID 获取图片
    public static function getImagesByID($Ids)
    {
        if (!is_array($Ids)) {
            $Ids = array($Ids);
        }
        return self::getAll(array(
            'where' => array(
                'iAutoID in ' =>  implode(',',$Ids),
                'iStatus' => 1
            ),
             'order' => 'iIndex asc',
        ));
    }

    /*
     * 通过TargetId 获取图片
     * @param $targetId int|array
     * @param $type int|array
     * @param $eId int 评测报告ID
     * */
    public static function getImagesArrByTargetId($targetId, $type,$eId)
    {

        if (!is_array($targetId)) {
            $targetId = array($targetId);
        }
        if (!is_array($type)) {
            $type = array($type);
        }
        $aParam = array(
            'where' => array(
                'iTargetID in' => implode(',', $targetId),
                'iTypeID in' => implode(',', $type),
                'iEvaluationID'=> $eId,
                'iStatus' => 1
            ),
            'order' => 'iIndex asc,iCreateTime asc',
        );
        $result = self::getOrm()->fetchAll($aParam);
        $newArr = array();
        if ($result) {
            foreach ($result as $k => $item) {
                $newArr[$item['iTargetID']][] = $item;
            }
        }
        return $newArr;
    }
}