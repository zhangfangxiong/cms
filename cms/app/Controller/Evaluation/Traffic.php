<?php
/**
 * 交通出行控制器
 * author: cjj
 */
class Controller_Evaluation_Traffic extends Controller_Evaluation_Hxbase
{

    const ICATID = 5; // 当前章节

    const ISUBCATID = 13; // 默认子章节


    const CHAPTERMETRO = 14;
    const CHAPTERBUS  = 15;
    const TP_PATH = '/Evaluation/Traffic/busLineList.phtml';

    public function actionBefore() {
        parent::actionBefore();
        $this->config = Model_EvaluationZxBrand::getConfig();
    }
    /**
    * 自驾
    */
    public function indexAction()
    {
        $this->getID();
        $this->assignCommon();

        $config = Model_EvaluationHxWhole::getConfig();
        $images = Model_EvaluationImage::getImages($this->iEvaluationID, array(
            $config['hx']['sType']['zjxl'],
        ));

        $driving = Model_EvaluationDriving::getDrivingByEID($this->iEvaluationID);
        $this->assign('images', $images);
        // 其他图片
        $qtImage = Model_EvaluationImage::getImages($this->iEvaluationID, array(
            3199,
        ));
        $this->assign('qtImage', $qtImage);
        $this->assign('driving', $driving);
    }

    /**
     * 保存自驾信息
     */
    public function saveDrivingAction()
    {
        $driving = $this->_checkData();
        if (empty($driving)) return null;

        $imgs = $this->getParam('sDrivingImgTitle');

        if(!isset($imgs)) {
            return $this->showMsg('请选择自驾线路图', false);
        }

        $rs = Model_EvaluationDriving::saveData($driving);

        $config = Model_EvaluationHxWhole::getConfig();
        Model_EvaluationImage::addImagesAndDesc($driving['iEvaluationID'],
            $config['hx']['sType']['zjxl'],
            $this->getParam('sDrivingImg'),
            $this->getParam('sDrivingImgTitle'),
            $this->getParam('sDrivingImgDesc'),
            $this->getParam('sDrivingUpImgId'));
        // 添加其他图
        $this -> addQtImages(3199);
        Model_EvaluationImage::delImages($this->getParam('sHxImgDel'));

        if ($rs) {
//            $iEvaluationID = intval($driving['iEvaluationID']);
//            Model_Evaluation::updateChapter($iEvaluationID, self::CHAPTERDRIVING);//更新最后编辑章节
            return $this->showMsg('自驾出行数据保存成功', true);
        } else {
            return $this->showMsg('自驾出行数据保存失败', false);
        }
    }
    /**
     * 轨交出行
     *
     */
    public function  railAction()
    {
        $this->getID();
        $this->assignCommon();
        $aMetro = Model_EvaluationJtMetro::getEvaluationMetro($this->iEvaluationID);
        $this->assign('aMetro', $aMetro);
        $this->assign('iCityId', $this->iCurrCityID);
        $aMetroLine = Model_Metro::getAll(
            [
                'where' => [
                    'iCityID' => $this->iCurrCityID,
                    'iStatus' => 1
                ]
            ]
        );
        // 其他图片
        $qtImage = Model_EvaluationImage::getImages($this->iEvaluationID, array(
            3299,
        ));
        $this->assign('qtImage', $qtImage);
        $this->assign('aMetroLine', $aMetroLine);
    }

    /**
     * 轨交出行保存
     */
    public function saverailAction()
    {
        $data = $this->_checkData();
        if (empty($data)){
            return null;
        }

        $iHasMetro = $this->getParam('iHasMetro');
        $delJtMetroInfoID = [];
        $addJtMetroInfo = [];
        $updateJtMetroInfo = [];
        $aMetroInfoID = $this->getParam('metroInfoID');
        $aMetroID = $this->getParam('iMetroID');
        $aStationID = $this->getParam('iStationID');
        $aDistance = $this->getParam('iDistance');
        $aMorningPeak = $this->getParam('iMorningPeak');
        $aEveningPeak = $this->getParam('iEveningPeak');

        $iAutoID = 0;
        if (isset($data['iAutoID'])) {
            $iAutoID = intval($data['iAutoID']);
        }
        if ($iHasMetro == null) {
            //未选中表示有轨交
            $data['iHasMetro'] = 1;
            if (count($aMetroInfoID) == 0) {
                return $this->showMsg('您选择有轨交，请至少添加一条轨交', false);
            }

            if ($iAutoID > 0) {
                $aJtMetroInfoID = Model_EvaluationJtMetro::getMetroInfoID($iAutoID);
                if ($aJtMetroInfoID) {
                    foreach($aJtMetroInfoID as $value) {
                        if (!in_array($value, $aMetroInfoID)) {
                            array_push($delJtMetroInfoID, $value);
                        }
                    }
                }
            }
            foreach ($aMetroInfoID as $key => $value) {
                $tmpValue = [];
                $tmpValue['iMetroID'] = $aMetroID[$key];
                $tmpValue['iStationID'] = $aStationID[$key];
                $tmpValue['iDistance'] = intval($aDistance[$key]);
                $tmpValue['iMorningPeak'] = $aMorningPeak[$key];
                $tmpValue['iEveningPeak'] = $aEveningPeak[$key];

                if ($value > 0) {
                    //更新
                    $tmpValue['iAutoID'] = $value;
                    //array_push($updateJtMetroInfo, $tmpValue);
                    $updateJtMetroInfo[$key] = $tmpValue;
                } else {
                    //新增
                    //array_push($addJtMetroInfo, $tmpValue);
                    $addJtMetroInfo[$key] = $tmpValue;
                }
            }
        } else {
            //选中表示无轨交
            $data['iHasMetro'] = 0;
            if ($iAutoID > 0) {
                $delJtMetroInfoID = Model_EvaluationJtMetro::getMetroInfoID($iAutoID);
            }
        }
       //获取开关配置
        $evaluationSwitch =  Yaf_G::getConf('evaluationSwitch',null,'evaluation');
        //如果开关开启就执行插入和更新操作
            if ($iAutoID > 0) {
                //修改操作
                if($evaluationSwitch == 1) {
                    unset($data['sGoodTag']);
                    unset($data['sBadTag']);
                }
                Model_EvaluationJtMetro::updData($data);
            } else {
                if($evaluationSwitch == 0) {
                    $iAutoID = Model_EvaluationJtMetro::addData($data);
                }
        }

        Model_EvaluationJtMetro::delMetroInfoByPkID($delJtMetroInfoID);
        $IDArr = Model_EvaluationJtMetro::addMetroInfo($addJtMetroInfo, $iAutoID);
        $upIDArr = Model_EvaluationJtMetro::updateMetroInfo($updateJtMetroInfo);
        if (!empty($upIDArr)) {
            foreach ($upIDArr as $k => $v) {
                $IDArr[$k] = $v;
            }
        }
        // 插入图片
        $railImageTitle = $this -> getParam('railImageTitle');
        $railImageDesc = $this -> getParam('railImageDesc');
        $railImage = $this -> getParam('railImage');
        $railUpImageId = $this -> getParam('railUpImageId');
        if (!empty($railImageTitle)) {
            foreach($railImageTitle as $k => $item) {
                Model_EvaluationImage::addImagesAndDesc($data['iEvaluationID'],
                    514,
                    isset($railImage[$k])?$railImage[$k]:null,
                    $railImageTitle[$k],
                    $railImageDesc[$k],
                    isset($railUpImageId[$k])?$railUpImageId[$k]:null,
                    isset($IDArr[$k])?$IDArr[$k]:0
                );
            }
        }
        // 添加其他图
        $this -> addQtImages(3299);
        // 删除图片
        Model_EvaluationImage::delImages($this->getParam('sHxImgDel'));
        Model_Evaluation::updateChapter($data['iEvaluationID'], Model_EvaluationJtMetro::CHAPTER);
        return $this->showMsg('轨交出行数据保存成功', true);

    }
    /**
     * 公交出行
     *
     */
    public function busAction()
    {
        $this->getID();
        $this->assignCommon();

        $busInfo = Model_EvaluationBus::getBusByEID($this->iEvaluationID);
        $iCityId = $this->iCurrCityID;

        //获取公交线路信息
        $relatedBus = null;
        $relatedBus = Model_EvaluationBusInfo::getRelatedBusById($busInfo['iAutoID']);

        if (!empty($relatedBus)) {
            foreach ($relatedBus as $k => $item) {
                $sInfo = array();
                if (!empty($item['sInfo'])) {
                    $arr = json_decode($item['sInfo'],true);
                    foreach ($arr as $line) {
                        $sInfo[$line['busID']] = $line;
                    }
                }
                $list = Model_Bus::getStationsByName($item['sBusName'],$this->iCurrCityID);
                $html = $this->getView()->render(self::TP_PATH,array(
                    'lineList' => $list,
                    'sInfo' => $sInfo,
                ));
                $relatedBus[$k]['html'] = $html;
                $relatedBus[$k]['images'] = Model_EvaluationImage::getImagesByTargetId($item['iAutoId'],515,$this->iEvaluationID);
            }
        }

        // 其他图片
        $qtImage = Model_EvaluationImage::getImages($this->iEvaluationID, array(
            3399,
        ));
        $this->assign('qtImage', $qtImage);
        /*
        if(!empty($relatedBus)) {
            $len = sizeof($relatedBus);
            for($i = 0; $i < $len; $i ++) {
                $bus = Model_Bus::getDetail($relatedBus[$i]['iBusId']);
                $station = Model_BusStation::getDetail($relatedBus[$i]['iStationId']);

                $stations = Model_BusStation::getStationsByBid($relatedBus[$i]['iBusId']);

                $relatedBus[$i]['sBusName'] = $bus['sBusName'];
                $relatedBus[$i]['sStation'] = $station['sStation'];
                $relatedBus[$i]['stations'] = $stations;
            }
        }
        */
        $this->assign('iCityId', $iCityId);
        $this->assign('iEvaluationID', $this->iEvaluationID);
        $this->assign('busInfo', $busInfo);
        $this->assign('relatedBus', $relatedBus);
    }

    protected function getCatId()
    {
        return self::ICATID;
    }



    protected function getSubCatId()
    {
        return self::ISUBCATID;
    }

    /*
     * 数据检测 自驾出行表单
     */
    private function _checkData()
    {
        $driving = array();
        $iAutoID = intval($this->getParam('iAutoID'));
        if(!empty($iAutoID)){
            $driving['iAutoID'] = $iAutoID;
        }
        $driving['iEvaluationID'] = intval($this->getParam('iEvaluationID'));
        $driving['sComment'] = $this->getParam('sComment');
        $driving['sGoodTag'] = $this->getParam('sGoodTag');
        if(!empty($driving['sGoodTag'])) {
            $driving['sGoodTag'] = implode(',', $driving['sGoodTag']);
        }else {
            $driving['sGoodTag'] = '';
        }
        $driving['sBadTag'] = $this->getParam('sBadTag');
        if(!empty($driving['sBadTag'])) {
            $driving['sBadTag'] = implode(',', $driving['sBadTag']);
        }else {
            $driving['sBadTag'] = '';
        }
        $driving['sOtherComment'] = $this->getParam('sOtherComment');
        $driving['iCreateTime'] = $this->getParam('iCreateTime');
        if(empty($driving['iCreateTime'])) {
            $driving['iCreateTime'] = time();
        }

        $driving['iUpdateTime'] = time();
        $driving['iStatus'] = 1;

        if (empty($driving['iEvaluationID'])) {
            return $this->showMsg('请选择评测报告！', false);
        }
        if (empty($driving['sComment'])) {
            return $this->showMsg('请输入分析师点评！', false);
        }
        return $driving;
    }

    /**
     * 获取公交线路
     */
    public function getBusLinesAction()
    {
        $stationName = $this->getParam('busStationName');
        $list = Model_Bus::getStationsByName($stationName,$this->iCurrCityID);
        return $this->showMsg($this->getView()->render(self::TP_PATH,array(
            'lineList' => $list
        )), true);
    }

    public function getStationsAction()
    {
        $busStationName = $this->getParam('sKey');
        $iCityID = $this->getParam('iCityID');
        $aData = Model_BusStation::getStationsByName($busStationName,$iCityID);
        $this->showMsg($aData, true);
    }
    /**
     * 保存公交出行
     */
    public function saveBusAction()
    {
        //删除相应的公交线路信息
        $delBusInfo = $this->getParam('delBusInfoID');

        if(!empty($delBusInfo)) {
            foreach($delBusInfo as $id) {
                Model_EvaluationBusInfo::delData(intval($id));
            }
        }

        $busInfo = $this->_checkData();
        if (empty($busInfo)) return null;

        $rs = Model_EvaluationBus::saveData($busInfo);
        $iJtBusId = empty($busInfo['iAutoID']) ? $rs : $busInfo['iAutoID'];

        /*
        $busInfoID = $this->getParam('busInfoID');
        $iBusId = $this->getParam('iBusId');
        $iStationId = $this->getParam('iStationId');
        $distance = $this->getParam('iDistance');
        $zaogaofeng = $this->getParam('iZaogaofeng');
        $wangaofeng = $this->getParam('iWangaofeng');

        if (empty($iBusId)) {
            return $this->showMsg('请输入公交线路！', false);
        }

        $len = sizeof($busInfoID);
        foreach($busInfoID as $key => $val) {
            $row = array();
            $row['iBusId'] = $iBusId[$key];
            $row['iStationId'] = $iStationId[$key];
            $row['iZaogaofeng'] = empty($zaogaofeng[$key]) ? 1 : $zaogaofeng[$key][0];
            $row['iWangaofeng'] = empty($wangaofeng[$key]) ? 1 : $wangaofeng[$key][0];
            $row['iDistance'] = empty($distance[$key]) ? 0 : $distance[$key];
            $row['iJtBusId'] = $iJtBusId;

            if(!empty($busInfoID[$key])) {
                $row['iAutoId'] = $busInfoID[$key];
                $row['iUpdateTime'] = time();
                Model_EvaluationBusInfo::updData($row);
            }else{
                $row['iCreateTime'] = time();
                Model_EvaluationBusInfo::addData($row);
            }
        }*/
        $busStationName = $this->getParam('busStationName'); // 站点名
        $distance = $this -> getParam('iDistance');
        $zaogaofeng = $this->getParam('iZaogaofeng');
        $wangaofeng = $this->getParam('iWangaofeng');
        $iBusId  = $this->getParam('iBusId'); // 公交线路表 主键
        $iBusLineName  = $this->getParam('iBusLineName'); //  公交线路名称
        $busInfoID = $this->getParam('busInfoID'); // jt_bus_info 主键
        $busImageTitle = $this -> getParam('busImageTitle');
        $busImageDesc = $this -> getParam('busImageDesc');
        $busImage = $this -> getParam('busImage');
        $busUpImageId = $this -> getParam('busUpImageId');
        foreach ($busStationName as $key => $v) {
            if (empty($v)) {
                return;
            }
            $row = array();
            $row['sBusName'] = $v;
            $row['iDistance'] = isset($distance[$key]) ? intval($distance[$key]) : 0;
            $row['iJtBusId'] = $iJtBusId;
            // 线路信息
            $info = array();
            if (!empty($iBusId[$key])) {
                foreach ($iBusId[$key] as $ck => $cv) {
                    $info[$ck]['busID'] = $cv;
                    $info[$ck]['lineName'] = $iBusLineName[$key][$ck];
                    $info[$ck]['zaogaofeng'] = isset($zaogaofeng[$key][$ck]) ? $zaogaofeng[$key][$ck] :1;
                    $info[$ck]['wangaofeng'] = isset($wangaofeng[$key][$ck]) ? $wangaofeng[$key][$ck] :1;
                }
            }
            if (!empty($info)) {
                $row['sInfo'] = json_encode($info);
            } else {
                $row['sInfo'] = '';
            }
            if(!empty($busInfoID[$key])) {
                $jtBusInfoID = $row['iAutoId'] = $busInfoID[$key];
                Model_EvaluationBusInfo::updData($row);
            }else{
                $jtBusInfoID = Model_EvaluationBusInfo::addData($row);
            }
            // 添加图片
            if (!empty($busImageTitle[$key])) {
                Model_EvaluationImage::addImagesAndDesc($busInfo['iEvaluationID'],
                    515,
                    isset($busImage[$key])?$busImage[$key]:null,
                    $busImageTitle[$key],
                    $busImageDesc[$key],
                    isset($busUpImageId[$key])?$busUpImageId[$key]:null,
                    $jtBusInfoID
                );
            }
        }
        $this -> addQtImages(3399);
        Model_EvaluationImage::delImages($this->getParam('sHxImgDel'));
///////////后台验证事务////////////////
        if ($rs) {
            $iEvaluationID = intval($busInfo['iEvaluationID']);
            Model_Evaluation::updateChapter($iEvaluationID, self::CHAPTERBUS);//更新最后编辑章节
            return $this->showMsg('公交出行数据保存成功', true);
        } else {
            return $this->showMsg('公交出行数据保存失败', false);
        }
    }


}