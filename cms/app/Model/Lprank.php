<?php

class Model_Lprank extends Model_Base
{

    const DB_NAME = 'cms';

    const TABLE_NAME = 't_lprank';

    /**
     * 跟据城市id取得城市
     *
     * @param int $iCid            
     * @return array
     */
    public static function getByCity ($iCityID, $sMonth)
    {
        $aRow = self::getRow(array(
            'where' => array(
                'sMonth' => $sMonth,
                'iCityID' => $iCityID
            )
        ));
        
        if (! empty($aRow)) {
            $aCity = Model_City::getDetail($iCityID);
            $aRow['aMNewsID'] = explode(',', $aRow['sMNewsID']);
            $aRow['aQNewsID'] = explode(',', $aRow['sQNewsID']);
            foreach ($aRow['aMNewsID'] as $k => $iNewsID) {
                $aNews = Model_News::getDetail($iNewsID);
                if (! empty($aNews)) {
                    $aRow['aMNews'][$k] = array(
                        'iNewsID' => $aNews['iNewsID'],
                        'sShortTitle' => $aNews['sShortTitle'],
                        'sTitle' => $aNews['sTitle'],
                        'sAbstract' => $aNews['sAbstract'],
                        'sUrl' => 'http://' . Util_Common::getConf('news', 'domain') . '/' . $aCity['sFullPinyin'] . '/news/detail/' . $aNews['iNewsID']
                    );
                }
            }
            foreach ($aRow['aQNewsID'] as $k => $iNewsID) {
                $aNews = Model_News::getDetail($iNewsID);
                if (! empty($aNews)) {
                    $aRow['aQNews'][$k] = array(
                        'iNewsID' => $aNews['iNewsID'],
                        'sShortTitle' => $aNews['sShortTitle'],
                        'sTitle' => $aNews['sTitle'],
                        'sAbstract' => $aNews['sAbstract'],
                        'sUrl' => 'http://' . Util_Common::getConf('news', 'domain') . '/' . $aCity['sFullPinyin'] . '/news/detail/' . $aNews['iNewsID']
                    );
                }
            }
        }
        
        return $aRow;
    }
}
