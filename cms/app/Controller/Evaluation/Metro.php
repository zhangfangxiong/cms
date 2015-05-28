<?php
/**
 * Created by PhpStorm.
 * User: xiaoyao
 * Date: 2015/1/22
 * Time: 11:30
 */

class Controller_Evaluation_Metro extends Controller_Base
{


    /**
     * 轨交线路列表
     */
    public function indexAction()
    {
        $aParams = ['where' => ['iCityID' => $this->iCurrCityID], 'order' => 'iMetroID DESC'];
        $aMetroList = Model_Metro::getAll($aParams);
        /**
        foreach ($aMetroList as $key => $aMetro) {
            $aStationList = Model_Metro::getMetroStation($aMetro['iMetroID']);
            $aMetroList[$key]['aStationList'] = $aStationList;
        }*/
        $this->assign('aMetroList', $aMetroList);
    }

    /**
     * 轨交线路禁用
     */
    public function unableAction()
    {
        $iMetroID = intval($this->getParam('id'));
        $bRet = false;
        $aMetroInfo = Model_Metro::getDetail($iMetroID);
        if ($iMetroID > 0) {
            $bRet = Model_Metro::unableMetro($iMetroID, $this->aCurrUser);
        }
        if ($bRet) {
            return $this->showMsg('操作成功，【' . $aMetroInfo['sMetroName'] . '】在部分楼盘评测页中不显示', true);
        } else {
            return $this->showMsg('禁用该地铁线路失败', false);
        }
    }

    /**
     * 轨交线路启用
     */
    public function enableAction()
    {
        $iMetroID = intval($this->getParam('id'));
        $bRet = false;
        $aMetroInfo = Model_Metro::getDetail($iMetroID);
        if ($iMetroID > 0) {
            $bRet = Model_Metro::enableMetro($iMetroID, $this->aCurrUser);
        }
        if ($bRet) {
            return $this->showMsg('操作成功，【' . $aMetroInfo['sMetroName'] . '】在部分楼盘评测页中显示', true);
        } else {
            return $this->showMsg('启用该地铁线路失败', false);
        }
    }


    /**
     * 轨交站点管理
     */
    public function stationAction()
    {
        $iMetroID = intval($this->getParam('id'));
        $aMetro = Model_Metro::getDetail($iMetroID);
        $aStationList = Model_Metro::getMetroStation($iMetroID);
        $aDirection = Model_Metro::metroDirection($iMetroID);
        if ($aStationList) {
            foreach ($aStationList as $key => $aStation) {
                $aStationRunTime = [];
                if ($aDirection) {
                    foreach ($aDirection as $direction) {
                        $row = Model_Metro::stationDirectionRuntime($aStation['iAutoID'], $direction['iAutoID']);
                        if($row) {
                            $row['sDirection'] = $direction['sDirection'];
                            $aStationRunTime[$direction['iAutoID']] = $row;
                        }

                    }
                }
                $aStationList[$key]['aRuntime'] = $aStationRunTime;
            }
        }

        $this->assign('aStationList', $aStationList);
        $this->assign('aDirection', $aDirection);
        $this->assign('aMetro', $aMetro);
    }

    /**
     * 轨交站点添加
     */
    public function stationaddAction()
    {
        $aParam = $this->getParams();
        $iMetroID = intval($aParam['iMetroID']);

        if ($iMetroID > 0) {
            $aDirection = Model_Metro::metroDirection($iMetroID);
            $sStation = trim($aParam['sStation']);
            if ($sStation) {
                $iStationID = Model_MetroStation::addData(
                    ['iMetroID' => $iMetroID, 'sStation' => $sStation, 'iStatus' => 1, 'iOpen' => 1, 'sGeo' => '']
                );
                if ($aDirection) {
                    foreach ($aDirection as $direction) {
                        if (isset($aParam['sFRunTime'.$direction['iAutoID']])) {
                            $sFRunTime = trim($aParam['sFRunTime'.$direction['iAutoID']]);
                        } else {
                            $sFRunTime = '-';
                        }

                        if (isset($aParam['sLRunTime'.$direction['iAutoID']])) {
                            $sLRunTime = trim($aParam['sLRunTime'.$direction['iAutoID']]);
                        } else {
                            $sLRunTime = '-';
                        }

                        $adddata['iStationID'] = $iStationID;
                        $adddata['iDirectionID'] = $direction['iAutoID'];
                        $adddata['sFRunTime'] = Util_Tools::transSBCDBC($sFRunTime);
                        $adddata['sLRunTime'] = Util_Tools::transSBCDBC($sLRunTime);
                        Model_MetroStationRuntime::addData($adddata);

                    }
                }
                return $this->showMsg('站点添加成功', true);
            }

        }
        return $this->showMsg('参数错误，站点添加失败', false);
    }

    /**
     * 轨交站点编辑
     */
    public function stationeditAction()
    {
        $aParam = $this->getParams();

        $iMetroID = intval($aParam['iMetroID']);
        $iStationID = intval($aParam['iStationID']);
        if ($iMetroID > 0 && $iStationID > 0) {
            $aDirection = Model_Metro::metroDirection($iMetroID);
            $sStation = trim($aParam['sStation']);
            if ($sStation) {
                Model_MetroStation::updData(
                    ['iAutoID' => $iStationID, 'sStation' => $sStation]
                );
                if ($aDirection) {
                    foreach ($aDirection as $direction) {
                        if (isset($aParam['sFRunTime'.$direction['iAutoID']])) {
                            $sFRunTime = trim($aParam['sFRunTime'.$direction['iAutoID']]);
                        } else {
                            $sFRunTime = '-';
                        }

                        if (isset($aParam['sLRunTime'.$direction['iAutoID']])) {
                            $sLRunTime = trim($aParam['sLRunTime'.$direction['iAutoID']]);
                        } else {
                            $sLRunTime = '-';
                        }

                        $row = Model_MetroStationRuntime::getRow(
                            [
                                'where' => [
                                    'iStationID' => $iStationID,
                                    'iDirectionID' => $direction['iAutoID'],
                                    'iStatus' => 1
                                ]
                            ]
                        );

                        if ($row) {
                            $upddata['iAutoID'] = $row['iAutoID'];
                            $upddata['sFRunTime'] = Util_Tools::transSBCDBC($sFRunTime);
                            $upddata['sLRunTime'] = Util_Tools::transSBCDBC($sLRunTime);
                            Model_MetroStationRuntime::updData($upddata);
                        } else {
                            $adddata['iStationID'] = $iStationID;
                            $adddata['iDirectionID'] = $direction['iAutoID'];
                            $adddata['sFRunTime'] = Util_Tools::transSBCDBC($sFRunTime);
                            $adddata['sLRunTime'] = Util_Tools::transSBCDBC($sLRunTime);
                            Model_MetroStationRuntime::addData($adddata);
                        }
                    }
                }
                return $this->showMsg('站点修改成功', true);
            }

        }
        return $this->showMsg('参数错误，站点修改失败', false);
    }

    /**
     * 轨交站点删除
     */
    public function stationdelAction()
    {
        $iStationID = intval($this->getParam('iStationID'));
        if ($iStationID > 0) {
            Model_MetroStation::delData($iStationID);
            return $this->showMsg('站点删除成功', true);
        }
        return $this->showMsg('参数错误，站点删除失败', false);
    }

    /**
     * 轨交站点排序
     */
    public function stationsortAction()
    {
        $aStationIDs = $this->getParam('iStationID');
        if (!empty($aStationIDs)) {
            foreach ($aStationIDs as $key => $stationID) {
                Model_MetroStation::updData(['iAutoID' => $stationID, 'iOrder' => $key]);
            }
        }
        return $this->showMsg('站点排序保存成功', true);
    }

    /**
     * 添加轨交线路
     */
    public function addAction()
    {
        $sMetroName = trim($this->getParam('sMetroName'));
        if ($sMetroName) {
            $insertData = [];
            $insertData['sMetroName'] = $sMetroName;
            $insertData['iOpen'] = 1;
            $insertData['iCityID'] = $this->iCurrCityID;
            $insertData['iStatus'] = 1;
            $insertData['sOperationName'] = $this->aCurrUser['sUserName'];
            $insertData['iOperationID'] = $this->aCurrUser['iUserID'];
            Model_Metro::addData($insertData);
            return $this->showMsg('线路添加成功', true);
        }

        return $this->showMsg('请输入线路名称', false);
    }

    /**
     * 编辑轨交线路
     */
    public function editAction()
    {
        $sMetroName = trim($this->getParam('sMetroName'));
        if (empty($sMetroName)) {
            return $this->showMsg('请输入线路名称', false);
        }

        $iMetroID = intval($this->getParam('iMetroID'));
        if ($iMetroID > 0) {
            $updData = [];
            $updData['sMetroName'] = $sMetroName;
            $updData['iMetroID'] = $iMetroID;
            $insertData['sOperationName'] = $this->aCurrUser['sUserName'];
            $insertData['iOperationID'] = $this->aCurrUser['iUserID'];
            Model_Metro::updData($updData);
            return $this->showMsg('线路编辑成功', true);
        }

        return $this->showMsg('参数错误，请刷新页面重试', false);

    }

    /**
     * 轨交线路方向管理
     */
    public function directionAction()
    {
        $iMetroID = intval($this->getParam('id'));
        if ($iMetroID > 0) {
            $aDirection = Model_Metro::metroDirection($iMetroID);
            $aMetro = Model_Metro::getDetail($iMetroID);
            $this->assign('aDirection', $aDirection);
            $this->assign('aMetro', $aMetro);
        } else {
            return $this->redirect('/evaluation/metro/');
        }
    }

    /**
     * 添加线路方向
     */
    public function directionaddAction()
    {
        $aParam = $this->getParams();
        $iMetroID = intval($aParam['iMetroID']);

        if ($iMetroID > 0) {
            $sDirection = trim($aParam['sDirection']);
            if (empty($sDirection)) {
                return $this->showMsg('请输入线路方向', false);
            }
            if ($sDirection) {
                Model_MetroDirection::addData(
                    ['iMetroID' => $iMetroID, 'sDirection' => $sDirection, 'iStatus' => 1]
                );
                return $this->showMsg('线路方向添加成功', true);
            }

        }
        return $this->showMsg('参数错误，方向添加失败', false);
    }

    /**
     * 编辑线路方向
     */
    public function directioneditAction()
    {
        $aParam = $this->getParams();
        $iDirectionID = intval($aParam['iDirectionID']);

        $sDirection = trim($this->getParam('sDirection'));
        if (empty($sDirection)) {
            return $this->showMsg('请输入线路名称', false);
        }

        if ($sDirection > 0) {
            $updData = [];
            $updData['sDirection'] = $sDirection;
            $updData['iAutoID'] = $iDirectionID;
            Model_Metro::updData($updData);
            return $this->showMsg('线路防线编辑成功', true);
        }

        return $this->showMsg('参数错误，请刷新页面重试', false);

    }

    /**
     * 删除线路方向
     */
    public function directiondelAction()
    {
        $iDirectionID = intval($this->getParam('id'));
        if ($iDirectionID > 0) {
            Model_MetroDirection::delData($iDirectionID);
            $aList = Model_MetroStationRuntime::getPair(
                [
                    'where' => [
                        'iDirectionID' => $iDirectionID
                    ]
                ],
                'iAutoID', 'iDirectionID'
            );
            if ($aList) {
                foreach ($aList as $key => $value) {
                    Model_MetroStationRuntime::delData($key);
                }
            }
        }
        return $this->showMsg('删除线路方向成功', true);
    }

}