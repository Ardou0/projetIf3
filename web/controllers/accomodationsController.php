<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

class accomodationsController
{
    private $_view;
    private $_model;

    public function __construct()
    {
        $this->_model = new model();
        $this->_view = new view("accommodations");

        $destinations = $this->_model->executeQuery("SELECT * FROM destination");
        $sql = "SELECT *
                FROM `accommodation_reference` a
                INNER JOIN destination d ON a.destination_id = d.destination_id
                ";
        $conditions = [];
        $params = [];
        // Application des filtres dynamiquement
        if (!empty($_POST['price_min'])) {
            $conditions[] = "a.price_per_night >= ?";
            $params[] = $_POST['price_min'];
        }
        if (!empty($_POST['price_max'])) {
            $conditions[] = "a.price_per_night <= ?";
            $params[] = $_POST['price_max'];
        }
        if (!empty($_POST['destination'])) {
            $conditions[] = "d.destination_id = ?";
            $params[] = $_POST['destination'];
        }
        if (!empty($_POST['people'])) {
            // Exemple d'application du filtre people
            if ($_POST['people'] == 'seul') {
                $conditions[] = "a.max_occupants = 1";
            } elseif ($_POST['people'] == 'couple') {
                $conditions[] = "a.max_occupants = 2";
            } elseif ($_POST['people'] == 'famille') {
                $conditions[] = "a.max_occupants > 2";
            }
        }
        if (!empty($_POST['continent_name'])) {
            $conditions[] = "d.continent = ?";
            $params[] = $_POST['continent_name'];
        }

        // Ajout des conditions dans la requête
        if ($conditions) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY `accommodation_reference_id` DESC LIMIT 10";


        $this->_view->buildUp(array("data" => $this->_model->extract("accommodations.json"), 'destinations' => $destinations, "accommodations" => $this->_model->executeQuery($sql, $params)));
    }
}
