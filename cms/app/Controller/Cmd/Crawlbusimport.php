<?php
/**
 * Created by PhpStorm.
 * User: len
 * Date: 2015/2/27
 * Time: 11:13
 */

class Controller_Cmd_Crawlbusimport extends Controller_Cmd_Base
{



    public function indexAction()
    {
        ini_set('memory_limit','512M');
        $ormBus = Util_Common::getOrm('crawl_data', 't_bus', true);
        $ormBus->setNeedCache(false);
        $ormBus->setWhereCache(false);

        $iCnt = $ormBus->fetchCnt();
        $iLimit = 500;
        $page = ceil($iCnt / 500);
        echo "start import Bus", PHP_EOL;
        for ($i = 0; $i < $page; $i++) {
            $iStart = $i * $iLimit;
            echo $iStart .' / ' . $iCnt . ' = '. ceil($iStart / $iCnt * 100) . ' % finish', PHP_EOL;
            echo memory_get_usage(), PHP_EOL;
            $busList = $ormBus->fetchAll(array('limit' => $iStart.','.$iLimit));
            echo memory_get_usage(), PHP_EOL;
            $this->_impotBusData($busList);
            $ormBus->clearAllCache();
            unset($busList);
            echo memory_get_usage(), PHP_EOL;
            Util_Common::getDebug()->clear();
            print_r(Util_Common::getDebug()->getAll());//exit;
            //sleep(5);
        }
        echo "finish import Bus", PHP_EOL;
    }

    private function _impotBusData($aBusList)
    {
        $cmsBus = Util_Common::getOrm('cms', 't_bus', true);
        $cmsBusStation = Util_Common::getOrm('cms', 't_bus_station', true);
        $cmsBusStation->setNeedCache(false);
        $cmsBusStation->setWhereCache(false);
        $cmsBus->setNeedCache(false);
        $cmsBus->setWhereCache(false);
        $cmsBus->clearAllCache();
        $cmsBusStation->clearAllCache();
        $typeinfo = ['', '上行', '下行', '内环', '外环'];
        if ($aBusList) {
            foreach ($aBusList as $aBus) {
                $aLinetype = explode($aBus['lineTitle'], $aBus['lineType']);
                if (count($aLinetype) > 1) {
                    $sType = str_replace('公交车', '', $aLinetype[1]);
                } else {
                    if (substr($aBus['url'], -1, 0) == 1) {
                        $sType = '下行';
                    } else {
                        $sType = '上行';
                    }
                }
                $iType = array_search($sType, $typeinfo);
                Util_Common::getOrm('permission', 't_city', true)->setNeedCache(false);
                Util_Common::getOrm('permission', 't_city', true)->setWhereCache(false);
                $aCity = Model_City::getCityByName($aLinetype[0]);

                if (!empty($aCity)) {
                    $aRow = $cmsBus->fetchRow(
                        [
                            'where' => [
                                'iType' => $iType,
                                'iStatus' => 1,
                                'iCityID' => $aCity['iCityID'],
                                'sBusName' => $aBus['lineTitle']
                            ]
                        ]
                    );
                    if (empty($aRow)) {
                        $insertBus = [];
                        $insertBus['sBusName'] = $aBus['lineTitle'];
                        $insertBus['iType'] = $iType;
                        $insertBus['iStatus'] = 1;
                        $insertBus['iCreateTime'] = $insertBus['iUpdateTime'] = time();
                        $insertBus['iCityID'] = $aCity['iCityID'];
                        $insertBus['sFRunTime'] = '';
                        $insertBus['sLRunTime'] = '';
                        $insertBus['sDesc'] = str_replace('图吧公交为您只持续导航。', '', $aBus['desc']);

                        if ($aBus['startFLTime']) {
                            $startfltime = str_replace('起点站首末车时间:', '', $aBus['startFLTime']);
                            //echo $startfltime;
                            $tmp = explode('/', $startfltime);
                            $sStartTime = '';
                            $sEndTime = '';

                            if (count($tmp) > 1) {
                                if(count($tmp) > 2) {
                                    $sStartTime .= $tmp[0];
                                    $sEndTime .= end($tmp);
                                } else {
                                    $tmpTime = explode('-', $tmp[0]);
                                    //var_dump($tmpTime);exit;
                                    if (count($tmpTime) > 1) {
                                        $sStartTime .= $tmpTime[0];
                                        $sEndTime .= $tmpTime[1];
                                    } else {
                                        $sStartTime .= $tmpTime[0];
                                    }


                                    $tmpTime = explode('-', $tmp[1]);
                                    if (count($tmpTime) > 1) {
                                        $sStartTime .= '/' . $tmpTime[0];
                                        $sEndTime .= '/' . $tmpTime[1];
                                    } else {
                                        $sEndTime .= $tmpTime[0];
                                    }

                                }

                            } else {
                                $tmp = explode('(夏)', $startfltime);
                                if (count($tmp) > 1) {
                                    $tmpTime = explode('-', $tmp[0]);
                                    $sStartTime .= $tmpTime[0].'(夏)';
                                    $sEndTime .= $tmpTime[1].'(夏)';

                                    $tmpTime = explode('-', $tmp[1]);
                                    $sStartTime .= '/' . $tmpTime[0].'(冬)';
                                    $sEndTime .= '/' . $tmpTime[1];
                                } else {
                                    $tmpTime = explode('-', $startfltime);
                                    if (count($tmpTime) > 1) {
                                        $sStartTime .= $tmpTime[0];
                                        $sEndTime .= $tmpTime[1];
                                    } else {
                                        $sStartTime .= $tmpTime[0];
                                    }
                                }
                            }
                            unset($tmp);
                            unset($tmpTime);

                            $insertBus['sFRunTime'] = $sStartTime;
                            $insertBus['sLRunTime'] = $sEndTime;
                        }
                        //var_dump($insertBus);
                        //continue;
                        //exit;
                        $iBusID = $cmsBus->insert($insertBus);
                        unset($insertBus);
                        $stationList = json_decode($aBus['stationList']);
                        $i = 0;
                        foreach ($stationList as $aStation) {
                            $insertBusStation = [];
                            $insertBusStation['iUpdateTime'] = $insertBusStation['iCreateTime'] = time();
                            $insertBusStation['iBusID'] = $iBusID;
                            $insertBusStation['sStation'] = $aStation;
                            $insertBusStation['iOrder'] = $i;
                            $insertBusStation['iStatus'] = 1;
                            //$insertBusStation['sGeo'] = '';
                            $cmsBusStation->insert($insertBusStation);
                            unset($insertBusStation);
                            $i++;
                            unset($aStation);
                        }
                        unset($stationList);

                    }
                }
                unset($aBus);
            }

        }
        unset($aBusList);
    }
}