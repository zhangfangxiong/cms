<?php

/**
 * Created by PhpStorm.
 * User: len
 * Date: 2015/4/10
 * Time: 11:33
 */
class Controller_H5_Evaluation extends Controller_Base
{
    private $_aMenuData = array();
    private $_sCurrParent = '';
    private $_iCurrParentID = 0;
    private $_sCurrSon = '';
    private $_iCurrSonID = 0;
    private $_aControl = array();
    private $_aAction = array();
    private $_iID = 0;
    private $sUrlPrifix = '';

    private $_zxPp = [
        'cf' => [
            'subcat' => [
                'tm' => [
                    'pp' => 'sKitchenTmPp',
                    'cz' => 'sKitchenTmCz',
                    'img' => 'cftmImage',
                    'text' => '台面'
                ],
                'cg' => [
                    'pp' => 'sKitchenCgPp',
                    'cz' => 'sKitchenCgCz',
                    'img' => 'cfcgImage',
                    'text' => '橱柜'
                ],
                'sc' => [
                    'pp' => 'sKitchenScPp',
                    'cz' => 'sKitchenScCz',
                    'img' => 'cfscImage',
                    'text' => '水槽'
                ],
                'slt' => [
                    'pp' => 'sKitchenLtPp',
                    'cz' => '',
                    'img' => 'cfltImage',
                    'text' => '水龙头'
                ],
                'cyyj' => [
                    'pp' => 'sKitchenCyyjPp',
                    'cz' => '',
                    'img' => 'cfyjImage',
                    'text' => '抽油烟机'
                ],
                'rqz' => [
                    'pp' => 'sKitchenRqzPp',
                    'cz' => '',
                    'img' => 'cfrqImage',
                    'text' => '燃气灶'
                ],
                'xdg' => [
                    'pp' => 'sKitchenXdgPp',
                    'cz' => '',
                    'img' => 'cfxdImage',
                    'text' => '消毒柜'
                ],
                'kx' => [
                    'pp' => 'sKitchenKxPp',
                    'cz' => '',
                    'img' => 'cfkxImage',
                    'text' => '烤箱'
                ],
                'zl' => [
                    'pp' => 'sKitchenZrPp',
                    'cz' => '',
                    'img' => 'cfzlImage',
                    'text' => '蒸炉'
                ],
                'bx' => [
                    'pp' => 'sKitchenDbxPp',
                    'cz' => '',
                    'img' => 'cfbxImage',
                    'text' => '冰箱'
                ],
                'qz' => [
                    'pp' => 'sKitchenQzPp',
                    'cz' => 'sKitchenQzCz',
                    'img' => 'cfqzImage',
                    'text' => '墙砖'
                ],
                'dz' => [
                    'pp' => 'sKitchenDzPp',
                    'cz' => 'sKitchenDzCz',
                    'img' => 'cfdzImage',
                    'text' => '地砖'
                ],
            ],
            'text' => '厨房'
        ],

        'wy' => [
            'subcat' => [
                'zbq' => [
                    'pp' => 'sToiletZpqPp',
                    'cz' => '',
                    'img' => 'wybqImage',
                    'text' => '坐便器'
                ],
                'tp' => [
                    'pp' => 'sToiletTpPp',
                    'cz' => '',
                    'img' => 'wytpImage',
                    'text' => '台盆'
                ],
                'yg' => [
                    'pp' => 'sToiletYgPp',
                    'cz' => '',
                    'img' => 'wyygImage',
                    'text' => '浴缸'
                ],
                'lt' => [
                    'pp' => 'sToiletLtPp',
                    'cz' => '',
                    'img' => 'wyltImage',
                    'text' => '龙头'
                ],
                'qm' => [
                    'pp' => 'sToiletQmPp',
                    'cz' => 'sToiletQmCz',
                    'img' => 'wyqmImage',
                    'text' => '墙面'
                ],
                'dz' => [
                    'pp' => 'sToiletDzPp',
                    'cz' => 'sToiletDzCz',
                    'img' => 'wydzImage',
                    'text' => '地砖'
                ],
                'yb' => [
                    'pp' => 'sToiletYbPp',
                    'cz' => 'sToiletYbLx',
                    'img' => 'wyybImage',
                    'text' => '浴霸'
                ],
                'gj' => [
                    'pp' => 'sToiletGjPp',
                    'cz' => 'sToiletGjCz',
                    'img' => 'wyzhImage',
                    'text' => '卫浴柜镜柜组合'
                ],
            ],
            'text' => '卫浴'
        ],

        'ws' => [
            'subcat' => [
                'm' => [
                    'pp' => 'sRoomMPp',
                    'cz' => 'sRoomMCz',
                    'img' => 'wsmImage',
                    'text' => '门'
                ],
                'db' => [
                    'pp' => 'sRoomDbPp',
                    'cz' => 'sRoomDbCz',
                    'img' => 'wsdbImage',
                    'text' => '地板'
                ],
                'qm' => [
                    'pp' => 'sRoomQmPp',
                    'cz' => 'sRoomQmCz',
                    'img' => 'wsqmImage',
                    'text' => '墙面'
                ],
                'dd' => [
                    'pp' => 'sRoomDdPp',
                    'cz' => 'sRoomDdCz',
                    'img' => 'wsddImage',
                    'text' => '吊灯'
                ],
            ],
            'text' => '卧室'
        ],

        'qw' => [
            'subcat' => [
                'xf' => [
                    'pp' => 'sHouseXfPp',
                    'cz' => '',
                    'img' => 'qwxfImage',
                    'text' => '新风系统'
                ],
                'kt' => [
                    'pp' => 'sHouseKtPp',
                    'cz' => '',
                    'img' => 'qwktImage',
                    'text' => '空调'
                ],
                'dn' => [
                    'pp' => 'sHouseDnPp',
                    'cz' => '',
                    'img' => 'qwdnImage',
                    'text' => '地暖'
                ],
                'hw' => [
                    'pp' => 'sHouseHwPp',
                    'cz' => '',
                    'img' => 'qwhwImage',
                    'text' => '红外布防系统'
                ],
            ],
            'text' => '全屋'
        ],

        'ybgc' => [
            'subcat' => [
                'jsg' => [
                    'pp' => 'sHouseGsgPp',
                    'cz' => 'sHouseGsgCz',
                    'img' => 'qwhwImage',
                    'text' => '给水管'
                ],
                'qld' => [
                    'pp' => 'sHouseQldPp',
                    'cz' => 'sHouseQldCz',
                    'img' => 'wsdbImage',
                    'text' => '强弱电'
                ],
                'gq' => [
                    'pp' => 'iHouseGq',
                    'cz' => '',
                    'img' => '',
                    'text' => '光纤'
                ]
            ],
            'text' => '隐蔽工程'
        ],
    ];
    /**
     * 执行Action前执行
     * @see Yaf_Controller::actionBefore()
     */
    public function actionBefore()
    {
        parent::actionBefore();
        $this->_iID = intval($this->getParam('eID'));// : 7329;
        $aMenus = Sdk_Cms_Evaluation::getMenus(array('eID'=>$this->_iID));

        if (!$aMenus['status']) {
            return false;//评测ID有误
        }
        $sUnitName = Sdk_Cms_Evaluation::evaluationUnitName($this->_iID);
        $this->_aMenuData = $aMenus;
        $this->_setCurrControl($aMenus['data']['controller']);
        $this->_aAction = $aMenus['data']['action'];
        $sUrlPrifix = 'http://' . Yaf_G::getConf('touchweb', 'domain') . '/h5/Evaluation/';
        $this->sUrlPrifix = $sUrlPrifix;
        $aParentTo = $this->_setParentToSonUrl();
        $aFirstActionName = $this->_getFirstActionName();
        $this->assign('sUrlPrifix', $sUrlPrifix);//URL前缀
        $this->assign('sParentName', $aMenus['data']['parent']);//父类名称
        $this->assign('sParentUrl', $aMenus['data']['controller']);//父类URL
        $this->assign('iParentIfNull', $aMenus['data']['parentIfNull']);//父类是否为空
        $this->assign('iSonIfNull', $aMenus['data']['sonIfNull']);//子类是否为空
        $this->assign('eID',$this->_iID);//评测报告ID
        $this->assign('sUnitName',$sUnitName['data']);//评测报告ID
        $this->assign('aParentTo',$aParentTo);//父menuURL指向
        $this->assign('aFirstActionName',$aFirstActionName);//第一个有效actionname
        $this->aMeta['title'] .= $sUnitName['data'].' 评测报告';

        $this->setFrame('h5frame.phtml');
    }



    /**
     * 执行Action后的操作
     * @see Yaf_Controller::actionAfter()
     */
    public function actionAfter()
    {
        parent::actionAfter();
        $this->assign('sCurrParent', $this->_sCurrParent);//当前父menu

        // $this->assign('aSonMenus', $this->_aAction[$this->_iCurrParentID]);
        // $this->assign('aSonMenusName', $this->_aMenuData['data']['child'][$this->_iCurrParentID]);

        $this->_iCurrParentID
        ? $this->assign('aSonMenus', $this->_aAction[$this->_iCurrParentID])//当前子menu列表
        : $this->assign('aSonMenus', '');

        $this->_iCurrParentID
        ? $this->assign('aSonMenusName', $this->_aMenuData['data']['child'][$this->_iCurrParentID])//当前子menu名称列表
        : $this->assign('aSonMenusName', '');

        $this->assign('sCurrSon', $this->_sCurrSon);//当前子menu
        $this->assign('aControl',$this->_aControl);
        $array = array('Public','Config','Parking','Business');
        if ( in_array($this->_sCurrSon,$array)) {
            $this->assign('addLeft', 1);//当前子menu添加向左属性
        }
        $analysts = Sdk_Cms_Evaluation::analysts(array('eID'=>$this->_iID));
        $this->assign('analysts', $analysts);//分析师
    }

    /**
     * 首页地址
     */
    public function indexAction()
    {
        $aFirstActionName = $this->_getFirstActionName();
        $sActionName = $aFirstActionName['sToControl'].$aFirstActionName['sToAction'];
        header('location:'.$this->sUrlPrifix.$sActionName.'?eID='.$this->_iID);
    }

    /**
     * 户型分析-整体评价
     * @return
     */
    public function hxanalyseIndexAction()
    {
        $sCurrAction = __FUNCTION__;
        $this->_setCurrMenu($sCurrAction);
        $aParm = array('eID' => $this->_iID, 'pChapterID' => $this->_iCurrParentID, 'cChapterID' => $this->_iCurrSonID);
        //以上要每个action都要有,根据action判断当前的父子menu
        $aHxfx = Sdk_Cms_Evaluation::evaluationhxanalyse($aParm);
        if (!is_array($aHxfx['data'])) {
            return false;//暂无数据
        }
        $aNumToWord = array('零', '一', '二', '三', '四', '五', '六', '七', '八', '九', '十');
        $this->assign('aHxfx', $aHxfx);
        $this->assign('aNumToWord', $aNumToWord);
    }

    /**
     * 户型分析-户型分析
     * @return
     */
    public function hxanalyseHxAction()
    {
        $sCurrAction = __FUNCTION__;
        $this->_setCurrMenu($sCurrAction);
        $aParm = array('eID' => $this->_iID, 'pChapterID' => $this->_iCurrParentID, 'cChapterID' => $this->_iCurrSonID);
        //以上要每个action都要有,根据action判断当前的父子menu

        $aHxfx = Sdk_Cms_Evaluation::evaluationhxanalysehx($aParm);
        if (!is_array($aHxfx['data'])) {
            return false;//暂无数据
        }
        $this->assign('aHxfx', $aHxfx);
    }


    /**
     * 装修标准-品牌配置
     * @return
     */
    public function zxstandardIndexAction()
    {
        $sCurrAction = __FUNCTION__;
        $this->_setCurrMenu($sCurrAction);
        $aParm = array('eID' => $this->_iID, 'pChapterID' => $this->_iCurrParentID, 'cChapterID' => $this->_iCurrSonID);
        //以上要每个action都要有,根据action判断当前的父子menu
        $aHxfx = Sdk_Cms_Evaluation::zxstandardIndex($aParm);
        if (!is_array($aHxfx['data'])) {
            return false;//暂无数据
        }

        //$aHxfx['data']['sToiletYbLx'] = (1==$aHxfx['data']['sToiletYbLx']) ? '灯暖' : '风暖';
        //$aHxfx['data']['iHouseGq'] = (1==$aHxfx['data']['iHouseGq']) ? '光纤到楼' : '光纤到户';

        $tmpZxpp = [];
        foreach($this->_zxPp as $key => $value) {
            $iShow = false;
            foreach($value['subcat'] as $k => $v) {
                $iVShow = false;
                $tmpZxpp[$key]['subcat'][$k]['pp'] = '-';
                $tmpZxpp[$key]['subcat'][$k]['cz'] = '-';
                $tmpZxpp[$key]['subcat'][$k]['img'] = [];
                if (!empty($aHxfx['data'][$v['pp']])) {
                    $iVShow = true;
                    $iShow = true;
                    $tmpZxpp[$key]['subcat'][$k]['pp'] = $aHxfx['data'][$v['pp']];

                }

                if (!empty($aHxfx['data'][$v['cz']])) {
                    $iVShow = true;
                    $iShow = true;
                    $tmpZxpp[$key]['subcat'][$k]['cz'] = $aHxfx['data'][$v['cz']];
                }

                if (!empty($aHxfx['data'][$v['img']])) {
                    $iVShow = true;
                    $iShow = true;
                    $tmpZxpp[$key]['subcat'][$k]['img'] = $aHxfx['data'][$v['img']];
                }

                $tmpZxpp[$key]['subcat'][$k]['iShow'] = $iVShow;
                $tmpZxpp[$key]['subcat'][$k]['text'] = $v['text'];
            }

            $tmpZxpp[$key]['iShow'] = $iShow;
            $tmpZxpp[$key]['text'] = $value['text'];
        }

        // echo "<pre>";        var_dump($tmpZxpp);        exit;
        $this->assign('aHxfx', $aHxfx);
        $this->assign('aPppz', $tmpZxpp);
    }

    /**
     * 装修标准-装修分析
     * @return
     */
    public function zxstandardAnalysisAction()
    {
        $sCurrAction = __FUNCTION__;
        $this->_setCurrMenu($sCurrAction);
        $aParm = array('eID' => $this->_iID, 'pChapterID' => $this->_iCurrParentID, 'cChapterID' => $this->_iCurrSonID);
        //以上要每个action都要有,根据action判断当前的父子menu

        $aHxfx = Sdk_Cms_Evaluation::getAnalysis($aParm);
        if (!is_array($aHxfx['data'])) {
            return false;//暂无数据
        }
        $this->assign('aHxfx', $aHxfx);
    }


    /**
     * 社区品质-整体规划
     * @return
     */
    public function sqpzIndexAction()
    {
        $sCurrAction = __FUNCTION__;
        $this->_setCurrMenu($sCurrAction);
        $aParm = array('eID' => $this->_iID, 'pChapterID' => $this->_iCurrParentID, 'cChapterID' => $this->_iCurrSonID);
        //以上要每个action都要有,根据action判断当前的父子menu

        $aHxfx = Sdk_Cms_Evaluation::sqpzIndex($aParm);
        if (!is_array($aHxfx['data'])) {
            return false;//暂无数据
        }
        $this->assign('aHxfx', $aHxfx);
    }

    /**
     * 社区品质-社区景观
     * @return
     */
    public function sqpzSceneryAction()
    {
        $sCurrAction = __FUNCTION__;
        $this->_setCurrMenu($sCurrAction);
        $aParm = array('eID' => $this->_iID, 'pChapterID' => $this->_iCurrParentID, 'cChapterID' => $this->_iCurrSonID);
        //以上要每个action都要有,根据action判断当前的父子menu

        $aHxfx = Sdk_Cms_Evaluation::sqpzScenery($aParm);
        if (!is_array($aHxfx['data'])) {
            return false;//暂无数据
        }
        $this->assign('aHxfx', $aHxfx);
    }

    /**
     * 社区品质-建筑立面
     * @return
     */
    public function sqpzBuildAction()
    {
        $sCurrAction = __FUNCTION__;
        $this->_setCurrMenu($sCurrAction);
        $aParm = array('eID' => $this->_iID, 'pChapterID' => $this->_iCurrParentID, 'cChapterID' => $this->_iCurrSonID);
        //以上要每个action都要有,根据action判断当前的父子menu

        $aHxfx = Sdk_Cms_Evaluation::sqpzBuild($aParm);
        if (!is_array($aHxfx['data'])) {
            return false;//暂无数据
        }
        $this->assign('aHxfx', $aHxfx);
    }

    /**
     * 社区品质-公共部位
     * @return
     */
    public function sqpzPublicAction()
    {
        $sCurrAction = __FUNCTION__;
        $this->_setCurrMenu($sCurrAction);
        $aParm = array('eID' => $this->_iID, 'pChapterID' => $this->_iCurrParentID, 'cChapterID' => $this->_iCurrSonID);
        //以上要每个action都要有,根据action判断当前的父子menu

        $aHxfx = Sdk_Cms_Evaluation::sqpzPublic($aParm);
        if (!is_array($aHxfx['data'])) {
            return false;//暂无数据
        }
        $this->assign('aHxfx', $aHxfx);
    }

    /**
     * 社区品质-社区配套
     * @return
     */
    public function sqpzConfigAction()
    {
        $sCurrAction = __FUNCTION__;
        $this->_setCurrMenu($sCurrAction);
        $aParm = array('eID' => $this->_iID, 'pChapterID' => $this->_iCurrParentID, 'cChapterID' => $this->_iCurrSonID);
        //以上要每个action都要有,根据action判断当前的父子menu

        $aHxfx = Sdk_Cms_Evaluation::sqpzConfig($aParm);
        if (!is_array($aHxfx['data'])) {
            return false;//暂无数据
        }
        $this->assign('aHxfx', $aHxfx);
    }

    /**
     * 社区品质-车位情况
     * @return
     */
    public function sqpzParkingAction()
    {
        $sCurrAction = __FUNCTION__;
        $this->_setCurrMenu($sCurrAction);
        $aParm = array('eID' => $this->_iID, 'pChapterID' => $this->_iCurrParentID, 'cChapterID' => $this->_iCurrSonID);
        //以上要每个action都要有,根据action判断当前的父子menu

        $aHxfx = Sdk_Cms_Evaluation::sqpzParking($aParm);
        if (!is_array($aHxfx['data'])) {
            return false;//暂无数据
        }
        $this->assign('aHxfx', $aHxfx);
    }


    /**
     * 物业服务-物业费用
     * @return
     */
    public function wyfwIndexAction()
    {
        $sCurrAction = __FUNCTION__;
        $this->_setCurrMenu($sCurrAction);
        $aParm = array('eID' => $this->_iID, 'pChapterID' => $this->_iCurrParentID, 'cChapterID' => $this->_iCurrSonID);
        //以上要每个action都要有,根据action判断当前的父子menu

        $aHxfx = Sdk_Cms_Evaluation::wyfwIndex($aParm);
        if (!is_array($aHxfx['data'])) {
            return false;//暂无数据
        }
        $this->assign('aHxfx', $aHxfx);
    }

    /**
     * 物业服务-物业服务
     * @return
     */
    public function wyfwServiceAction()
    {
        $sCurrAction = __FUNCTION__;
        $this->_setCurrMenu($sCurrAction);
        $aParm = array('eID' => $this->_iID, 'pChapterID' => $this->_iCurrParentID, 'cChapterID' => $this->_iCurrSonID);
        //以上要每个action都要有,根据action判断当前的父子menu

        $aHxfx = Sdk_Cms_Evaluation::wyfwService($aParm);
        if (!is_array($aHxfx['data'])) {
            return false;//暂无数据
        }
        $this->assign('aHxfx', $aHxfx);
    }


    /**
     * 交通出行-自驾出行
     * @return
     */
    public function trafficIndexAction()
    {
        $sCurrAction = __FUNCTION__;
        $this->_setCurrMenu($sCurrAction);
        $aParam = array('eID'=>$this->_iID, 'pChapterID'=>$this->_iCurrParentID, 'cChapterID'=>$this->_iCurrSonID);

        //以上要每个action都要有,根据action判断当前的父子menu
        $aTraFree = Sdk_Cms_Evaluation::evaluationtrafficindex($aParam);
        if (!is_array($aTraFree['data'])) {
            return false;//暂无数据
        }
        $this->assign('aTraFree', $aTraFree);
    }

    /**
     * 交通出行-轨交出行
     * @return
     */
    public function trafficRailAction()
    {
        $sCurrAction = __FUNCTION__;
        $this->_setCurrMenu($sCurrAction);
        $aParam = array('eID'=>$this->_iID, 'pChapterID'=>$this->_iCurrParentID, 'cChapterID'=>$this->_iCurrSonID);

        //以上要每个action都要有,根据action判断当前的父子menu
        $aTraRail = Sdk_Cms_Evaluation::evaluationtrafficrail($aParam);
        // print_r($aTraRail);die;
        if (!is_array($aTraRail['data'])) {
            return false;//暂无数据
        }
        $this->assign('aTraRail', $aTraRail);
    }


    /**
     * 交通出行-公交
     * @return
     */
    public function trafficBusAction()
    {
        $sCurrAction = __FUNCTION__;
        $this->_setCurrMenu($sCurrAction);
        $aParam = array('eID'=>$this->_iID, 'pChapterID'=>$this->_iCurrParentID, 'cChapterID'=>$this->_iCurrSonID);

        //以上要每个action都要有,根据action判断当前的父子menu
        $aTraBus = Sdk_Cms_Evaluation::evaluationtrafficbus($aParam);
        if (!is_array($aTraBus['data'])) {
            return false;//暂无数据
        }
        $this->assign('aTraBus', $aTraBus);
    }


    /**
     * 社区配套-区域简介
     * @return
     */
    public function regionIndexAction()
    {
        $sCurrAction = __FUNCTION__;
        $this->_setCurrMenu($sCurrAction);
        $aParam = array('eID'=>$this->_iID, 'pChapterID'=>$this->_iCurrParentID, 'cChapterID'=>$this->_iCurrSonID);

        //以上要每个action都要有,根据action判断当前的父子menu
        $aRegion = Sdk_Cms_Evaluation::evaluationregionindex($aParam);
        if (!is_array($aRegion['data'])) {
            return false;//暂无数据
        }
        $this->assign('aRegion', $aRegion);
    }

    /**
     * 社区配套-教育资源
     * @return
     */
    public function regionEducateAction()
    {
        $sCurrAction = __FUNCTION__;
        $this->_setCurrMenu($sCurrAction);
        $aParam = array('eID'=>$this->_iID, 'pChapterID'=>$this->_iCurrParentID, 'cChapterID'=>$this->_iCurrSonID);

        //以上要每个action都要有,根据action判断当前的父子menu
        $aEducate = Sdk_Cms_Evaluation::evaluationregioneducate($aParam);
        if (!is_array($aEducate['data'])) {
            return false;//暂无数据
        }
        $this->assign('aEducate', $aEducate);
    }

    /**
     * 社区配套-医疗资源
     * @return
     */
    public function regionMedicalAction()
    {
        $sCurrAction = __FUNCTION__;
        $this->_setCurrMenu($sCurrAction);
        $aParam = array('eID'=>$this->_iID, 'pChapterID'=>$this->_iCurrParentID, 'cChapterID'=>$this->_iCurrSonID);

        //以上要每个action都要有,根据action判断当前的父子menu
        $aMedical = Sdk_Cms_Evaluation::evaluationregionmedical($aParam);
        if (!is_array($aMedical['data'])) {
            return false;//暂无数据
        }
        $this->assign('aMedical', $aMedical);
    }

    /**
     * 社区配套-周边商圈
     * @return
     */
    public function regionBusinessAction()
    {
        $sCurrAction = __FUNCTION__;
        $this->_setCurrMenu($sCurrAction);
        $aParam = array('eID'=>$this->_iID, 'pChapterID'=>$this->_iCurrParentID, 'cChapterID'=>$this->_iCurrSonID);

        //以上要每个action都要有,根据action判断当前的父子menu
        $aBusiness = Sdk_Cms_Evaluation::evaluationregionbusiness($aParam);
        if (!is_array($aBusiness['data'])) {
            return false;//暂无数据
        }
        $this->assign('aBusiness', $aBusiness);
    }

    /**
     * 社区配套-公共资源
     * @return
     */
    public function regionPublicAction()
    {
        $sCurrAction = __FUNCTION__;
        $this->_setCurrMenu($sCurrAction);
        $aParam = array('eID'=>$this->_iID, 'pChapterID'=>$this->_iCurrParentID, 'cChapterID'=>$this->_iCurrSonID);

        //以上要每个action都要有,根据action判断当前的父子menu
        $aPublic = Sdk_Cms_Evaluation::evaluationregionpublic($aParam);
        if (!is_array($aPublic['data'])) {
            return false;//暂无数据
        }
        $this->assign('aPublic', $aPublic);
    }

    /**
     * 不利因素-内部不利因素
     * @return
     */
    public function badfactorIndexAction()
    {
        $sCurrAction = __FUNCTION__;
        $this->_setCurrMenu($sCurrAction);
        $aParam = array('eID'=>$this->_iID, 'pChapterID'=>$this->_iCurrParentID, 'cChapterID'=>$this->_iCurrSonID);

        //以上要每个action都要有,根据action判断当前的父子menu
        $aBad = Sdk_Cms_Evaluation::evaluationbadfactor($aParam);
        if (!is_array($aBad['data'])) {
            return false;//暂无数据
        }
        $this->assign('aBad', $aBad);
    }

    /**
     * 不利因素-外部不利因素
     * @return
     */
    public function badfactorOutsideAction()
    {
        $sCurrAction = __FUNCTION__;
        $this->_setCurrMenu($sCurrAction);
        $aParam = array('eID'=>$this->_iID, 'pChapterID'=>$this->_iCurrParentID, 'cChapterID'=>$this->_iCurrSonID);

        //以上要每个action都要有,根据action判断当前的父子menu
        $aOut = Sdk_Cms_Evaluation::evaluationoutside($aParam);
        if (!is_array($aOut['data'])) {
            return false;//暂无数据
        }
        $this->assign('aOut', $aOut);
    }

    /**
     * 评测相册
     */
    public function photoAction () {
        $sCurrAction = __FUNCTION__;
        $this->_setCurrMenu($sCurrAction);
        $aParam = array('eID'=>$this->_iID, 'pChapterID'=>$this->_iCurrParentID, 'cChapterID'=>$this->_iCurrSonID);

        //以上要每个action都要有,根据action判断当前的父子menu
        $aEvalImage = Sdk_Cms_Evaluation::evaluationImage($aParam);

        if (!is_array($aEvalImage['data'])) {
            return false;//暂无数据
        }

        $iImageId = $this->getParam('iImageId') ? $this->getParam('iImageId') : 0;
        $iSelect = 0;
        foreach ($aEvalImage['data'] as $key => $value) {
            if($iImageId == $value['iAutoID']) {
                break;
            }
            $iSelect++;
        }

        $this->assign('aEvalImage', $aEvalImage['data']);
        $this->assign('iSelect', $iSelect);
        $this->assign('isPhoto', 1);
    }

    /**
     * 判断父menu指向哪个子menu
     */
    private function _setParentToSonUrl()
    {
        $aMenuData = $this->_aMenuData;
        $aChildBaseInfo = $aMenuData['data']['child'];
        $aChildHasInfo = $aMenuData['data']['sonIfNull'];
        $aChildActionInfo = $aMenuData['data']['action'];
        $data = array();//存父menu有数据的第一个
        foreach ($aChildBaseInfo as $key => $value ) {
            foreach ($value as $k => $v) {
                if (isset($aChildHasInfo[$k]) && $aChildHasInfo[$k]) {
                    $data[$key]['id'] = $k;
                    $data[$key]['name'] = $aChildActionInfo[$key][$k];
                    break;
                }
            }
        }
        return $data;
    }

    /**
     * 设置当前menu
     * @param $sCurrAction
     */
    private function _setCurrMenu($sCurrAction)
    {
        $sCurrActionName = substr($sCurrAction, 0, strlen($sCurrAction) - 6);
        $iLength = strlen($sCurrActionName);
        for ($i = 0; $i < $iLength - 1; $i++) {
            $iTem = ord($sCurrActionName[$i]);
            if ($iTem > 64 && $iTem < 91) {
                $this->_sCurrParent = substr($sCurrActionName, 0, $i);
                $this->_sCurrSon = substr($sCurrActionName, $i, $iLength);
                $aControl = array_flip($this->_aControl);
                if (isset($aControl[$this->_sCurrParent])) {
                    $this->_iCurrParentID = $aControl[$this->_sCurrParent];
                    $aAction = $this->_aAction;
                    $aCurrSonMenu = $aAction[$this->_iCurrParentID];
                    foreach ($aCurrSonMenu as $key => $value) {
                        if (strtolower($this->_sCurrSon) == strtolower($value)) {
                            $this->_iCurrSonID = $key;
                            break;
                        }
                    }
                }
                break;
            }
        }
    }

    /**
     * @param $aArray
     */
    private function _setCurrControl($aArray)
    {
        foreach ($aArray as $key => $value) {
            $aArray[$key] = strtolower($value);
        }
        $this->_aControl = $aArray;
    }

    private function _getFirstActionName()
    {
        $aParentTo = $this->_setParentToSonUrl();
        foreach ($aParentTo as $key => $value) {
            $data['sToControl'] = $this->_aControl[$key];
            $data['sToAction'] = $value['name'];
            break;
        }
        return $data;
    }

}