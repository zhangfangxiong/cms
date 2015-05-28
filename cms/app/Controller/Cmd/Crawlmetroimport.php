<?php
/**
 * Created by PhpStorm.
 * User: len
 * Date: 2015/2/27
 * Time: 15:37
 */

class Controller_Cmd_Crawlmetroimport extends Controller_Cmd_Base
{


    public function indexAction()
    {
        ini_set('memory_limit','512M');
        $ormMetro = Util_Common::getOrm('crawl_data', 't_metro_new', true);
        $ormMetro->setNeedCache(false);
        $ormMetro->setWhereCache(false);

        $iCnt = $ormMetro->fetchCnt();
        $iLimit = 500;
        $page = ceil($iCnt / 500);
        echo "start import Metro", PHP_EOL;
        for ($i = 0; $i < $page; $i++) {
            $iStart = $i * $iLimit;
            echo $iStart .' / ' . $iCnt . ' = '. ceil($iStart / $iCnt * 100) . ' % finish', PHP_EOL;
            echo memory_get_usage(), PHP_EOL;
            $aMetroList = $ormMetro->fetchAll(array('limit' => $iStart.','.$iLimit));
            echo memory_get_usage(), PHP_EOL;
            $this->_importMetroData($aMetroList);
            $ormMetro->clearAllCache();
            unset($busList);
            echo memory_get_usage(), PHP_EOL;
            Util_Common::getDebug()->clear();
            print_r(Util_Common::getDebug()->getAll());//exit;
        }



        echo "finish import Metro", PHP_EOL;
    }

    public function importStationAction()
    {
        ini_set('memory_limit','512M');
        $ormMetro = Util_Common::getOrm('crawl_data', 't_metro_new', true);
        $ormMetro->setNeedCache(false);
        $ormMetro->setWhereCache(false);

        $iCnt = $ormMetro->fetchCnt();
        $iLimit = 500;
        $page = ceil($iCnt / 500);
        echo "start import Metro station", PHP_EOL;
        for ($i = 0; $i < $page; $i++) {
            $iStart = $i * $iLimit;
            echo $iStart .' / ' . $iCnt . ' = '. ceil($iStart / $iCnt * 100) . ' % finish', PHP_EOL;
            echo memory_get_usage(), PHP_EOL;
            $aMetroList = $ormMetro->fetchAll(array('limit' => $iStart.','.$iLimit));
            echo memory_get_usage(), PHP_EOL;
            $this->_importMetroStationData($aMetroList);
            $ormMetro->clearAllCache();
            unset($busList);
            echo memory_get_usage(), PHP_EOL;
            Util_Common::getDebug()->clear();
            print_r(Util_Common::getDebug()->getAll());//exit;

        }
        echo "finish import Metro Station", PHP_EOL;
    }


    private function _importMetroStationData($aMetroList)
    {
        $ormMetroStation = Util_Common::getOrm('crawl_data', 't_metro_station_new', true);
        $ormMetroStation->setNeedCache(false);
        $ormMetroStation->setWhereCache(false);
        /**
        $ormMetroStationRel = Util_Common::getOrm('cms', 't_metro_station_rel', true);
        $ormMetroStationRel->setNeedCache(false);
        $ormMetroStationRel->setWhereCache(false);*/

        $ormMetroDirection = Util_Common::getOrm('cms', 't_metro_direction', true);
        $ormMetroDirection->setNeedCache(false);
        $ormMetroDirection->setWhereCache(false);

        $ormMetroStationRuntime = Util_Common::getOrm('cms', 't_metro_station_runtime', true);
        $ormMetroStationRuntime->setNeedCache(false);
        $ormMetroStationRuntime->setWhereCache(false);

        foreach ($aMetroList as $aMetro) {

            $sCitySQL = 'SELECT * FROM t_city WHERE instr( "'.$aMetro['city'].'", sCityName)';
            $aCity = Model_City::query($sCitySQL);
            $iCityID = 0;
            if ($aCity) {
                $iCityID = $aCity[0]['iCityID'];
            }
            $aMetroInfo = Model_Metro::getRow(['where' => ['sMetroName' => $aMetro['title'], 'iCityID' => $iCityID]]);
            if (empty($aMetroInfo)) {
                continue;
            }
            //print_r($aMetroInfo);exit;
            $aStationList = json_decode($aMetro['stationList'], true);
            $i = 0;
            foreach ($aStationList as $sStation) {
                $aStationInfo = Model_MetroStation::getRow(['where' => ['sStation' => $sStation, 'iMetroID' => $aMetroInfo['iMetroID']]]);

                if (empty($aStationInfo)) {
                    $aNewStation = $ormMetroStation->fetchRow(['where' => ['name' => $sStation]]);
                    $insertStation = [];
                    $insertStation['sStation'] = $sStation;
                    $insertStation['iStatus'] = 1;
                    $insertStation['sGeo'] = '';
                    $insertStation['iUpdateTime'] = $insertStation['iCreateTime'] = time();
                    if (empty($aNewStation['baiduCoord'])) {
                        $insertStation['iOpen'] = 0;
                    } else {
                        $insertStation['sGeo'] = $this->_getBaiduCoord($aNewStation['baiduCoord']);
                    }
                    $insertStation['iMetroID'] = $aMetroInfo['iMetroID'];
                    $insertStation['iOrder'] = $i;
                    //添加站点基本信息
                    $iStationID = Model_MetroStation::addData($insertStation);
                    unset($insertStation);
                } else {
                    $iStationID = $aStationInfo['iAutoID'];
                }
                /**
                $insertMetroStationRel = [];
                $insertMetroStationRel['iStationID'] = $iStationID;
                $insertMetroStationRel['iMetroID'] = $aMetroInfo['iMetroID'];
                $insertMetroStationRel['iOrder'] = $i;
                $insertMetroStationRel['iStatus'] = 1;
                $insertMetroStationRel['iOrder'] = $i;
                $insertMetroStationRel['iUpdateTime'] = $insertMetroStationRel['iCreateTime'] = time();
                $ormMetroStationRel->insert($insertMetroStationRel);
                unset($insertMetroStationRel);
                 */
                $i++;
                //添加线路站点关联信息

                $aOperationTime = json_decode($aNewStation['operationTime'], true);
                if ($aOperationTime) {
                    foreach ($aOperationTime as $operationTime) {
                        //$tmpMetro = Model_Metro::getRow(['where' => ['sMetroName' => $operationTime['name'], 'iCityID' => $iCityID]]);
                        if ($operationTime['name'] == $aMetroInfo['sMetroName']) {
                            //添加线路方向信息
                            if (!empty($operationTime['operationTime'])) {
                                foreach ($operationTime['operationTime'] as $value) {
                                    $aDirection = explode('方向', $value);
                                    $sDirection = trim(Util_Tools::substr($aDirection[0], mb_strpos($aDirection[0],'往')+1, -1));
                                    $sDirection = preg_replace('/([\d:\-]+)/', '', $sDirection);
                                    if(!empty($sDirection)) {
                                        $aDirectionInfo = $ormMetroDirection->fetchRow(
                                            [
                                                'where' => [
                                                    'iMetroID' => $aMetroInfo['iMetroID'],
                                                    'sDirection' => $sDirection
                                                ]
                                            ]
                                        );
                                        if (empty($aDirectionInfo)) {
                                            $insertDirection = [];
                                            $insertDirection['iMetroID'] = $aMetroInfo['iMetroID'];
                                            $insertDirection['iStatus'] = 1;
                                            $insertDirection['iUpdateTime'] = $insertDirection['iCreateTime'] = time();
                                            $insertDirection['sDirection'] = $sDirection;
                                            $iDirectionID = $ormMetroDirection->insert($insertDirection);
                                            unset($insertDirection);
                                        } else {
                                            $iDirectionID = $aDirectionInfo['iAutoID'];
                                        }

                                        $aTmp = explode('|', $value);
                                        $sFRunTime = trim(Util_Tools::substr($aTmp[0], mb_strpos($aTmp[0],'首班车时间：')+6, -1));
                                        $sLRunTime = trim(Util_Tools::substr($aTmp[1], mb_strpos($aTmp[1],'末班车时间：')+6, -1));
                                        $insertStationRunTime = [];
                                        $insertStationRunTime['iStationID'] = $iStationID;
                                        $insertStationRunTime['sFRunTime'] = $sFRunTime;
                                        $insertStationRunTime['sLRunTime'] = $sLRunTime;
                                        $insertStationRunTime['iDirectionID'] = $iDirectionID;
                                        $insertStationRunTime['iStatus'] = 1;
                                        $insertStationRunTime['iUpdateTime'] = $insertStationRunTime['iCreateTime'] = time();
                                        $ormMetroStationRuntime->insert($insertStationRunTime);
                                        unset($insertStationRunTime);
                                    }
                                }
                            }

                        }
                    }
                }
            }
        }
    }

    private function _importMetroData($aMetroList)
    {
        $aOpen = Yaf_G::getConf('aOpen', 'metro', 'evaluation');
        foreach ($aMetroList as $aMetro) {
            $sCitySQL = 'SELECT * FROM t_city WHERE instr( "'.$aMetro['city'].'", sCityName)';
            $aCity = Model_City::query($sCitySQL);
            if ($aCity) {
                $iCityID = $aCity[0]['iCityID'];
                $insertMetro = [];
                $aRow = Model_Metro::getRow(
                    [
                        'where' => ['sMetroName' => $aMetro['title'], 'iCityID' => $iCityID]
                    ]
                );
                if ($aRow) {
                    $aUpdateMetro = [];

                    $aUpdateMetro['iOpen'] = array_search($aMetro['metroType'], $aOpen);
                    $aUpdateMetro['iUpdateTime'] = time();
                    $iMetroID = $aUpdateMetro['iMetroID'] = $aRow['iMetroID'];
                    Model_Metro::updData($aUpdateMetro);
                    unset($aUpdateMetro);
                    unset($aRow);

                } else {
                    $insertMetro['iStatus'] = 1;
                    $insertMetro['iCreateTime'] = $insertMetro['iUpdateTime'] = time();
                    $insertMetro['sOperationName'] = '';
                    $insertMetro['iOpen'] = array_search($aMetro['metroType'], $aOpen);
                    $insertMetro['sMetroName'] = $aMetro['title'];
                    $insertMetro['iCityID'] = $iCityID;
                    $iMetroID = Model_Metro::addData($insertMetro);
                    unset($insertMetro);
                }
            }
        }
    }

    public function testbaiducoordAction()
    {
        $this->_getBaiduCoord('126.71711,45.79525');
    }

    private function _getBaiduCoord($lat)
    {
        return $lat;
        $url = 'http://api.map.baidu.com/geoconv/v1/?coords=' . $lat . '&ak=q32WT64oio1Kt83T8bs16qYK&output=json';
        $httpClient = new Util_HttpClient();
        $result = $httpClient->get($url);
        $aRet = json_decode($httpClient->content, true);
        if(isset($aRet['result'][0])) {
            return sprintf("%01.5f", $aRet['result'][0]['x']).','.sprintf("%01.5f", $aRet['result'][0]['y']);
        } else {
            return $lat;
        }

    }
}