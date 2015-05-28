<?php

/**
 * 户型基本信息控制器 (房子朝向,面积,楼梯数等数据)
 * author: cjj
 * Date: 2015/1/26
 */
class Controller_Evaluation_Hxtype extends Controller_Base
{

    private $config = null;


    public function actionBefore()
    {
        parent::actionBefore();
        $this->config = Model_EvaluationHxType::getConfig();
    }

    /**
     * 添加户型数据
     */
    public function addAction()
    {
        try {
            $aParam = $this->_checkData();
            $aInfo = $this->checkHxInfoData(); // 户型基本信息
            if (empty($aParam) || empty($aInfo)) return null;
            $arr = Model_EvaluationHxInfo::getHxInfo($aInfo);
            if (!empty($arr)) {
                $aParam['iHxInfoID'] = $arr['iHxInfoID'];
            } else {
                $aParam['iHxInfoID'] = Model_EvaluationHxInfo::addData($aInfo);
                if (empty($aParam['iHxInfoID'])) {
                    return $this->showMsg('户型信息保存失败！', false);
                }
            }
            $insertId = Model_EvaluationHxType::addHxType($aParam);
            if ($insertId > 0) {
                $infoList = Model_EvaluationHxType::getHxTypeList($aInfo['iEvaluationID']);
                return $this->showMsg($this->getView()->render(Model_EvaluationHxType::TP_PATH, array(
                    'list' => $infoList,
                    'evaluationSwitch' => $this->config['evaluationSwitch']
                )), true);

            } else {
                return $this->showMsg('户型信息保存失败！', false);
            }
        } catch (Exception $e) {
            return $this->showMsg($e->getMessage(), false);
        }

    }


    /**
     * 修改户型数据
     */
    public function editAction()
    {
        $aParam = $this->_checkData();
        if (empty($aParam)) {
            return null;
        }
        if (Model_EvaluationHxType::upHxType($aParam)) {
            $infoList = Model_EvaluationHxType::getHxTypeList($this->getParam('iEvaluationID'));
            return $this->showMsg($this->getView()->render(Model_EvaluationHxType::TP_PATH, array(
                'list' => $infoList,
                'evaluationSwitch' => $this->config['evaluationSwitch']
            )), true);
        } else {
            return $this->showMsg('户型信息修改失败！', false);
        }
    }

    /**
     * 获取户型数据
     */
    public function getHxAction()
    {
        $hxID = intval($this->getParam('hxID'));
        $data = Model_EvaluationHxType::getDetail($hxID); // 户型数据 房子面积朝向等等
        if (!empty($data)) {
            $result = Model_EvaluationHxInfo::getDetail($data['iHxInfoID']); // 户型基本信息 几室几厅
            if (!empty($result)) {
                $data['iRoom'] = $this->config['hx']['iRoom'][$result['iRoomNum']];
                $data['iHall'] = $this->config['hx']['iHall'][$result['iHallNum']];
                $data['iToilet'] = $this->config['hx']['iToilet'][$result['iToiletNum']];
            }
        }
        return $this->showMsg($data, true);
    }

    /**
     * 删除户型数据
     */
    public function delAction()
    {
        $hxID = $this->getParam('hxID');
        Model_EvaluationHxType::delData(intval($hxID));
        return $this->showMsg('', true);
    }

    /**
     * 请求数据检测
     *
     * @return mixed
     */
    private function _checkData()
    {

        $aParam = array();
        $aParam['iAutoID'] = intval($this->getParam('iAuID'));
        $aParam['sHuXingID'] = $this->getParam('sHuXingID');
        $aParam['iHxInfoID'] = intval($this->getParam('iHxInfoID'));
        $aParam['iAreaNum'] = floatval($this->getParam('iAreaNum'));
        $aParam['iMaxAreaNum'] = floatval($this->getParam('iMaxAreaNum'));
        $aParam['iStairsNum'] = intval($this->getParam('iStairsNum'));
        $aParam['iDoorNum'] = intval($this->getParam('iDoorNum'));
        $aParam['iFloorNum'] = $this->getParam('iFloorNum');
        $aParam['iHallWidth'] = floatval($this->getParam('iHallWidth'));
        $aParam['iMasterRoomWidth'] = floatval($this->getParam('iMasterRoomWidth'));
        $aParam['iMasterRoomToward'] = intval($this->getParam('iMasterRoomToward'));
        $aParam['iSecondRoomToward'] = intval($this->getParam('iSecondRoomToward'));
        $aParam['iIsNorthSouth'] = intval($this->getParam('iIsNorthSouth'));
        $aParam['iAreaRate'] = floatval($this->getParam('iAreaRate'));
        $aParam['iObviousToilet'] = intval($this->getParam('iObviousToilet'));
        $aParam['iHideToilet'] = intval($this->getParam('iHideToilet'));
        if (empty($aParam['sHuXingID'])) {
            return $this->showMsg('请输入户型编号！', false);
        }
        if (empty($aParam['iAreaNum'])) {
            return $this->showMsg('请输入面积！', false);
        }
        if (empty($aParam['iStairsNum'])) {
            return $this->showMsg('请输入梯数！', false);
        }
        if (empty($aParam['iDoorNum'])) {
            return $this->showMsg('请输入户数！', false);
        }
        if (empty($aParam['iMasterRoomToward'])) {
            return $this->showMsg('请选择主卧朝向！', false);
        }
        if (empty($aParam['iSecondRoomToward'])) {
            return $this->showMsg('请选择次主卧朝向！', false);
        }
        if (empty($aParam['iIsNorthSouth'])) {
            return $this->showMsg('请选择南北通透！', false);
        }
        return $aParam;
    }

    private function checkHxInfoData()
    {
        $aParam['iEvaluationID'] = intval($this->getParam('iEvaluationID'));
        $aParam['iRoomNum'] = intval($this->getParam('iRoomNum'));
        $aParam['iHallNum'] = intval($this->getParam('iHallNum'));
        $aParam['iToiletNum'] = intval($this->getParam('iToiletNum'));
        if (empty($aParam['iEvaluationID'])) {
            return $this->showMsg('请选择评测报告！', false);
        }
        if (empty($aParam['iRoomNum'])) {
            return $this->showMsg('请选择房间数！', false);
        }
        if (empty($aParam['iHallNum'])) {
            $aParam['iHallNum'] = 0;
        }
        if (empty($aParam['iToiletNum'])) {
            $aParam['iToiletNum'] = 0;
        }
        return $aParam;
    }

}