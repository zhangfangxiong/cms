<?php

/**
 * 房贷计算
 * Date: 2015/2/2
 * author:cjj
 * Time: 13:27
 */
class Controller_Calculator_Index extends Controller_Base
{

    public function indexAction()
    {
        $zongjia = $this->getParam('zongjia');
        $anjie = $this->getParam('anjie');
        $lilv = $this->getParam('lilv');
        $qishu = $this->getParam('qishu');
        $huankuanfangshi =$this->getParam('huankuanfangshi');
        $result = Model_Calculator::Fangdai($zongjia, $anjie , $lilv , $qishu , $huankuanfangshi);
        return $this->showMsg($result, true);
    }
}