<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

class homeController {

    private $_view;
    private $_model;

    public function __construct()
    {   
        $this->_model = new model();
        $data = $this->_model->extract("home.json");
        $options = $this->_model->executeQuery("SELECT * FROM `destination`");
        $this->_view = new view("home");
        $this->_view->buildUp(array("data" => $data, "options" => $options));
    }

}