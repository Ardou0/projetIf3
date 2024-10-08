<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

class loginController {

    private $_view;

    public function __construct()
    {
        $this->_view = new view("login");
        $this->_view->buildUp(array());
    }

}