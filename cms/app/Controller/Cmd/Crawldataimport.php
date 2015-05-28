<?php
/**
 * Created by PhpStorm.
 * User: xiaoyao
 * Date: 2015/1/20
 * Time: 9:44
 */

/**
 * Class Controller_Cmd_CrawlDataImport
 * 导入抓取过来的数据
 */

class Controller_Cmd_Crawldataimport extends Controller_Cmd_Base
{

    /**
     * 医院数据导入
     */
    public function hospitalsAction()
    {
        $ormHospitals = Util_Common::getOrm('crawl_data', 't_hospitals', true);

        $ormRegion = Util_Common::getOrm('cric_xf', 'Region', true);
        $iCnt = $ormHospitals->fetchCnt();
        //$iCnt = 1;
        $iLimit = 500;
        $page = ceil($iCnt / 500);
        echo "start import hospital", PHP_EOL;
        for ($i = 0; $i < $page; $i++) {
            $iStart = $i * $iLimit;
            echo $iStart .' / ' . $iCnt . ' = '. ceil($iStart / $iCnt * 100) . ' % finish', PHP_EOL;

            $hospitalsList = $ormHospitals->fetchAll(array('limit' => $iStart.','.$iLimit));
            $this->impotHospitalsData($hospitalsList, $ormRegion);
            unset($hospitalsList);
            Util_Common::getDebug()->clear();
            //print_r(Util_Common::getDebug()->getAll());exit;
        }
        echo "finish import hospital", PHP_EOL;

    }

    public function oneAction() {
        $ormHospitals = Util_Common::getOrm('crawl_data', 't_hospitals', true);

        $ormRegion = Util_Common::getOrm('cric_xf', 'Region', true);
        $hospitalsList = $ormHospitals->fetchAll(array('where' => array('id IN' => array(4773))));
        $this->impotHospitalsData($hospitalsList, $ormRegion);
    }

    private function impotHospitalsData($aHospitals, $ormRegion)
    {
        if ($aHospitals) {
            $aType = Yaf_G::getConf('aType', 'hospital', 'evaluation');
            $aLevel = Yaf_G::getConf('aLevel', 'hospital', 'evaluation');
            $aProperty = Yaf_G::getConf('aProperty', 'hospital', 'evaluation');
            $aIsMedicalPoint = Yaf_G::getConf('aIsMedicalPoint', 'hospital', 'evaluation');

            foreach($aHospitals as $key => $hospital) {

                $sCitySQL = 'SELECT * FROM t_city WHERE instr( "'.$hospital['city'].'", sCityName)';
                $aCity = Model_City::query($sCitySQL);
                if ($aCity) {
                    $iCityID = $aCity[0]['iCityID'];
                    $sRegionSQL = 'SELECT * FROM Region WHERE CityCode=\''.$aCity[0]['sFullPinyin'].'\' AND instr("'.$hospital['region'].'", RegionName)';
                    //echo $sRegionSQL;

                    $aRegion = $ormRegion->query($sRegionSQL);
                    //print_r($aRegion);
                    $iRegion = 0;
                    if ($aRegion) {
                        $iRegion = $aRegion[0]['ID'];
                    }
                    $aRow = Model_Hospital::getRow(['where' => ['sName' => $hospital['name'], 'iCityID' => $iCityID]]);
                    if ($aRow) {
                        $aUpdateHospital = [];
                        $aUpdateHospital['sAddress'] = trim(str_replace('地址：', '', $hospital['address']));
                        $aUpdateHospital['sContact'] = trim(str_replace('电话：', '', $hospital['contact']));
                        $aUpdateHospital['iMedicalPoint'] = array_search($hospital['medicalPoint'], $aIsMedicalPoint);
                        if (array_search($hospital['level'], $aLevel)) {
                            $aUpdateHospital['iLevel'] = array_search($hospital['level'], $aLevel);
                            $aUpdateHospital['iProperty'] = array_search($hospital['sproperty'], $aProperty);

                        } else {
                            $aUpdateHospital['iLevel'] = 99;
                            $aUpdateHospital['iProperty'] = array_search($hospital['level'], $aProperty);
                        }

                        $aUpdateHospital['iType'] = array_search($hospital['stype'], $aType);

                        $aUpdateHospital['iHospitalID'] = $aRow['iHospitalID'];
                        $aUpdateHospital['iRegionID'] = $iRegion;
                        Model_Hospital::updData($aUpdateHospital);
                        unset($aUpdateHospital);
                        unset($aRow);
                    } else {
                        $aInsertHospital = [];
                        $aInsertHospital['sName'] = trim($hospital['name']);
                        $aInsertHospital['iUpdateTime'] = $aInsertHospital['iCreateTime'] = time();
                        $aInsertHospital['sAddress'] = trim(str_replace('地址：', '', $hospital['address']));
                        $aInsertHospital['sContact'] = trim(str_replace('电话：', '', $hospital['contact']));
                        $aInsertHospital['iMedicalPoint'] = array_search($hospital['medicalPoint'], $aIsMedicalPoint);
                        if (array_search($hospital['level'], $aLevel)) {
                            $aInsertHospital['iLevel'] = array_search($hospital['level'], $aLevel);
                            $aInsertHospital['iProperty'] = array_search($hospital['sproperty'], $aProperty);

                        } else {
                            $aInsertHospital['iLevel'] = 99;
                            $aInsertHospital['iProperty'] = array_search($hospital['level'], $aProperty);
                        }

                        //$aInsertHospital['iLevel'] = array_search($hospital['level'], $aLevel);
                        //$aInsertHospital['iProperty'] = array_search($hospital['sproperty'], $aProperty);
                        $aInsertHospital['iType'] = array_search($hospital['stype'], $aType);
                        $aInsertHospital['iCityID'] = $iCityID;
                        $aInsertHospital['iRegionID'] = $iRegion;
                        Model_Hospital::addData($aInsertHospital);
                        unset($aInsertHospital);
                    }

                }

            }
        }
    }





    /**
     * 医院数据导入
     */
    public function metroAction()
    {
        $ormMetro = Util_Common::getOrm('crawl_data', 't_metro', true);

        $iCnt = $ormMetro->fetchCnt();
        //$iCnt = 1;
        $iLimit = 500;
        $page = ceil($iCnt / 500);
        echo "start import metro", PHP_EOL;
        for ($i = 0; $i < $page; $i++) {
            $iStart = $i * $iLimit;
            echo $iStart .' / ' . $iCnt . ' = '. ceil($iStart / $iCnt * 100) . ' % finish', PHP_EOL;
            $aMetroList = $ormMetro->fetchAll(array('limit' => $iStart.','.$iLimit));
            $this->importMetroData($aMetroList);
            unset($aMetroList);
        }
        echo "finish import metro", PHP_EOL;

    }

    public function onemetroAction() {
        $ormMetro = Util_Common::getOrm('crawl_data', 't_metro', true);

        //, 156, 143,147,139
        $aMetroList = $ormMetro->fetchAll(array('where' => array('id IN' => array(118))));
        $this->importMetroData($aMetroList);
    }

    private function importMetroData($aMetroList)
    {
        $ormMetroNew = Util_Common::getOrm('crawl_data', 't_metro_new', true);
        $ormMetroStation = Util_Common::getOrm('crawl_data', 't_metro_station', true);
        $ormCmsMetro = Util_Common::getOrm('cms', 't_metro', true);
        $ormCmsMetroStation = Util_Common::getOrm('cms', 't_metro_station', true);
        $ormMetroStationNew = Util_Common::getOrm('crawl_data', 't_metro_station_new', true);

        if ($aMetroList) {

            $aOpen = Yaf_G::getConf('aOpen', 'metro', 'evaluation');

            foreach($aMetroList as $key => $metro) {
                //print_r($metro);

                $sCitySQL = 'SELECT * FROM t_city WHERE instr( "'.$metro['city'].'", sCityName)';
                echo $metro['title'], PHP_EOL;
                $aCity = Model_City::query($sCitySQL);
                if ($aCity) {
                    echo $aCity[0]['sCityName'], PHP_EOL;
                    $iCityID = $aCity[0]['iCityID'];

                    $aRow = $ormCmsMetro->fetchRow(['where' => ['sMetroName' => $metro['title'], 'iCityID' => $iCityID]]);
                    $iMetroID = 0;
                    if ($aRow) {

                        $aUpdateMetro = [];
                        $aUpdateMetro['sDesc'] = '';
                        $aUpdateMetro['sSERunTime'] = trim(str_replace('起点站首末车时间：', '', $metro['startFLTime']));
                        $aUpdateMetro['sESRunTime'] = trim(str_replace('终点站首末车时间：', '', $metro['endFLTime']));
                        $aUpdateMetro['sCompany'] = trim(str_replace('所属公司：', '', $metro['metroCorp']));
                        $aUpdateMetro['sInterval'] = trim(str_replace('发车间隔：', '', $metro['departTime']));
                        $aUpdateMetro['sPrice'] = trim($metro['pjInfo']);
                        $aUpdateMetro['iOpen'] = array_search($metro['metroType'], $aOpen);
                        $aUpdateMetro['iUpdateTime'] = time();
                        $iMetroID = $aUpdateMetro['iMetroID'] = $aRow['iMetroID'];
                        $ormCmsMetro->update($aUpdateMetro);
                        unset($aUpdateMetro);
                        unset($aRow);
                    } else {
                        $aInsertMetro = [];
                        $aInsertMetro['sDesc'] = '';
                        $aInsertMetro['sSERunTime'] = trim(str_replace('起点站首末车时间：', '', $metro['startFLTime']));
                        $aInsertMetro['sESRunTime'] = trim(str_replace('终点站首末车时间：', '', $metro['endFLTime']));
                        $aInsertMetro['sCompany'] = trim(str_replace('所属公司：', '', $metro['metroCorp']));
                        $aInsertMetro['sInterval'] = trim(str_replace('发车间隔：', '', $metro['departTime']));
                        $aInsertMetro['sPrice'] = trim($metro['pjInfo']);
                        $aInsertMetro['iOpen'] = array_search($metro['metroType'], $aOpen);
                        $aInsertMetro['sMetroName'] = trim($metro['title']);
                        $aInsertMetro['sDesc'] = '';
                        $aInsertMetro['iUpdateTime'] = $aInsertMetro['iCreateTime'] = time();
                        $aInsertMetro['iCityID'] = $iCityID;
                        $aInsertMetro['iStatus'] = 1;
                        $iMetroID = $ormCmsMetro->insert($aInsertMetro);
                        unset($aInsertMetro);
                    }
                    $aStationList = $ormMetroStation->fetchAll(['where' => ['metroID' => $metro['id']]]);
                    $aStationList = json_decode($metro['stationList'], true);
                    if ($aStationList) {
                        $i = 0;
                        $sMetroName = trim(str_replace('地铁', '', $metro['title']));
                        $sMetroName = trim(str_replace('一', '1', $sMetroName));


                        $sMetroNewSQL = 'SELECT * FROM t_metro_new WHERE city="'.$aCity[0]['sCityName'].'" and instr(title, "'.$sMetroName.'")';
                        echo $sMetroNewSQL;
                        $aMetroNew = [];
                        $aMetroNewTmp = $ormMetroStationNew->query($sMetroNewSQL);
                        if ($aMetroNewTmp) {
                            $aMetroNew = $aMetroNewTmp[0];
                        }
//                        echo $sMetroName;
//                        $aMetroNew = $ormMetroNew->fetchRow(
//                            array(
//                            'where' =>
//                                array(
//                                    'city' => $aCity[0]['sCityName'],
//                                    'title IN' => array("'".$sMetroName."'")
//                                )
//                            )
//                        );
//
                        if (!$aMetroNew) {
                            $sMetroName = Util_Tools::substr($sMetroName, 0,mb_strpos($sMetroName, '线')-1);
                            $sMetroNewSQL = 'SELECT * FROM t_metro_new WHERE city="'.$aCity[0]['sCityName'].'" and instr(title, "'.$sMetroName.'")';
                            echo $sMetroNewSQL;
                            $aMetroNew = [];
                            $aMetroNewTmp = $ormMetroStationNew->query($sMetroNewSQL);
                            if ($aMetroNewTmp) {
                                $aMetroNew = $aMetroNewTmp[0];
                            }

                        }

                        //var_dump($aMetroNew);
                        //echo $sMetroName;
                        //print_r($aStationList);
                        foreach($aStationList as $sStationName) {
                            echo $sStationName, PHP_EOL;
                            $aStation = $ormMetroStation->fetchRow(['where' => ['metroID' => $metro['id'], 'name' => $sStationName]]);
                            $row = $ormCmsMetroStation->fetchRow(['where' => ['iMetroID' => $iMetroID, 'sStation' => $aStation['name']]]);

                            $aStationNew = [];
                            $sSEFirstTime = '';
                            $sSELastTime = '';
                            $sESFirstTime = '';
                            $sESLastTime = '';
                            //print_r($aStation);
                            //echo $sMetroName;
                            //print_r($aMetroNew);
                            if ($aMetroNew) {
                                //echo $aStation['name'];
                                $sStationNewSQL = 'SELECT * FROM t_metro_station_new WHERE city="'.$aCity[0]['sCityName'].'" and instr( "'.$aStation['name'].'", name)';
                                echo $sStationNewSQL;
                                $aStationNewTmp = $ormMetroStationNew->query($sStationNewSQL);
                                //print_r($aStationNewTmp);
                                //print_r($aStationNewTmp);
                                if ($aStationNewTmp) {
                                    print_r(json_decode($aStationNewTmp[0]['operationTime'], true));
                                    if(isset($aStationNewTmp[0]['operationTime'])) {
                                        $aStationNew = json_decode($aStationNewTmp[0]['operationTime'], true);
                                        if($aStationNew) {
                                            foreach($aStationNew as $key => $value) {
                                                $sTmpMetroName = Util_Tools::substr($value['name'], 0,mb_strpos($value['name'], '线')-1);
                                                if(mb_strpos($sMetroName, $sTmpMetroName) === false) {

                                                } else {
                                                    if(isset($value['operationTime'][0])) {
                                                        $aTmp = explode('|', $value['operationTime'][0]);
                                                        $sSEFirstTime = Util_Tools::substr($aTmp[0], mb_strpos($aTmp[0],'首班车时间：')+6, -1);
                                                        $sSELastTime = trim(Util_Tools::substr($aTmp[1], mb_strpos($aTmp[1],'末班车时间：')+6, -1));

                                                    }

                                                    if(isset($value['operationTime'][1])) {
                                                        $aTmp = explode('|', $value['operationTime'][1]);

                                                        $sESFirstTime = Util_Tools::substr($aTmp[0], mb_strpos($aTmp[0],'首班车时间：')+6, -1);
                                                        $sESLastTime = trim(Util_Tools::substr($aTmp[1], mb_strpos($aTmp[1],'末班车时间：')+6, -1));
                                                    }
                                                }
                                            }
                                        }
                                    }

                                }
                            }
                            //exit;
                            $sGeo = $aStation['baiduCoord'];
//                            if( $aStation['name'] == '莲花路站') {
//                                print_r($aStationNewTmp);
//                            }
                            if (empty($sGeo) && isset($aStationNewTmp[0]['baiduCoord'])) {

                                $sGeo = $aStationNewTmp[0]['baiduCoord'];
                            }

                            if ($row) {
                                $aUpdateMetroStation = [];
                                $aUpdateMetroStation['iOrder'] = $i;
                                $aUpdateMetroStation['sGeo'] = $sGeo;
                                $aUpdateMetroStation['sSEFirstTime'] = $sSEFirstTime == '-' ||  $sSEFirstTime == '--' ? '' : $sSEFirstTime;
                                $aUpdateMetroStation['sSELastTime'] = $sSELastTime == '-' ||  $sSELastTime == '--'  ? '' : $sSELastTime;
                                $aUpdateMetroStation['sESFirstTime'] = $sESFirstTime == '-' ||  $sESFirstTime == '--'  ? '' : $sESFirstTime;
                                $aUpdateMetroStation['sESLastTime'] = $sESLastTime == '-' ||  $sESLastTime == '--'  ? '' : $sESLastTime;
                                $aUpdateMetroStation['iUpdateTime'] = time();
                                $aUpdateMetroStation['iAutoID'] = $row['iAutoID'];
                                $ormCmsMetroStation->update($aUpdateMetroStation);
                            } else {
                                $aInsertMetroStation = [];
                                $aInsertMetroStation['iMetroID'] = $iMetroID;
                                $aInsertMetroStation['iStatus'] = 1;
                                $aInsertMetroStation['sStation'] = $aStation['name'];
                                $aInsertMetroStation['iOrder'] = $i;
                                $aInsertMetroStation['sGeo'] = $sGeo;
                                $aUpdateMetroStation['sSEFirstTime'] = $sSEFirstTime == '-' ||  $sSEFirstTime == '--'  ? '' : $sSEFirstTime;
                                $aUpdateMetroStation['sSELastTime'] = $sSELastTime == '-' ||  $sSELastTime == '--'  ? '' : $sSELastTime;
                                $aUpdateMetroStation['sESFirstTime'] = $sESFirstTime == '-' ||  $sESFirstTime == '--'  ? '' : $sESFirstTime;
                                $aUpdateMetroStation['sESLastTime'] = $sESLastTime == '-' ||  $sESLastTime == '--'  ? '' : $sESLastTime;

                                $aInsertMetroStation['iUpdateTime'] = $aInsertMetroStation['iCreateTime'] = time();
                                $aInsertMetroStation['iStatus'] = 1;
                                print_r($aInsertMetroStation);
                                $ormCmsMetroStation->insert($aInsertMetroStation);
                            }
                            $i++;
                        }
                    }
                }
            }
        }
    }

}