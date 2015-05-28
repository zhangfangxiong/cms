<?php

/**
 * Created by PhpStorm.
 * User: cjj
 * Date: 15/4/7
 * Time: 下午1:44
 */
class Model_RoomType
{


    public static function getRecommendedRoomTypes($unitID) {

        $result = Sdk_Cms_Newhouse::getRecommendedRoomTypes($unitID);
        if ($result['status'] == 1) {
            return $result['data'];
        }
        return null;
    }

    // 房间信息分组
    public static function groupRoomType($data) {
        if (empty($data)) {
            return array();
        }
        $group = array();
        foreach($data as $item) {
            $group[$item['TypeName']][] = $item;
        }
        return $group;
    }

    // 房间信息数据重构
    public static function formatRoomType(&$data){
        if (empty($data)) {
            return array();
        }
        $groupData = self::groupRoomType($data);
        $newArr = array();
        $i = 0;
        foreach ($groupData as $k => $item) {
            $newArr[$i]['fx'] = $k;
            $hx = array();
            $min = array();
            $max = array();
            foreach($item as $kc => $vc) {
                $hx[] = $vc['TypeCode'];
                $min[] = $vc['MinArea'];
                $max[] = $vc['MaxArea'];
            }
            $newArr[$i]['hx'] = implode(',',$hx);
            $newArr[$i]['ar'] = sprintf("%s-%s㎡",min($min),max($max));
            $i++;
        }
        return $newArr;
    }

    //roomTyep数据重构(用于价目表功能)
    public static function formatRoomTyepData($data)
    {
        if (empty($data)) {
            return array();
        }
        foreach($data as $key=>$value){
            //判断房型是否一样
            if($value['TypeName'] == $value['TypeName']){
                $newArr[$value['TypeName']][] = $value;
            }
        }
        return $newArr;
    }
}