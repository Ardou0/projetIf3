<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

class transportController
{
    private $_view;
    private $_model;

    public function __construct($url)
    {
        $this->_model = new model();

        if (isset($url[2]) and $url[2] == "show" and isset($url[3])) {
            $sql = "SELECT * FROM transport_reference t INNER JOIN company c ON t.company_id = c.company_id WHERE transport_reference_id = ?";
            $transport = $this->_model->executeQuery($sql, [$url[3]]);
            if ($transport) {
                $transport = $transport[0];
                $sql = "SELECT * FROM `comments` c INNER JOIN transport t ON c.transport_id = t.transport_id INNER JOIN client ct on c.client_id = ct.client_id WHERE t.transport_reference_id = ?";
                $comments = $this->_model->executeQuery($sql, [$url[3]]);
                $this->_view = new view("offers/transport");
                $this->_view->buildUp(array("transport" => $transport, "data" => $this->_model->extract("offers/transport.json"), "comments" => $comments));
            } else {
                header('location' . URL . 'transport');
                exit();
            }
        } elseif (isset($url[2]) and $url[2] == "book" and isset($url[3]) and isset($_SESSION['type']) and $_SESSION['type'] == "client") {
            $sql = "SELECT * FROM transport_reference WHERE transport_reference_id = ?;";
            $transport = $this->_model->executeQuery($sql, [$url[3]]);

            if ($transport) {
                $transport = $transport[0];

                $passengers = 0;
                for ($i = 0; $i < count($_POST); $i++) {
                    if (isset($_POST['passengers-' . $i . '-first_name'])) {
                        $passengers += 1;
                    }
                }

                $sql = "INSERT INTO transport (
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
                    (SELECT destination_id FROM transport_reference WHERE transport_reference_id = ? LIMIT 1),  -- Destination associée au package
                    NULL,  -- Package ID généré
                    @transport_id,  -- Transport ID généré
                    NULL,  -- Accommodation ID généré
                    NOW(),  -- Date de réservation actuelle
                    ?,  -- Date de début du voyage
                    ?,  -- Date de fin du voyage
                    ?,  -- Points de fidélité générés
                    'pending'   -- Statut de la réservation
                );
                SET @reservation_id = LAST_INSERT_ID();
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
                    'pending'   -- Statut initial du paiement
                );";

                if ($passengers > 0) {
                    $sql .=
                        implode("", array_map(function ($i) {
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
                            );";
                        }, range(0, $passengers - 1)));
                }

                $params = [
                    $transport['transport_reference_id'],  // package_reference_id
                    $_POST['departure'] . ' 08:00:00',  // departure_date
                    $_POST['departure'] . ' 18:00:00',  // arrival_date
                    time(),  // ticket_number
                    'Aisle',  // seat_preference
                    $_SESSION['id'],  // client_id
                    $passengers,  // num_passengers
                    $transport['destination_id'],  // destination_id (retrieved from package_reference)
                    $_POST['departure'],  // travel_date_from
                    $_POST['departure'],  // travel_date_to
                    $transport['price'] / 10,  // loyalty_points_generated
                    $transport['price'],  // amount
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
                header('location:' . URL . 'transport');
                exit();
            }
        } elseif (!isset($_SESSION['type']) and isset($url[2]) and $url[2] == "book") {
            header('location:' . URL . 'login/notification/connect');
            exit();
        } else {

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
            if (!empty($_POST['transport'])) {
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
}
