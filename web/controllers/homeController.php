<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

class homeController
{

    private $_view;
    private $_model;

    public function __construct()
    {
        $this->_model = new model();

        $packages = $this->_model->executeQuery("SELECT * FROM `package_reference` p INNER JOIN destination d ON p.destination_id = d.destination_id WHERE (SELECT COUNT(*) FROM activity WHERE package_reference_id = p.package_reference_id) >= 1 ORDER BY package_reference_id DESC LIMIT 4");
        if (count($packages) > 0) {
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
        if (isset($_SESSION['type']) and $_SESSION['type'] == "client") {
            $sql = "SELECT pr.*, d.city
            FROM package_reference pr
            INNER JOIN destination d ON pr.destination_id = d.destination_id
            JOIN transport_reference tr ON pr.transport_reference_id = tr.transport_reference_id
            JOIN client c ON c.client_id = ?
            LEFT JOIN reservation r ON pr.package_reference_id = r.package_id AND r.client_id = c.client_id
            WHERE c.travel_preferences = tr.transport_type
            AND r.package_id IS NULL
            LIMIT 4;";

            $recommendation = $this->_model->executeQuery($sql, [$_SESSION['id']]);
        }
        else {
            $recommendation = [];
        }

        $data = $this->_model->extract("home.json");
        $destinations = $this->_model->executeQuery("SELECT * FROM `destination`");

        $this->_view = new view("home");
        $this->_view->buildUp(array("data" => $data, "packages" => $packages, "activities" => $activities, "destinations" => $destinations, "recommendation" => $recommendation));
    }
}
