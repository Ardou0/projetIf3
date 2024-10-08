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
        $package_reference = $this->_model->executeQuery("SELECT * FROM `package_reference` ORDER BY `package_reference_id` desc limit 5");
        $accommodation_reference = $this->_model->executeQuery("SELECT * FROM `accommodation_reference` ORDER BY `accommodation_reference_id` desc limit 5");
        $transport_reference = $this->_model->executeQuery("SELECT * FROM `transport_reference` ORDER BY `transport_reference_id` DESC LIMIT 5");

        $this->_view = new view("home");
        $this->_view->buildUp(array("data" => $data, "options" => $options, "package" => $package_reference, "accommodation" => $accommodation_reference, "transport" => $transport_reference));
    }

}