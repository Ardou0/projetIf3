<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

class accommodationsController
{
    private $_view;
    private $_model;

    public function __construct($url)
    {
        $this->_model = new model();
        if (isset($url[2]) and $url[2] == "show" and isset($url[3])) {
            $sql = "SELECT * FROM accommodation_reference AC INNER JOIN company c ON AC.company_id = c.company_id WHERE accommodation_reference_id = ?";
            $accommodation = $this->_model->executeQuery($sql, [$url[3]]);
            if ($accommodation) {
                $accommodation = $accommodation[0];
                $sql = "SELECT * FROM `comments` c INNER JOIN accommodation aa ON c.accommodation_id = aa.accommodation_id INNER JOIN client ct on c.client_id = ct.client_id WHERE aa.accommodation_reference_id = ?";
                $comments = $this->_model->executeQuery($sql, [$url[3]]);
                $this->_view = new view("offers/accommodation");
                $this->_view->buildUp(array("accommodation" => $accommodation, "data" => $this->_model->extract("offers/accommodation.json"), 'comments' => $comments));
            } else {
                header('location' . URL . 'accommodations');
                exit();
            }
        } elseif (isset($url[2]) and $url[2] == "book" and isset($url[3]) and isset($_SESSION['type']) and $_SESSION['type'] == "client") {
            $sql = "SELECT 
                AR.accommodation_reference_id,
                AR.provider_name,
                AR.accommodation_photo,
                AR.room_type,
                AR.amenities,
                AR.max_occupants,
                AR.price_per_night
            FROM 
                accommodation_reference AR
            WHERE 
                AR.accommodation_reference_id = ?;";
            $reference = $this->_model->executeQuery($sql, [$url[3]]);

            if ($reference) {
                $reference = $reference[0];

                $sql = "INSERT INTO accommodation (
                    accommodation_reference_id,
                    check_in_date,
                    check_out_date
                )
                VALUES (
                    ?,  -- ID de l'accommodation reference
                    ?,  -- Date d'arrivée
                    ?   -- Date de départ
                );
        
                -- Récupérer l'ID de l'hébergement nouvellement inséré
                SET @accommodation_id = LAST_INSERT_ID();
        
                -- Créer la réservation avec uniquement l'hébergement
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
                    (SELECT destination_id FROM accommodation_reference WHERE accommodation_reference_id = ? LIMIT 1),  -- Destination associée à l'hébergement
                    NULL,  -- Package ID (pas de package)
                    NULL,  -- Transport ID (pas de transport)
                    @accommodation_id,  -- Accommodation ID généré
                    NOW(),  -- Date de réservation actuelle
                    ?,  -- Date de début du voyage
                    ?,  -- Date de fin du voyage
                    ?,  -- Points de fidélité générés
                    'pending'  -- Statut de la réservation
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
                    'credit_card',  -- Méthode de paiement (toujours carte de crédit)
                    'pending'  -- Statut initial du paiement
                );";

                $params = [
                    $reference['accommodation_reference_id'],  // accommodation_reference_id pour l'insertion dans accommodation
                    $_POST['departure'],  // Date d'arrivée
                    $_POST['return'],  // Date de départ
                    $_SESSION['id'],  // Client ID
                    "0",  // Nombre de passagers
                    $reference['accommodation_reference_id'],  // Destination associée à l'hébergement
                    $_POST['departure'],  // Date de début du voyage
                    $_POST['return'],  // Date de fin du voyage
                    ($reference['price_per_night'] * ((strtotime($_POST['return']) - strtotime($_POST['departure'])) / (60 * 60 * 24))) / 10,  // Points de fidélité générés
                    ($reference['price_per_night'] * ((strtotime($_POST['return']) - strtotime($_POST['departure'])) / (60 * 60 * 24)))  // Montant du paiement
                ];


                $this->_model->executeQuery($sql, $params);
                header('Location:' . URL . 'reservation');
                exit();
            } else {
                header('location:' . URL . 'transport');
                exit();
            }
        } elseif (!isset($_SESSION['type']) and isset($url[2]) and $url[2] == "book") {
            header('location:' . URL . 'login/notification/connect');
            exit();
        } else {
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
}
