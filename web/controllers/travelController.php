<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

class travelController
{
    private $_view;
    private $_model;

    public function __construct($url)
    {
        $this->_model = new model();

        $destinations = $this->_model->executeQuery("SELECT * FROM destination");
        // Initialisation de la requête SQL et des paramètres
        $sql = "SELECT p.*, d.*, a.*, t.transport_type
                FROM `package_reference` p 
                INNER JOIN destination d ON p.destination_id = d.destination_id 
                INNER JOIN accommodation_reference a ON p.accommodation_reference_id = a.accommodation_reference_id
                INNER JOIN transport_reference t on p.transport_reference_id = t.transport_reference_id
                ";
        $conditions = [];
        $params = [];
        // Application des filtres dynamiquement
        if (!empty($_POST['price_min'])) {
            $conditions[] = "p.price >= ?";
            $params[] = $_POST['price_min'];
        }
        if (!empty($_POST['price_max'])) {
            $conditions[] = "p.price <= ?";
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
        if (!empty($_POST['activity_count'])) {
            $conditions[] = "(SELECT COUNT(*) FROM activity WHERE package_reference_id = p.package_reference_id ) >= ?";
            $params[] = $_POST['activity_count'];
        }
        if (!empty($_POST['date_max'])) {
            // Calculer la durée maximale en jours entre la date actuelle et la date max fournie
            if (!empty($_POST['date_min'])) {
                $departure = new DateTime($_POST['date_min']);
            }
            else {
                $departure = new DateTime();
            }
            $date_max = new DateTime($_POST['date_max']);
            $interval = $departure->diff($date_max);
            $max_duration_days = $interval->days;

            // Ajouter la condition pour la durée
            $conditions[] = "p.duration <= ?";
            $params[] = $max_duration_days;
        }
        if (!empty($_POST['continent_name'])) {
            $conditions[] = "d.continent = ?";
            $params[] = $_POST['continent_name'];
        }

        // Ajout des conditions dans la requête
        if ($conditions) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY `package_reference_id` DESC LIMIT 10";

        // Exécution de la requête avec les paramètres
        $package_references = $this->_model->executeQuery($sql, $params);

        // Extraction des package IDs pour les activités associées
        if ($package_references && !empty($package_references)) {

            $package_ids = array_column($package_references, 'package_reference_id');

            if (!empty($package_ids)) {
                // Récupération des activités en une requête
                $placeholders = implode(',', array_fill(0, count($package_ids), '?'));
                $activities_query = "SELECT * FROM `activity` WHERE `package_reference_id` IN ($placeholders)";
                $activities = $this->_model->executeQuery($activities_query, $package_ids);

                // Organisation des activités par package ID
                $activities_by_package = [];
                if (isset($activities)) {
                    foreach ($activities as $activity) {
                        $package_id = $activity['package_reference_id'];
                        $activities_by_package[$package_id][] = $activity;
                    }
                }

                // Association des activités aux packages
                foreach ($package_references as &$package) {
                    $package_id = $package['package_reference_id'];
                    $package['activities'] = $activities_by_package[$package_id] ?? [];
                }
            }
        }

        // Transmission des packages à la vue
        $this->_view = new view("travel");
        $this->_view->buildUp(['packages' => $package_references, 'destinations' => $destinations, 'data' => $this->_model->extract('travel.json')]);
    }
}
