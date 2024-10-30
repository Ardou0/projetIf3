<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

class accomodationsController {
    private $_view;
    private $_model;

    public function __construct()
    {   
        $this->_model = new model();
        $this->_view = new view("accomodations");
        $this->_view->buildUp(array());
    }
}