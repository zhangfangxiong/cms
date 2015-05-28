<?php

/**
 * Created by PhpStorm.
 * User: xiaoyao
 * Date: 2015/1/20
 * Time: 13:25
 */
class Controller_Evaluation_Medical extends Controller_Base
{

    /**
     * 医院列表
     */
    public function indexAction()
    {
        $aParam = array();
        $aParam['sName'] = $this->getParam('sName');
        $aParam['iLevel'] = $this->getParam('iLevel');
        $aParam['iRegionID'] = $this->getParam('iRegionID');
        $aParam['sOrder'] = $this->getParam('sOrder') ? $this->getParam('sOrder') : 'iHospitalID DESC';
        $this->assign('aParam', $aParam);

        $iPage = intval($this->getParam('page'));
        $aWhere = array(
            'iStatus' => 1,
            'iCityID' => $this->iCurrCityID
        );

        if (isset($aParam['iRegionID']) && !empty($aParam['iRegionID']) && $aParam['iRegionID'] != -1) {
            $aWhere['iRegionID'] = intval($aParam['iRegionID']);
        }

        if (isset($aParam['iLevel']) && !empty($aParam['iLevel']) && $aParam['iLevel'] != -1) {
            $aWhere['iLevel'] = intval($aParam['iLevel']);
        }

        if (!empty($aParam['sName'])) {
            $aWhere['sName LIKE'] = '%' . $aParam['sName'] . '%';
        }

        $sOrder = '';
        if (!empty($aParam['sOrder'])) {
            $sOrder = $aParam['sOrder'];
        }

        $aCity = Model_City::getDetail($this->iCurrCityID);
        $aRegion = $this->_getCityRegion($aCity['sFullPinyin']);
        $aNewRegion = [];
        if ($aRegion) {
            foreach ($aRegion as $key => $value) {
                $aNewRegion[$value['ID']] = $value;
            }
        }
        $aList = Model_Hospital::getList($aWhere, $iPage, $sOrder);
        $this->_assignConfig();
        $this->assign('aList', $aList);
        $this->assign('aRegion', $aNewRegion);

    }

    /**
     * 医院添加
     */
    public function addAction()
    {
        if ($this->isPost()) {
            $aHospital = $this->_checkData();
            if (empty($aHospital)) {
                return null;
            }
            $aHospital['iCreateUserID'] = $aHospital['iUpdateUserID'] = $this->aCurrUser['iUserID'];
            if (Model_Hospital::addData($aHospital) > 0) {
                return $this->showMsg('医院增加成功！', true);
            } else {
                return $this->showMsg('医院增加失败！', false);
            }
        } else {
            $this->_assignConfig();
            $aCity = Model_City::getDetail($this->iCurrCityID);
            $aRegion = $this->_getCityRegion($aCity['sFullPinyin']);
            $this->assign('aRegion', $aRegion);
        }
    }

    /**
     * 医院编辑
     */
    public function editAction()
    {
        if ($this->isPost()) {
            $aHospital = $this->_checkData();
            if (empty($aHospital)) {
                return null;
            }
            $aHospital['iUpdateUserID'] = $this->aCurrUser['iUserID'];
            $aHospital['iHospitalID'] = intval($this->getParam('iHospitalID'));

            if (1 == Model_Hospital::updData($aHospital)) {
                return $this->showMsg('医院信息更新成功！', true);
            } else {
                return $this->showMsg('医院信息更新失败！', false);
            }
        } else {
            $this->_response->setHeader('Access-Control-Allow-Origin', '*');

            $iHospitalID = intval($this->getParam('id'));
            $aHospital = Model_Hospital::getDetail($iHospitalID);
            if (!$aHospital) {
                $this->redirect('/evaluation/medical.html');
            }
            $aCity = Model_City::getDetail($this->iCurrCityID);
            $aRegion = $this->_getCityRegion($aCity['sFullPinyin']);
            $this->assign('aRegion', $aRegion);
            $this->assign('aHospital', $aHospital);
            $this->_assignConfig();
        }
    }

    /**
     * 医院删除
     */
    public function delAction()
    {
        $iHospitalID = intval($this->getParam('id'));
        $aHospital = Model_Hospital::getDetail($iHospitalID);
        if (1 == $aHospital['iStatus']) {
            $iRet = Model_Hospital::delData($iHospitalID);

            if ($iRet) {
                return $this->showMsg('医院删除成功！', true);
            } else {
                return $this->showMsg('医院删除失败！', false);
            }

        } else {
            return $this->showMsg('医院删除成功！', false);
        }
    }

    /*
     * 医院输入自动提示 add by cjj
     *
     * */
    public function autoCompleteAction() {

        $sKey = $this->getParam('sKey');
        $aWhere = array(
            'iStatus' => 1,
            'iCityID' => $this->iCurrCityID
        );
        if (!empty($sKey)) {
            $aWhere['sName LIKE'] = '%' . $sKey . '%';
            $result = Model_Hospital::getAll(array('where' => $aWhere, 'limit' => '0, 10'));
            $data = array();
            if(!empty($result)) {
                foreach($result as $k => $item) {
                    $data[$k]['id'] = $item['iHospitalID'];
                    $data[$k]['sName'] = $item['sName'];
                }
            }
        } else {
            $data = '';
        }
        return $this->showMsg($data, false);
    }

    private function _assignConfig()
    {
        $aLevel = Yaf_G::getConf('aLevel', 'hospital', 'evaluation');
        $aType = Yaf_G::getConf('aType', 'hospital', 'evaluation');
        $aProperty = Yaf_G::getConf('aProperty', 'hospital', 'evaluation');
        $aIsMedicalPoint = Yaf_G::getConf('aIsMedicalPoint', 'hospital', 'evaluation');
        $this->assign('aLevel', $aLevel);
        $this->assign('aType', $aType);
        $this->assign('aProperty', $aProperty);
        $this->assign('aIsMedicalPoint', $aIsMedicalPoint);
    }

    private function _getCityRegion($p_sCityCode)
    {
        $ormRegion = Util_Common::getOrm('cric_xf', 'Region', true);

        return $ormRegion->fetchAll(
            array(
                'where' => array(
                    'CityCode' => $p_sCityCode
                )
            )
        );
    }

    /**
     * 检测参数
     * @return array|void
     */
    private function _checkData()
    {

        $aLevel = Yaf_G::getConf('aLevel', 'hospital', 'evaluation');
        $aType = Yaf_G::getConf('aType', 'hospital', 'evaluation');
        $aProperty = Yaf_G::getConf('aProperty', 'hospital', 'evaluation');
        $aIsMedicalPoint = Yaf_G::getConf('aIsMedicalPoint', 'hospital', 'evaluation');
        $sName = $this->getParam('sName');
        $iLevel = intval($this->getParam('iLevel'));
        $iProperty = intval($this->getParam('iProperty'));
        $iMedicalPoint = intval($this->getParam('iMedicalPoint'));
        $iType = intval($this->getParam('iType'));
        $sContact = $this->getParam('sContact');
        $sAddress = $this->getParam('sAddress');
        $iRegionID = intval($this->getParam('iRegionID'));

        if (!Util_Validate::isLength($sName, 2, 20)) {
            return $this->showMsg('医院名称长度范围为2到20个字！', false);
        }

        if (!in_array($iLevel, array_keys($aLevel))) {
            return $this->showMsg('请正确选择的等级', false);
        }

        if (empty($iRegionID)) {
            return $this->showMsg('请选择对应的区域', false);
        }

        if (empty($sAddress)) {
            return $this->showMsg('请填写医院地址', false);
        }
        /**
        if (!in_array($iType, array_keys($aType))) {
            return $this->showMsg('请正确选择的类型', false);
        }

        if (!in_array($iProperty, array_keys($aProperty))) {
            return $this->showMsg('请正确选择的属性', false);
        }

        if (!in_array($iMedicalPoint, array_keys($aIsMedicalPoint))) {
            return $this->showMsg('请正确选择的定点医保', false);
        }*/

        $aRow = array(
            'sName'         => $sName,
            'iLevel'        => $iLevel,
            'iType'         => $iType,
            'iProperty'     => $iProperty,
            'iMedicalPoint' => $iMedicalPoint,
            'sContact'      => Util_Tools::substr($sContact, 0, 255),
            'sAddress'      => Util_Tools::substr($sAddress, 0, 255),
            'iRegionID'     => $iRegionID,
            'iCityID'       => $this->iCurrCityID
        );


        return $aRow;

    }
}