<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

class homeController {

    private $_view;
    private $_model;

    public function __construct()
    {   
        $this->_model = new model();

        $packages = $this->_model->executeQuery("");

        $data = $this->_model->extract("home.json");

        $this->_view = new view("home");
        $this->_view->buildUp(array("data" => $data, "packages" => $packages));
    }

}