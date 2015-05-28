<?php

/**
 * Created by PhpStorm.
 * User: xiaoyao
 * Date: 2015/1/26
 * Time: 9:38
 */
class Controller_Evaluation_Bus extends Controller_Base
{

    /**
     * 公交列表
     */

    public function indexAction()
    {
        $iPage = intval($this->getParam('page'));
        $aParam = ['iCityID' => $this->iCurrCityID];
        $sOrder = $this->getParam('sOrder') ? $this->getParam('sOrder') : 'iBusID DESC';
        $aParam['iType'] = $this->getParam('iType');
        $aParam['iStatus'] = $this->getParam('iStatus');
        $aParam['sBusName'] = $this->getParam('sBusName');

        $aBusList = Model_Bus::getBusList($aParam, $iPage, $sOrder);
        $aType = Yaf_G::getConf('aType', 'bus', 'evaluation');
        $aStatus = Yaf_G::getConf('status', 'bus', 'evaluation');

        $aParam['sOrder'] = $sOrder;
        $this->assign('aParam', $aParam);
        $this->assign('aType', $aType);
        $this->assign('aStatus', $aStatus);
        $this->assign('aList', $aBusList);
    }

    /**
     * 增加公交线路
     */

    public function addAction()
    {
        if ($this->isPost()) {
            $aBus = $this->_checkData();
            if (empty($aBus)) {
                return null;
            }

            if (Model_Bus::addData($aBus) > 0) {
                return $this->showMsg('公交线路增加成功！', true);
            } else {
                return $this->showMsg('公交线路增加失败！', false);
            }
        } else {
            $this->_assignConfig();
        }
    }

    /**
     * 编辑公交线路
     */
    public function editAction()
    {
        if ($this->isPost()) {
            $aBus = $this->_checkData();
            if (empty($aBus)) {
                return null;
            }
            $aBus['iBusID'] = intval($this->getParam('iBusID'));

            if (1 == Model_Bus::updData($aBus)) {
                return $this->showMsg('公交线路信息更新成功！', true);
            } else {
                return $this->showMsg('公交线路信息更新失败！', false);
            }
        } else {
            $this->_response->setHeader('Access-Control-Allow-Origin', '*');

            $iBusID = intval($this->getParam('id'));
            $aBus = Model_Bus::getDetail($iBusID);
            if (!$aBus) {
                $this->redirect('/evaluation/bus.html');
            }

            $this->assign('aBus', $aBus);
            $this->_assignConfig();
        }
    }

    /**
     * 更改公交线路状态
     */
    public function changeStatusAction()
    {
        $iBusID = intval($this->getParam('id'));
        $aBus = Model_Bus::getDetail($iBusID);

        if($aBus['iStatus'] == 1) {
            $aBus['iStatus'] = 0;
            $message = '操作成功，【'.$aBus['sBusName']. '】在部分楼盘评测页中不显示';
        }else {
            $aBus['iStatus'] = 1;
            $message = '操作成功，【'.$aBus['sBusName'].'】在部分楼盘评测页中显示';
        }
        if (1 == Model_Bus::updData($aBus)) {
            return $this->showMsg($message, true);
        } else {
            return $this->showMsg('操作失败！', false);
        }

    }

    private function _assignConfig()
    {
        $aType = Yaf_G::getConf('aType', 'bus', 'evaluation');
        $this->assign('aType', $aType);
    }

    /**
     * 检测参数
     * @return array|void
     */
    private function _checkData()
    {
        $sBusName = $this->getParam('sBusName');
        $sFRunTime = $this->getParam('sFRunTime');
        $sLRunTime = $this->getParam('sLRunTime');
        $iStatus = $this->getParam('iStatus');
        $iType = intval($this->getParam('iType'));

        $iCreateTime = $this->getParam('iCreateTime');
        $iCreateTime = empty($iCreateTime) ? time(): $iCreateTime;
        $iUpdateTime = time();
        $sOperator = $this->aCurrUser['sUserName'];
        $iCityID = $this->getParam('iCityID');
        $iCityID = empty($iCityID) ? $this->iCurrCityID: $iCityID;

        if (empty($sBusName)) {
            return $this->showMsg('请填写线路名称', false);
        }
        if (empty($sFRunTime)) {
            return $this->showMsg('请填写首班车时间', false);
        }
        if (empty($sLRunTime)) {
            return $this->showMsg('请填写末班车时间', false);
        }

        $aRow = array(
            'sBusName' => $sBusName,
            'sFRunTime' =>  Util_Tools::transSBCDBC($sFRunTime),
            'sLRunTime' =>  Util_Tools::transSBCDBC($sLRunTime),
            'iStatus' => $iStatus,
            'iCreateTime' => $iCreateTime,
            'sOperator' => $sOperator,
            'iType' => $iType,
            'iCityID' => $iCityID
        );

        return $aRow;

    }

    /**
     * 编辑公交站点
     */
    public function stationsAction()
    {
        $iBusID = intval($this->getParam('id'));
        $aBus = Model_Bus::getDetail($iBusID);
        $option = array();
        $option['where'] = array('iBusID' => $iBusID, 'iStatus' => 1);
        $option['order'] = '`iOrder` ASC';
        $stations = Model_BusStation::getAll($option);
        $this->assign('aStations', $stations);
        $this->assign('aBusName', $aBus['sBusName']);
        $this->assign('iBusID', $iBusID);
    }

    /**
     * 增加公交站点
     */
    public function addStationAction()
    {
        if ($this->isPost()) {
            $aStation = $this->_saveStation();
            if (empty($aStation)) {
                return null;
            }
            $opType = intval($this->getParam('opType'));
            $iAutoID = 0;
            if($opType) {//$opType, 1新增，0编辑
                $aStation['iCreateTime'] = time();
                $iAutoID  = Model_BusStation::addData($aStation);
            }else {
                $aStation['iAutoID'] = intval($this->getParam('iAutoID'));
                $iAutoID  = Model_BusStation::updData($aStation);
            }


            if ($iAutoID > 0) {
                if(!$opType) {
                    $iAutoID = $aStation['iAutoID'];
                }
                $data = array('msg'=>'公交站点增加/编辑成功！', 'iAutoID'=>$iAutoID, 'sStation'=>$aStation['sStation']);
                return $this->showMsg($data, true);
            } else {
                return $this->showMsg('公交站点增加/编辑失败！', false);
            }
        }
    }


    /**
     * 保存公交站点顺序
     */
    public function saveStationsAction()
    {
        if ($this->isPost()) {
            $ids = $this->getParam('ids');
            $orders = $this->getParam('orders');
            $ids = explode( ',', $ids);
            $orders = explode( ',', $orders);
            $len = sizeof($ids);

            $error = true;
            $orm = Model_BusStation::getOrm();
            $orm->begin();
            for($i = 0; $i < $len; $i ++) {
                $station = array('iAutoID'=> $ids[$i], 'iOrder'=>$orders[$i]);
                $error = Model_BusStation::updData($station);
                if(!$error) {
                    break;
                    $orm->rollback();
                    return $this->showMsg('公交站点顺序保存失败！', false);
                }
            }

            $orm->commit();
            return $this->showMsg('公交站点顺序保存成功！', true);
        }
    }

    /**
     * 删除公交站点
     */
    public function deleteStationAction()
    {
        if ($this->isPost()) {
            $id = intval($this->getParam('id'));
            $station = array('iAutoID'=> $id, 'iStatus'=>0);
            $error = Model_BusStation::updData($station);

            if($error) {
                return $this->showMsg('公交站点删除成功！', true);
            } else {
                return $this->showMsg('公交站点删除失败！', false);
            }
        }
    }

    private function _saveStation()
    {
        $sStation = $this->getParam('sStation');
        $iBusID = intval($this->getParam('iBusID'));

        if (empty($sStation)) {
            return $this->showMsg('请填写站点名称', false);
        }

        $aRow = array(
            'iBusID' => $iBusID,
            'sStation' => $sStation,
            'iStatus' => 1,
            'iUpdateTime' => time()
        );

        return $aRow;

    }

}
