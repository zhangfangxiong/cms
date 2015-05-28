<?php

class Bootstrap extends Yaf_Bootstrap
{
    public function _initRoute (Yaf_Dispatcher $dispatcher)
    {
        // $router = Yaf_Dispatcher::getInstance()->getRouter();
        // print_r(get_class_methods($router));
        // print_r($router->getCurrentRoute());
    }

    public function _initPlugin (Yaf_Dispatcher $dispatcher)
    {
        // $dispatcher->registerPlugin(new TestPlugin());
        //$dispatcher->registerPlugin(new Plugin_Auth());
    }
}