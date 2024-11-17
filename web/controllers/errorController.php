<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

class errorController
{

    private $_view;
    private $_model;

    public function __construct($e)
    {
        $this->_view = new view("error");
        $this->_model = new model();
        $this->_view->buildUp(array("error" => $e, "data" => $this->_model->extract("error.json")));
    }
}
