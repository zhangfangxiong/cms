<?php

class Controller_Cmd_User extends Controller_Cmd_Base
{
    public function importAction ()
    {
        $handle = fopen("/data/www/xjc/user.csv", "r");
        $n = 0;
        while (($data = fgetcsv($handle, 4096, ",")) !== FALSE) {
            $n++;
            if ($n == 1) {
                continue;
            }
            if (count($data) < 5) {
                continue;
            }
            foreach ($data as $k => $v) {
                $data[$k] = trim($v);
            }
            
            if ($data[0] == '全国') {
                $iCityID = 1;
                $sCityID = -1; 
            } else {
                $aCity = Model_City::getCityByName($data[0]);
                $iCityID = $aCity['iCityID'];
                $sCityID = $aCity['iCityID'];
            }
            $aTmp = explode('+', $data[3]);
            $aRole = array();
            foreach ($aTmp as $v) {
                $r = Model_Role::getRoleByName(trim($v));
                $aRole[] = $r['iRoleID'];
            }
            
            $aRow = array(
                'sUserName' => trim($data[5]),
                'sPassword' => '33229d1126566a462b8495634e56515a',
                'sRealName' => trim($data[1]),
                'sUserCode' => trim($data[2]),
                'sPosition' => trim($data[4]),
                'sEmail'    => trim($data[5]),
                'sMobile'   => trim($data[6]),
                'iCityID'   => $iCityID,
                'sCityID'   => $sCityID,
                'sRoleID'   => join(',', $aRole)
            );
            Model_User::addData($aRow);
            //print_r($aRow);exit;
        }
        fclose($handle);
    }
}