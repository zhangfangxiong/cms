<?php

class Controller_Cmd_Cricin extends Controller_Cmd_Base
{

    public function syncallAction ()
    {
        $iMaxProcess = $this->getParam('ps', 10);
        $oCricIn = new Model_CricIn();
        $oCricIn->syncAll($iMaxProcess);
    }

    public function synctableAction ()
    {
        $sTable = $this->getParam('tbl');
        $aTable = explode(',', $sTable);
        $oCricIn = new Model_CricIn();
        $oCricIn->syncTable($aTable);
    }
    
    public function syncneedAction ()
    {
        $iMaxProcess = $this->getParam('ps', 10);
        $oCricIn = new Model_CricIn();
        $oCricIn->syncNeed($iMaxProcess);
    }

    public function syncmutiAction ()
    {
        $aParam = $this->getParams();
        $oCricIn = new Model_CricIn();
        $oCricIn->syncMutiData($aParam['table'], $aParam['offset'], $aParam['limit'], $aParam['pid']);
    }

    public function dropAction ()
    {
        $sKeyword = $this->getParam('kv', '');
        $oCricIn = new Model_CricIn();
        $oCricIn->dropTable($sKeyword);
    }
    
    public function statAction ()
    {
        $sType = $this->getParam('type', 'need');
        $oCricIn = new Model_CricIn();
        $oCricIn->syncStat($sType);
    }

    public function countAction ()
    {
        $oCricIn = new Model_CricIn();
        $oCricIn->countData();
    }

    public function tableAction ()
    {
        $oCricIn = new Model_CricIn();
        $aTable = $oCricIn->getAllTable();
        foreach ($aTable as $tbl) {
            echo "$tbl \n";
        }
    }

    public function repairgpsAction ()
    {
        return false;
        if (($handle = fopen("/tmp/district.csv", "r")) !== FALSE) {
            file_put_contents('/tmp/district.sql', '');
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($data) != 4) {
                    continue;
                }
                $aGeo = Util_Gps::getAmapGps($data[1], $data[3]);
                if ($aGeo['lng'] == 0 || $aGeo['lat'] == 0) {
                    $aGeo = Util_Gps::getAmapGps($data[1], $data[2]);
                }
                if ($aGeo['lng'] == 0 || $aGeo['lat'] == 0) {
                    $aGeo = Util_Gps::getAmapGps($data[1], $data[1]);
                }
                if ($aGeo['lng'] == 0 || $aGeo['lat'] == 0) {
                    echo 'Error:' . join(',', $data) . "\n";
                    exit();
                }
                $sSQL = "UPDATE District SET X=" . $aGeo['lng'] . ",Y=" . $aGeo['lat'] . ' WHERE ID=' . $data[0] . ";\n";
                file_put_contents('/tmp/district.sql', $sSQL, FILE_APPEND);
                echo join(',', $data) . "\n";
            }
            fclose($handle);
        }
        
        if (($handle = fopen("/tmp/region.csv", "r")) !== FALSE) {
            file_put_contents('/tmp/region.sql', '');
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($data) != 3) {
                    continue;
                }
                $aGeo = Util_Gps::getAmapGps($data[1], $data[2]);
                if ($aGeo['lng'] == 0 || $aGeo['lat'] == 0) {
                    $aGeo = Util_Gps::getAmapGps($data[1], $data[1]);
                }
                if ($aGeo['lng'] == 0 || $aGeo['lat'] == 0) {
                    echo 'Error:' . join(',', $data) . "\n";
                    exit();
                }
                $sSQL = "UPDATE Region SET X=" . $aGeo['lng'] . ",Y=" . $aGeo['lat'] . ' WHERE ID=' . $data[0] . ";\n";
                file_put_contents('/tmp/region.sql', $sSQL, FILE_APPEND);
                echo join(',', $data) . "\n";
            }
            fclose($handle);
        }
    }
}