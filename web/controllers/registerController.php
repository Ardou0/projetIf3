<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

class registerController
{

    private $_model;
    public function __construct() {
        if(!isset($_POST['type'])) {
            header('location:'.URL.'login');
            exit();
        }
    }

}