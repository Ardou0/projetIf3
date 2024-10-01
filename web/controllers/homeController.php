<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

class homeController {

    private $_view;

    public function __construct()
    {
        $this->_view = new view("home");
        $this->_view->buildUp([]);
    }

}