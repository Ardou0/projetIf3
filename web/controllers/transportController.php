<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

class transportController
{
    private $_view;
    private $_model;

    public function __construct()
    {
        $this->_model = new model();
        $this->_view = new view("transport");

        $destinations = $this->_model->executeQuery("SELECT * FROM destination");
        $sql = "SELECT *
        FROM `transport_reference` t
        INNER JOIN destination d ON t.destination_id = d.destination_id
        ";
        $conditions = [];
        $params = [];
        // Application des filtres dynamiquement
        if (!empty($_POST['price_min'])) {
            $conditions[] = "t.price >= ?";
            $params[] = $_POST['price_min'];
        }
        if (!empty($_POST['price_max'])) {
            $conditions[] = "t.price <= ?";
            $params[] = $_POST['price_max'];
        }
        if (!empty($_POST['destination'])) {
            $conditions[] = "d.destination_id = ?";
            $params[] = $_POST['destination'];
        }
        if (!empty($_POST['continent_name'])) {
            $conditions[] = "d.continent = ?";
            $params[] = $_POST['continent_name'];
        }
        if(!empty($_POST['transport'])) {
            $conditions[] = "t.transport_type = ?";
            $params[] = $_POST['transport'];
        }

        // Ajout des conditions dans la requête
        if ($conditions) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY t.`transport_reference_id` DESC LIMIT 10";


        $this->_view->buildUp(array("data" => $this->_model->extract("transport.json"), 'destinations' => $destinations, "transports" => $this->_model->executeQuery($sql, $params)));
    }
}
