<?php
/**
 * 同步 cricin 评测报告子章节的数据.
 * User: cjj
 * Date: 2015/4/22
 * Time: 9:37
 */

class Controller_Evaluation_Syncricin extends Controller_Base
{
    private $request = null;
    private $response = null;
    private $view = null;

    public function indexAction()
    {
        $this->init();
        $controller = $this->getParam('c');
        $action = $this->getParam('a');
        $action = $action.'Action';

        $unitid = $this->getParam('unitid');
        $params = $this->getParams();
        $params['unitid'] = $unitid;
        $this->request->setParam($params);

        // 同步 子章节数据
        $controllerObj = $this->getController($controller);
        $controllerObj -> $action();

         /* 同步 主表评测报告数据
        $controllerObj = $this->getController('Evaluation');
        $controllerObj -> indexAction();
         */

        $this->showMsg('',true);
    }

    private function getController($controller)
    {
        $controller = 'Controller_CricInevaluation_'.$controller;
        $obj  = new $controller($this->request,$this->response,$this->view);
        return $obj;
    }

    public function init()
    {
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
