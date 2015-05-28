<?php

class Controller_Index_Clear extends Controller_Base
{
    /**
     * 清除缓存
     */
    public function cacheAction ()
    {
        $sType = $this->getParam('type'); 
        $oCache = Util_Common::getCache($sType);
        $oCache->flush();
        return $this->showMsg('清除成功！', true);
    }
    
    /**
     * 清除缓存
     */
    public function redisAction ()
    {
        $sType = $this->getParam('type');
        $oRedis = Util_Common::getRedis($sType);
        $oRedis->flushdb();
        return $this->showMsg('清除成功！', true);
    }
    
    public function showfileAction ()
    {
        echo '<pre>';
        $sFile = $this->getParam('file');
        if (file_exists($sFile)) {
            echo file_get_contents($sFile);
        } else {
            echo $sFile . " not exists!";
        }
        echo '</pre>';
        exit;
    }
    
    public function mssqlAction ()
    {
        $sDB = $this->getParam('db');
        $sSQL = $this->getParam('sql');
        
        if (strtolower(substr($sSQL, 0, 4)) == 'get ') {
            $sSQL = 'select ' . substr($sSQL, 4);
        } else {
            die("只能执行SELECT！");
        }
    
        $oDbh = Util_Common::getMsSQLDB($sDB);
        $aData = $oDbh->getAll($sSQL);

        echo '<pre>';
        print_r($aData);
        echo '</pre>';
        exit;
    }
}