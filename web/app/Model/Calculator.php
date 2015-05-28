<?php

/**
 * 房贷计算器
 * User: cjj
 * Date: 15/5/19
 * Time: 下午5:44
 */
class Model_Calculator
{


    public static function Fangdai($zongjia = 0, $anjie = 0, $lilv = 0, $qishu = 0, $huankuanfangshi = 0)
    {
        $_zongJia = $zongjia;
        $_daiKuan = $zongjia * $anjie / 10;
        $_shoufu = $_zongJia - $_daiKuan;

        $_yuelilv = $lilv / 100 / 12;
        $_qiShu = $qishu;

        $_huanKuan = 0;
        $_lixi = 0;
        $_yueHuanKuan = array();
        if ($huankuanfangshi == 1) {
            $_yueHuanKuan[0] = $_daiKuan * $_yuelilv * Pow((1 + $_yuelilv), $_qiShu) / (Pow((1 + $_yuelilv),
                        $_qiShu) - 1);
            for ($i = 1; $i < $_qiShu; $i++) {
                $_yueHuanKuan[$i] = $_yueHuanKuan[0];
            }
            $_huanKuan = $_yueHuanKuan[0] * $_qiShu;
        } else {
            $_huanKuan = 0;
            for ($i = 0; $i < $_qiShu; $i++) {
                //（贷款本金÷还款月数）+（本金 — 已归还本金累计额）×每月利率
                $_yueHuanKuan[$i] = $_daiKuan / $_qiShu + ($_daiKuan - $_daiKuan / $_qiShu * $i) * $_yuelilv;
                $_huanKuan += $_yueHuanKuan[$i];
            }
        }
        $_lixi = $_huanKuan - $_daiKuan;
        for ($i = 0; $i < sizeof($_yueHuanKuan); $i++) {
            $_yueHuanKuan[$i] = Round($_yueHuanKuan[$i], 2);
        }
        $result = array(
            'ZongJia' => $_zongJia,
            'DaiKuan' => $_daiKuan,
            'HuanKuan' => Round($_huanKuan, 2),
            'Lixi' => Round($_lixi, 2),
            'Shoufu' => $_shoufu,
            'QiShu' => $_qiShu,
            'YueHuanKuan' => $_yueHuanKuan,
        );
        return $result;
    }
}