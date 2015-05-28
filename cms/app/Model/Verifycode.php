<?php
/**
 * 评测报告业务类
 * author: cjj
 */
class Model_Verifycode extends Model_Base
{

    const DB_NAME = 'cms';

    const TABLE_NAME = 't_verifycode';

    /*
     * 获取最近一条验证码详情
     * @param array $aData
     * @return bool
     */
    public static function getCode($mobile, $type=1)
    {
        $options = array(
            'where' => array(
                'sPhone' => $mobile,
                'iType' => $type
            ),
            'order' => '`iCreateTime` DESC',
            'limit' => '0,1'
        );

        return self::getRow($options);
    }

    /*
     * 检查code是否正确
     * @param array $aData
     * @return bool
     */
    public static function checkCode($mobile, $code, $type)
    {
        $currentTime = time();
        $life = Yaf_G::getConf('code_life',null,'mapi');
        $life *= 60;

        $codeDetail = self::getCode($mobile, $type);

        $deadTime = $codeDetail['iCreateTime'] + $life; //验证码过期时间

        if( !empty($codeDetail) && $code == $codeDetail['sCode'] && $currentTime < $deadTime ) {
            return true;
        }

        return false;

    }



}