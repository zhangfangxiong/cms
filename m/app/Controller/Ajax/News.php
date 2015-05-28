<?php

class Controller_Ajax_News extends Controller_Ajax_Base
{

    public function updShareAction() {
        $iNewsID = $this->getParam('iNewsID');
        $resuult = Sdk_Cms_News::updShare($iNewsID);
        echo $this->showMsg($resuult, true);
    }

    public function updGoodAction () {
        $iNewsID = intval($this->getParam('iNewsID'));
        $result =  Sdk_Cms_News::updGood($iNewsID);
        echo $this->showMsg($result, true);
    }

}