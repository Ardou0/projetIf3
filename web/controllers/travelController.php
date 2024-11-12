<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

class travelController
{
    private $_view;
    private $_model;

    public function __construct($url)
    {
        $this->_model = new model();
        if (isset($url[2]) and $url[2] == "show" and isset($url[3])) {
            $sql = "SELECT 
            pr.package_reference_id,
            pr.company_id,
            c.full_name AS company_name,
            c.email AS company_email,
            d.continent,
            d.country,
            d.city,
            pr.transport_reference_id,
            tr.provider_name AS transport_provider,
            tr.transport_type,
            tr.ticket_format,
            tr.seat_available,
            tr.price AS transport_price,
            pr.accommodation_reference_id,
            ar.provider_name AS accommodation_provider,
            ar.room_type,
            ar.amenities,
            ar.max_occupants,
            ar.price_per_night,
            ar.accommodation_photo,
            pr.duration,
            pr.price AS package_price,
            pr.description AS package_description,
            pr.created_at AS package_created_at,
            i.itinerary_id,
            i.schedule_description,
            i.emergency_contact
            FROM 
                package_reference pr
            LEFT JOIN company c ON pr.company_id = c.company_id
            LEFT JOIN destination d ON pr.destination_id = d.destination_id
            LEFT JOIN transport_reference tr ON pr.transport_reference_id = tr.transport_reference_id
            LEFT JOIN accommodation_reference ar ON pr.accommodation_reference_id = ar.accommodation_reference_id
            LEFT JOIN itinerary i ON pr.package_reference_id = i.package_reference_id
            WHERE 
                pr.package_reference_id = ?; -- Remplacez '?' par l'ID du package que vous voulez récupérer
            ";

            $package = $this->_model->executeQuery($sql, [$url[3]]);

            if ($package) {
                $sql = "SELECT * FROM activity WHERE package_reference_id = ?";

                $activities = $this->_model->executeQuery($sql, [$package[0]["package_reference_id"]]);
                $this->_view = new view("offers/package");
                $this->_view->buildUp(array("package" => $package[0], "data" => $this->_model->extract("offers/package.json"), "activities" => $activities));
            } else {
                header('location:' . URL . 'travel');
                exit();
            }
        } elseif (isset($url[2]) and $url[2] == "book" and isset($url[3]) and $_SESSION['type'] == "client") {
            $sql = "SELECT 
            PR.*,  -- Sélectionner toutes les colonnes de package_reference
            (IFNULL(AR.price_per_night * PR.duration, 0) + 
            IFNULL(TR.price, 0) + 
            IFNULL(PR.price, 0)) AS total_price
        FROM 
            package_reference PR
            LEFT JOIN transport_reference TR ON PR.transport_reference_id = TR.transport_reference_id
            LEFT JOIN accommodation_reference AR ON PR.accommodation_reference_id = AR.accommodation_reference_id
        WHERE 
            PR.package_reference_id = ?";
            $params = [$url[3]];

            $package = $this->_model->executeQuery($sql, $params);

            if ($package) {
                $package = $package[0];
                $passengers = 0;
                for ($i = 0; $i < count($_POST); $i++) {
                    if (isset($_POST['passengers-' . $i . '-first_name'])) {
                        $passengers += 1;
                    }
                }
                $sql = "INSERT INTO package (
                package_reference_id
            )
            VALUES (
                ?  -- Package Reference ID
            );

            -- Récupérer l'ID du package nouvellement inséré
            SET @package_id = LAST_INSERT_ID();

            -- Insérer dans la table Transport pour la réservation
            INSERT INTO transport (
                transport_reference_id,
                departure_date,
                arrival_date,
                ticket_number,
                seat_preference
            )
            VALUES (
                ?,  -- Transport Reference ID
                ?,  -- Date de départ
                ?,  -- Date d'arrivée
                ?,  -- Numéro du ticket
                ?   -- Préférence de siège
            );

            -- Récupérer l'ID du transport nouvellement inséré
            SET @transport_id = LAST_INSERT_ID();

            -- Insérer dans la table Hébergement pour la réservation
            INSERT INTO accommodation (
                accommodation_reference_id,
                check_in_date,
                check_out_date
            )
            VALUES (
                ?,  -- Accommodation Reference ID
                ?,  -- Date d'arrivée à l'hébergement
                ?   -- Date de départ de l'hébergement
            );

            -- Récupérer l'ID de l'hébergement nouvellement inséré
            SET @accommodation_id = LAST_INSERT_ID();

            -- Créer la réservation avec les détails appropriés
            INSERT INTO reservation (
                client_id,
                num_passengers,
                destination_id,
                package_id,
                transport_id,
                accommodation_id,
                reservation_date,
                travel_date_from,
                travel_date_to,
                loyalty_points_generated,
                status
            )
            VALUES (
                ?,  -- Client ID
                ?,  -- Nombre de passagers
                (SELECT destination_id FROM package_reference WHERE package_reference_id = ? LIMIT 1),  -- Destination associée au package
                @package_id,  -- Package ID généré
                @transport_id,  -- Transport ID généré
                @accommodation_id,  -- Accommodation ID généré
                NOW(),  -- Date de réservation actuelle
                ?,  -- Date de début du voyage
                ?,  -- Date de fin du voyage
                ?,  -- Points de fidélité générés
                ?   -- Statut de la réservation
            );

            -- Récupérer l'ID de la réservation nouvellement insérée
            SET @reservation_id = LAST_INSERT_ID();

            -- Insérer le paiement lié à la réservation
            INSERT INTO payment (
                reservation_id,
                amount,
                payment_date,
                payment_method,
                payment_status
            )
            VALUES (
                @reservation_id,  -- L'ID de la réservation tout juste créée
                ?,  -- Montant du paiement calculé
                NOW(),  -- Date du paiement actuelle
                'credit_card',  -- Méthode de paiement (carte de crédit)
                ?   -- Statut initial du paiement
            );

            -- Insérer les passagers liés à la réservation
            "
                    . implode("", array_map(function ($i) {
                        return "INSERT INTO passenger (
                    reservation_id,
                    first_name,
                    last_name,
                    email,
                    transport_id
                )
                VALUES (
                    @reservation_id,  -- L'ID de la réservation
                    ?,  -- Prénom du passager
                    ?,  -- Nom du passager
                    ?,  -- Email du passager
                    @transport_id
                );n";
                    }, range(0, $passengers - 1)));
                $params = [
                    $package['package_reference_id'],  // package_reference_id
                    $package['transport_reference_id'],  // transport_reference_id
                    $_POST['departure'] . ' 08:00:00',  // departure_date
                    $_POST['return'] . ' 18:00:00',  // arrival_date
                    time(),  // ticket_number
                    'Aisle',  // seat_preference
                    $package['accommodation_reference_id'],  // accommodation_reference_id
                    $_POST['departure'] . ' 14:00:00',  // check_in_date
                    $_POST['return'] . ' 12:00:00',  // check_out_date
                    $_SESSION['id'],  // client_id
                    $passengers,  // num_passengers
                    $package['destination_id'],  // destination_id (retrieved from package_reference)
                    $_POST['departure'],  // travel_date_from
                    $_POST['return'],  // travel_date_to
                    $package['total_price'] / 10,  // loyalty_points_generated
                    'pending',  // status
                    $package['total_price'],  // amount
                    'pending'  // payment_status
                ];

                for ($i = 0; $i < count($_POST); $i++) {
                    if (isset($_POST['passengers-' . $i . '-first_name'])) {
                        $passengers += 1;
                        array_push(
                            $params,
                            $_POST['passengers-' . $i . '-first_name'],  // first_name
                            $_POST['passengers-' . $i . '-last_name'],  // last_name
                            $_POST['passengers-' . $i . '-email']  // email
                        );
                    }
                }

                $this->_model->executeQuery($sql, $params);

                header('Location:' . URL . 'reservation');
                exit();
            } else {
                header('location:' . URL . 'travel');
                exit();
            }
        } else {
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
                } else {
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
}
