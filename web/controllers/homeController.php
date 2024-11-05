<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

class homeController {

    private $_view;
    private $_model;

    public function __construct()
    {   
        $this->_model = new model();

        $packages = $this->_model->executeQuery("SELECT * FROM `package_reference` p INNER JOIN destination d ON p.destination_id = d.destination_id WHERE activity_count >= 1 ORDER BY package_reference_id DESC LIMIT 4");
        if(count($packages) > 0) {
            $sql = "SELECT * FROM activity";
            $conditions = [];
            $params = [];
            foreach ($packages as $pack) {
                $conditions[] = "package_reference_id = ?";
                $params[] = $pack['package_reference_id'];
            }
            if ($conditions) {
                $sql .= " WHERE " . implode(" OR ", $conditions);
            }
            $activities = $this->_model->executeQuery($sql, $params);
        }

        $data = $this->_model->extract("home.json");
        $destinations = $this->_model->executeQuery("SELECT * FROM `destination`");

        $this->_view = new view("home");
        $this->_view->buildUp(array("data" => $data, "packages" => $packages, "activities" => $activities, "destinations" => $destinations));
    }

}