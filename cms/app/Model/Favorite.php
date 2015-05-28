<?php
/**
 * 用户收藏管理类
 * author: chasel
 */
class Model_Favorite extends Model_Base
{

    const DB_NAME = 'cms';

    const TABLE_NAME = 't_favorite';

    /*
         * 删除收藏内容
         * return array
         */
    public static function delFavorite($contentID, $userID, $type)
    {
        $where = array(
            'iContentID' => $contentID,
            'iUserID' => $userID,
            'iType' => $type
        );
        $fav = self::getRow(['where'=>$where]);

        if(!empty($fav)) {
            $pid = $fav['iAutoID'];
            self::updData(['iAutoID' => $pid, 'iStatus' => 0]);
        }
    }

    /*
         * 删除某用户某以类型的所有收藏内容
         * return array
         */
    public static function delAllFavorites($userID, $type)
    {
        $where = array(
            'iUserID' => $userID,
            'iType' => $type
        );

        $favs = self::getAll(['where' => $where]);
        if(!empty($favs)) {
            foreach($favs as $f) {
                self::updData(['iAutoID' => $f['iAutoID'], 'iStatus' => 0]);
            }
        }

    }

    /*
         * 获取收藏列表
         * return array
         */
    public static function getByuID($iUserID, $type, $iPage, $iRows, $status = 1)
    {
        $where = array(
            'iUserID' => $iUserID,
            'iType' => $type,
            'iStatus' => $status
        );
        return self::getList($where, $iPage, '', $iRows, '', '', false);
    }

    /*
         * 获取收藏列表
         * return array
         */
    public static function exist($contentID, $iUserID, $type, $status = 1)
    {
        $where = array(
            'iContentID' => $contentID,
            'iUserID' => $iUserID,
            'iType' => $type,
            'iStatus' => $status
        );
        $data =  self::getRow(['where' => $where]);
        if(!empty($data)) {
            return true;
        }

        return false;
    }
}