<?php

/**
 * 400电话管理
 * @author: cjj
 */
class Controller_Phone_Index extends Controller_Base
{

    const DB_NAME = 'hotline';

    const TABLE_NAME = 't_tellog';

    /**
     *  来电记录
     */
    public function addAction()
    {
        $hotlineOrm = Util_Common::getOrm('hotline', 't_tellog', false);
        $hotlineList = $hotlineOrm->fetchAll([
            'where' => [
                'iExt IN' => ['1', '2'],
                'iStatus' => 2
            ],
            'order' => 'iAutoID DESC',
            'limit' => '10'
        ]);
        $this->assign('hotlineList', $hotlineList);
        $this->assign('citys', Model_CrmCity::geAllCitys());
    }

    /**
     * 来电记录保存
     */
    public function saveAction()
    {
        $aParam = $this->__checkData();
        if (empty($aParam)) {
            return null;
        }
        $rs = Model_CrmUser::addUser($aParam);
        if ($rs) {
            $this->sendSms($aParam['city'], $aParam['name']);
            return $this->showMsg('数据保存成功！', true);
        } else {
            return $this->showMsg('数据保存失败！', false);
        }
    }

    /**
     * 获取表单数据
     */
    private function __checkData()
    {

        $aParam['mobile'] = $this->getParam('phoneNum'); // 移动电话
        $aParam['phone'] = $this->getParam('Telnum'); // 国定电话
        $aParam['area_code'] = $this->getParam('Quh'); // 区号
        $aParam['name'] = $this->getParam('name');
        $aParam['sex'] = '';
        $aParam['city'] = intval($this->getParam('iCityID'));
        $aParam['level1'] = 0;
        $aParam['plan'] = 1;
        $aParam['type'] = 99;

        $aParam['budget_min'] = 0;
        $aParam['budget_max'] = 0;
        $aParam['price'] = 0;
        $aParam['square_min'] = 0;
        $aParam['square_max'] = 0;
        $aParam['origin'] = 6;

        if (empty($aParam['mobile']) && empty($aParam['phone'])) {
            return $this->showMsg('请输入来电号码！', false);
        }
        if (empty($aParam['name'])) {
            return $this->showMsg('请输入用户姓名！', false);
        }
        return $aParam;

    }

    public function sendSms($city_id, $name)
    {
        // 20XX年XX月XX日XX点
        $Msg = "%s市%s先生/小姐于%s致电400客服中心咨询。详情请登录CRM系统查看，请机构及时跟进回访。【销售中心】";
        $cityArr = Model_CrmCity::getRow(array(
            'where' => array(
                'id' => $city_id,
            )
        ));
        $cityPinyin = $cityArr['code'];
        $cityName = $cityArr['name'];
        $date = date("Y年m月d日H点");
        $Msg = vsprintf($Msg, array($cityName, $name, $date));
        $cityPhone = Yaf_G::getConf('cityPhone');
        if (isset($cityPhone[$cityPinyin])) {
            if (is_array($cityPhone[$cityPinyin])) {
                foreach ($cityPhone[$cityPinyin] as $phone) {
                    Model_SendSms::send($phone, $Msg);
                }
            } else {
                Model_SendSms::send($cityPhone[$cityPinyin], $Msg);
            }

        }
    }

    /**
     *  显示来电记录
     */
    public function showAction()
    {
        $aParam['iSTime'] = $this->getParam('iSTime'); // 开始时间
        $aParam['iETime'] = $this->getParam('iETime'); // 结束时间
        $iPage = $this->getParam('page'); // 页数
        $hotlineList = self::getPageList($aParam,$iPage);
        $aParam['iSTime'] = strtotime($this->getParam('iSTime')); // 开始时间
        $aParam['iETime'] = strtotime($this->getParam('iETime')); // 结束时间
        $this->assign('aParam', $aParam);
        $this->assign('aList', $hotlineList);
    }

    /**
     * 导出来电记录
     */
    public function explodeLogAction()
    {
        $iReadNum = 10;//每次读取的数目
        $aParam['iSTime'] = $this->getParam('iSTime'); // 开始时间
        $aParam['iETime'] = $this->getParam('iETime'); // 结束时间
        $sFileName = date('Ymd').'400Log.csv';
        $sSQL = 'SELECT * FROM ' . self::TABLE_NAME . ' WHERE 1';
        $sCntSQL = 'SELECT COUNT(*) as total FROM ' . self::TABLE_NAME . ' WHERE 1';
        //时间换算
        if (!empty($aParam['iSTime'])) {
            $sSQL .= ' AND iCreateTime >=' . strtotime($aParam['iSTime']);
            $sCntSQL .= ' AND iCreateTime >=' . strtotime($aParam['iSTime']);
        }

        if (!empty($aParam['iETime'])) {
            $sSQL .= ' AND iCreateTime <=' . strtotime($aParam['iETime']);
            $sCntSQL .= ' AND iCreateTime <=' . strtotime($aParam['iETime']);
        }
        $hotlineOrm = Util_Common::getOrm(self::DB_NAME,self::TABLE_NAME, false);
        $ret = $hotlineOrm->query($sCntSQL);
        $iTotal = $ret[0]['total'];//总数
        $iTotal = min(1000,$iTotal);//最多读取1000条
        $iReadTimes = ceil($iTotal/$iReadNum);//需要读取的次数
        Util_phpCsv::export_csv($sFileName);
        echo iconv("utf8","gb2312","分机号,被叫号码,主叫号码,通话开始时间,通话时长,状态\r\n");
        for($i=0;$i<=$iReadTimes;$i++) {
            $sLimit = $i*$iReadNum;
            $sSQLLimit = $sSQL.' Limit '.$sLimit.','.$iReadNum;
            $hotlineOrm = Util_Common::getOrm(self::DB_NAME,self::TABLE_NAME, false);
            $hotlineList['aList'] = $hotlineOrm->query($sSQLLimit);
            if (!empty($hotlineList['aList'])) {
                foreach($hotlineList['aList'] as $value) {
                    echo iconv("utf8","gb2312",$value['iExt'].','.$value['sTel'].','.$value['sCaller'].','.$value['sCallTime'].','.$value['iCallLen'].','.$value['iStatus']."\r\n");
                }
            }
        }
        return false;
    }

    private static function getPageList(
        $aParam,
        $iPage,
        $sOrder = 'iCreateTime DESC',
        $iPageSize = 20,
        $sUrl = '',
        $aArg = array()
    )
    {

        $iPage = max($iPage, 1);
        $sSQL = 'SELECT * FROM ' . self::TABLE_NAME . ' WHERE 1';
        $sCntSQL = 'SELECT COUNT(*) as total FROM ' . self::TABLE_NAME . ' WHERE 1';

        //时间换算
        if (!empty($aParam['iSTime'])) {
            $sSQL .= ' AND iCreateTime >=' . strtotime($aParam['iSTime']);
            $sCntSQL .= ' AND iCreateTime >=' . strtotime($aParam['iSTime']);
        }

        if (!empty($aParam['iETime'])) {
            $sSQL .= ' AND iCreateTime <=' . strtotime($aParam['iETime']);
            $sCntSQL .= ' AND iCreateTime <=' . strtotime($aParam['iETime']);
        }

        $sSQL .= ' Order by ' . $sOrder;
        $sSQL .= ' Limit ' . ($iPage - 1) * $iPageSize . ',' . $iPageSize;
        $hotlineOrm = Util_Common::getOrm(self::DB_NAME,self::TABLE_NAME, false);
        $aRet['aList'] = $hotlineOrm->query($sSQL);
        if ($iPage == 1 && count($aRet['aList']) < $iPageSize) {
            $aRet['iTotal'] = count($aRet['aList']);
            $aRet['aPager'] = null;
        } else {
            unset($aParam['limit'], $aParam['order']);
            $ret = $hotlineOrm->query($sCntSQL);
            $aRet['iTotal'] = $ret[0]['total'];
            $aRet['aPager'] = Util_Page::getPage($aRet['iTotal'], $iPage, $iPageSize,'',self::_getNewsPageParam($aParam));
        }
        //获取文章相关城市
        return $aRet;
    }

    private static function _getNewsPageParam(&$aParam)
    {
        $pageParam = array(
            'iSTime' => isset($aParam['iSTime']) ? $aParam['iSTime'] : '',
            'iETime' => isset($aParam['iETime']) ? $aParam['iETime'] : '',
        );
        return $pageParam;

    }

    public function actionAfter()
    {
        parent::actionAfter();
        $this->_assignUrl();
    }

    protected function _assignUrl()
    {
        $this->assign('sShowUrl', '/phone/show');
        $this->assign('sExplodeLogUrl', '/phone/explodeLog');
    }


}