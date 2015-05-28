<?php

class Controller_Lprank_Index extends Controller_Base
{

    public function newsAction ()
    {
        $sMonth = $this->getParam('sMonth');
        if (empty($sMonth)) {
            $sMonth = date('Ym');
        }
        if ($this->isPost()) {
            $aQNewsID = $this->getParam('aQNewsID');
            $aMNewsID = $this->getParam('aMNewsID');
            /*
             * if (count($aQNewsID) != 4) { return $this->showMsg('季度榜单资讯少填啦！', false); } if (count($aMNewsID) != 4) { return $this->showMsg('月度榜单资讯少填啦！', false); }
             */
//             foreach ($aMNewsID as $k => $iNewsID) {
//                 if (empty($iNewsID)) {
//                     unset($aMNewsID[$k]);
//                     continue;
//                 }
//                 $aDetail = Model_News::getDetail($iNewsID);
//                 if (empty($aDetail)) {
//                     return $this->showMsg('月度榜单资讯ID' . $iNewsID . '不存在！', false);
//                 }
//             }
//             foreach ($aQNewsID as $k => $iNewsID) {
//                 if (empty($iNewsID)) {
//                     unset($aQNewsID[$k]);
//                     continue;
//                 }
//                 $aDetail = Model_News::getDetail($iNewsID);
//                 if (empty($aDetail)) {
//                     return $this->showMsg('季度榜单资讯ID' . $iNewsID . '不存在！', false);
//                 }
//             }
            
            $aData = Model_Lprank::getByCity($this->iCurrCityID, $sMonth);
            $aRow = array(
                'sMonth' => $sMonth,
                'iCityID' => $this->iCurrCityID,
                'sQNewsID' => join(',', $aQNewsID),
                'sMNewsID' => join(',', $aMNewsID)
            );
            if (empty($aData)) {
                Model_Lprank::addData($aRow);
            } else {
                $aRow['iAutoID'] = $aData['iAutoID'];
                Model_Lprank::updData($aRow);
            }
            
            return $this->showMsg('榜单资讯设置成功', true);
        } else {
            $aData = Model_Lprank::getByCity($this->iCurrCityID, $sMonth);
            $this->assign('sMonth', $sMonth);
            $this->assign('aData', $aData);
        }
    }
}