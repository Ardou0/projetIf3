<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

class profileController
{

    private $_view;
    private $_model;

    public function __construct()
    {
        $this->_model = new model();
        $this->_view = new view("profile");
        if (!isset($_SESSION['type'])) {
            header('location:' . URL . 'login');
            exit();
        } else {
            $this->_view->buildUp(array("data" => $this->_model->extract("profile.json")));
        }
    }
}
