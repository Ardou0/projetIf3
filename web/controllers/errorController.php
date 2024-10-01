<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

class errorController
{

    private $_view;

    public function __construct($e)
    {
        $this->_view = new view("error");
        $this->_view->buildUp(array("error" => $e));
    }
}
