<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

class transportController {
    private $_view;
    private $_model;

    public function __construct()
    {   
        $this->_model = new model();
        $this->_view = new view("transport");
        $this->_view->buildUp(array());
    }
}