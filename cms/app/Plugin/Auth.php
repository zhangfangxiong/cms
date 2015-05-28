<?php

class Plugin_Auth extends Yaf_Plugin
{

    public function preDispatch (Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
        //$sModName = $request->getModuleName();
        //$sCtrName = $request->getControllerName();
        //$sActName = $request->getActionName();
        //echo "$sModName, $sCtrName, $sActName";exit;
//         if ($sModName != 'User' || $sCtrName != 'Index') {
//             $aCookie = Util_Cookie::get(Yaf_G::getConf('authkey', 'cookie'));
//             if (empty($aCookie)) {
                
//             }
//             print_r($aCookie);exit;
//         }
    }
}