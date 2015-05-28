<?php

/**
 * 评测报告接口
 * User: cjj
 * Date: 2015/03/16
 * Time: 11:17
 */
ini_set('memory_limit','512M');
set_time_limit(0);
class Controller_Cmd_Evaluation extends Controller_Cmd_Base
{

    private $request = null;
    private $response = null;
    private $view = null;

    /**
     * 评测报告接口
     */
    public function indexAction()
    {
        $logger = Yaf_Logger::getInstance();
        $this -> init();
        $datetime = $this->getParam('datetime');
        if (empty($datetime)) {
            $datetime = date('Y-m-d H:i:s',strtotime("-15 minute"));
        }

        $params = $this->getParams();
        $params['datetime'] = $datetime;
        $this->request->setParam($params);

        self::callAction(self::getController('Evaluation'));
        echo 'Evaluation list is completed ....';
        $logger->info('Evaluation list is completed ....', 'evaluation');
        self::callAction(self::getController('Hxanalyse'));
        echo 'Hxanalyse is completed ....';
        $logger->info('Hxanalyse list is completed ....', 'evaluation');

        self::callAction(self::getController('Zxstandard'));
        echo 'Zxstandard is completed ....';
        $logger->info('Zxstandard list is completed ....', 'evaluation');

        self::callAction(self::getController('Sqpz'));
        echo 'Sqpz is completed ....';
        $logger->info('Sqpz list is completed ....', 'evaluation');

        self::callAction(self::getController('Wyfw'));
        echo 'Wyfw is completed ....';
        $logger->info('Wyfw list is completed ....', 'evaluation');

        self::callAction(self::getController('Traffic'));
        echo 'Traffic is completed ....';
        $logger->info('Traffic list is completed ....', 'evaluation');

        self::callAction(self::getController('Region'));
        echo 'Region is completed ....';
        $logger->info('Region list is completed ....', 'evaluation');

        self::callAction(self::getController('Badfactor'));
        echo 'All is completed ....';
        $logger->info('All is completed ....', 'evaluation');


    }
    // 调用
    private function callAction($obj)
    {
        $method = get_class_methods($obj);
        foreach ($method as $item) {
            if ( substr($item,-6)=='Action') {
                $obj->$item();
            }
        }
    }

    private function getController($controller)
    {
        $controller = 'Controller_CricInevaluation_'.$controller;
        $obj  = new $controller($this->request,$this->response,$this->view);
        return $obj;
        }

    public function init() {
        global $app;
        $this -> request = $app ->getDispatcher()->getRequest();
        if ($this -> request instanceof Yaf_Request_Http) {
            $this->response = new Yaf_Response_Http();
        } elseif ($this -> request instanceof Yaf_Request_Cli) {
            $this->response = new Yaf_Response_Cli();
        }
        $this -> view = $app ->getDispatcher()->initView();
    }

}