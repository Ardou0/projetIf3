<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

class travelController {
    private $_view;
    private $_model;

    public function __construct()
    {   
        $this->_model = new model();
        $package_reference = $this->_model->executeQuery("SELECT * FROM `package_reference` ORDER BY `package_reference_id` desc limit 5");
        $this->_view = new view("travel");
        $this->_view->buildUp(array());
    }
}