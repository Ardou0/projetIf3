<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

class controller
{
    private $_model;
    private $_params;


    public function __construct()
    {
        $this->_params = @explode("/", $_GET['url']);

        try {
            if (!isset($this->_params[1])) {
                require_once(PATH."controllers/homeController.php");
                new homeController();
            } else {
                $controller = strtolower($this->_params[1]);
                $controller_class = $controller . "Controller";
                $controller_file = PATH."controllers/" . $controller_class . ".php";

                if(!file_exists($controller_file)) {
                    throw new Exception(404);
                }
                else {
                    require_once($controller_file);
                    new $controller_class($this->_params);
                }
            }
        } catch (Exception $e) {
            require_once(PATH."controllers/errorController.php");
            new errorController($e->getMessage()); 
        }
    }
}