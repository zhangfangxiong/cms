<?php
/**
 * Created by PhpStorm.
 * User: xcy
 * Date: 2015/5/25
 * Time: 15:18
 * 楼盘接口
 */


class Controller_Api_Comment extends Controller_Api_Base {

	/**
     * 评论列表
     */
    public function getCommentAction(){
        $iLoupanID = $this->getParam('loupanId');
        if(empty($iLoupanID)){
            return null;
        }

        $aWhere = [
        	'iLoupanID' => $iLoupanID,
        	'iStatus' => 1,
        	'iIsCheck !=' => 2,
        ];

        $result = Model_Comment::getCommentList($aWhere);
        $this->showMsg($result, true);
    }
}