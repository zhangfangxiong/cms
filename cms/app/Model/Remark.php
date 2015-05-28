<?php
/**
 * 网友点评管理类
 * author: chasel
 */
class Model_Remark extends Model_Base
{

    const DB_NAME = 'cms';

    const TABLE_NAME = 't_remark';

    public static function getListByBID($buildingID, $iPage, $iRows)
    {
        $where = array(
            'iBuildingID' => $buildingID,
            'iStatus' => 1
        );

        $remarks = self::getList($where, $iPage, 'iAutoID DESC', $iRows, '', '', false);

        if(!empty($remarks['aList'])) {
            $len = sizeof($remarks['aList']);
            for($i = 0; $i < $len; $i ++) {
                if(!empty($remarks['aList'][$i]['iUserId'])) {
                   $muser = new Model_CricUser();
                    $user = $muser->getUserById($remarks['aList'][$i]['iUserId']);
                    if(!empty($user)) {
                        if(empty($user['UserName'])) {
                            $userName = '游客';
                        } else {
                            if (Util_Validate::isMobile($user['UserName'])) {
                                $userName = substr($user['UserName'],0, 3).'****'.substr($user['UserName'], -4, 4);
                            } else {
                                $userName = $user['UserName'];
                            }
                        }
                        $remarks['aList'][$i]['sName'] = $userName;
                        $remarks['aList'][$i]['sAvatorImgUrl'] = empty($user['HeadImg']) ? '' : $user['HeadImg'];
                    } else {
                        $remarks['aList'][$i]['sName'] = '游客';
                        $remarks['aList'][$i]['sAvatorImgUrl'] = '';
                    }
                }else {
                    $remarks['aList'][$i]['sName'] = '游客';
                    $remarks['aList'][$i]['sAvatorImgUrl'] = '';
                }
            }
        }
        return $remarks;
    }

}