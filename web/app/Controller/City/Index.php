<?php
/**
 * Created by PhpStorm
 * Date: 2015/2/2
 * Time: 13:27
 */

class Controller_City_Index extends Controller_Base
{
    /**
     * 切换城市*/
    public function ChangeAction()
    {
        $sCityCode = $this->getParam('city');
        $aCity= Model_City::getCityInfo($sCityCode);
        if(empty($aCity)){
            return $this->redirect("/welcome");
        }

        Util_Cookie::delete("aCity");
        Util_Cookie::set('aCity', ['iCityID' => $aCity['ID'], 'sCityName' => $aCity['CityName'], 'sFullPinyin' => $aCity['CityCode']]);

        return $this->redirect("/");
    }
}