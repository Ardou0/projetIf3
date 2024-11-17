<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

class reservationController
{
    private $_view;
    private $_model;

    public function __construct($url)
    {
        $this->_model = new model();

        if (!isset($_SESSION['type'])) {
            header('location:' . URL);
            exit();
        } elseif (isset($url[2]) and $url[2] == 'invoice' and isset($url[3])) {
            $this->generateInvoice($url);
        } elseif (isset($url[2]) and $url[2] == 'cancel' and isset($url[3])) {
            $this->cancelReservation($url);
        } else {
            if ($_SESSION['type'] == 'company') {
                header('location:' . URL);
                exit();
            }
            if (isset($url[2]) and $url[2] == 'pay' and isset($url[3])) {
                $this->payReservation($url);
            } elseif (isset($url[2]) and $url[2] == 'comment' and !isset($url[3])) {
                $this->commentReservation();
            } else {

                if (isset($url[2]) and $url[2] == "notification" and isset($url[3])) {
                    $notification = $url[3];
                } else {
                    $notification = "";
                }
                $this->_view = new view("reservation");
                $sql = "SELECT 
                    R.reservation_id,
                    C.client_id,
                    C.first_name,
                    C.last_name,
                    R.num_passengers,
                    D.city AS destination_city,
                    D.country AS destination_country,
                    PR.duration,
                    R.travel_date_from,
                    R.travel_date_to,
                    R.status AS reservation_status,
                    R.reservation_date,
                    P.payment_status,
                    P.amount AS payment_amount,
                    TR.transport_type,
                    A.accommodation_id,
                    T.transport_id,
                    PCK.package_id,
                    
                    -- Calcul des prix individuels
                    AR.price_per_night * PR.duration AS total_accommodation_price,
                    TR.price AS total_transport_price,
                    PR.price AS total_package_price,
                    
                    -- Calcul du prix total de la réservation
                    (IFNULL(AR.price_per_night *  DATEDIFF(R.travel_date_to, R.travel_date_from), 0) + 
                    IFNULL(TR.price, 0) + 
                    IFNULL(PR.price, 0)) AS total_price,
                    
                    -- Nombre de jours de différence entre travel_date_from et travel_date_to
                    DATEDIFF(R.travel_date_to, R.travel_date_from) AS duration_days,
                    
                    -- Booléen indiquant si un commentaire existe pour tous les éléments de la réservation
                    (
                        SELECT 
                            CASE 
                                WHEN COUNT(*) > 0 THEN 1
                                ELSE 0
                            END
                        FROM comments CM
                        WHERE CM.client_id = C.client_id
                        AND (CM.accommodation_id = A.accommodation_id OR A.accommodation_id IS NULL)
                        AND (CM.transport_id = T.transport_id OR T.transport_id IS NULL)
                        AND (CM.package_id = PCK.package_id OR PCK.package_id IS NULL)
                    ) AS has_comment
                
                FROM 
                    reservation R
                    JOIN client C ON R.client_id = C.client_id
                    LEFT JOIN destination D ON R.destination_id = D.destination_id
                    LEFT JOIN package PCK ON R.package_id = PCK.package_id
                    LEFT JOIN package_reference PR ON PCK.package_reference_id = PR.package_reference_id
                    LEFT JOIN accommodation A ON R.accommodation_id = A.accommodation_id
                    LEFT JOIN accommodation_reference AR ON A.accommodation_reference_id = AR.accommodation_reference_id
                    LEFT JOIN transport T ON R.transport_id = T.transport_id
                    LEFT JOIN transport_reference TR ON T.transport_reference_id = TR.transport_reference_id
                    LEFT JOIN payment P ON R.reservation_id = P.reservation_id
                
                WHERE 
                    C.client_id = ?
                ORDER BY 
                    R.reservation_date DESC;
                ";

                $reservations = $this->_model->executeQuery($sql, [$_SESSION['id']]);

                $updated = false;
                if ($reservations) {
                    foreach ($reservations as $reservation) {
                        if (new DateTime($reservation['travel_date_from']) < new DateTime() && $reservation['reservation_status'] == "confirmed") {
                            $sql = "UPDATE `reservation` SET `status`='completed' WHERE reservation_id = ?";
                            $this->_model->executeQuery($sql, [$reservation['reservation_id']]);
                            $updated = true;
                        }
                    }

                    if ($updated) {
                        header('location:' . URL . 'reservation');
                        exit();
                    } else {
                        $this->_view->buildUp(array("data" => $this->_model->extract("reservation.json"), "reservations" => $reservations, "notification" => $notification));
                    }
                } else {
                    $this->_view->buildUp(array("data" => $this->_model->extract("reservation.json"), "notification" => $notification));
                }
            }
        }
    }

    private function generateInvoice($url)
    {
        $sql = "SELECT 
    R.reservation_id, 
    R.num_passengers, 
    R.reservation_date, 
    R.travel_date_from, 
    R.travel_date_to, 
    R.status AS reservation_status, 
    R.loyalty_points_generated, 
    C.first_name AS client_first_name, 
    C.last_name AS client_last_name, 
    C.email AS client_email, 
    C.phone_number, 
    D.city AS destination_city, 
    D.country AS destination_country, 
    MAX(PR.description) AS package_description, 
    MAX(PR.duration) AS package_duration, 
    MAX(PR.price) AS package_price, 
    MAX(IT.schedule_description) AS itinerary_schedule, 
    MAX(IT.emergency_contact) AS emergency_contact, 
    MAX(TR.provider_name) AS transport_provider, 
    MAX(TR.transport_type) AS transport_type, 
    MAX(TR.price) AS transport_price, 
    MAX(TRR.departure_date) AS departure_date, 
    MAX(TRR.arrival_date) AS arrival_date, 
    MAX(ACCR.provider_name) AS accommodation_provider, 
    MAX(ACCR.room_type) AS room_type, 
    MAX(ACCR.price_per_night) AS price_per_night, 
    MAX(ACCR.accommodation_photo) AS accommodation_photo,  -- Ajouter la photo de l'hébergement
    MAX(ACC.check_in_date) AS check_in_date, 
    MAX(ACC.check_out_date) AS check_out_date, 
    GROUP_CONCAT(ACT.activity_name) AS activity_names, 
    GROUP_CONCAT(ACT.activity_description) AS activity_descriptions, 
    GROUP_CONCAT(ACT.duration_hours) AS activity_durations, 
    GROUP_CONCAT(ACT.activity_photo) AS activity_photos,  -- Ajouter les photos des activités
    GROUP_CONCAT(DISTINCT PSS.first_name) AS passenger_first_names, 
    GROUP_CONCAT(DISTINCT PSS.last_name) AS passenger_last_names, 
    MAX(PAY.amount) AS payment_amount, 
    MAX(PAY.payment_date) AS payment_date, 
    MAX(PAY.payment_method) AS payment_method, 
    MAX(PAY.payment_status) AS payment_status 
FROM 
    reservation R 
    JOIN client C ON R.client_id = C.client_id 
    LEFT JOIN destination D ON R.destination_id = D.destination_id 
    LEFT JOIN package PCK ON R.package_id = PCK.package_id 
    LEFT JOIN package_reference PR ON PCK.package_reference_id = PR.package_reference_id 
    LEFT JOIN itinerary IT ON PR.package_reference_id = IT.package_reference_id 
    LEFT JOIN transport_reference TR ON R.transport_id = TR.transport_reference_id 
    LEFT JOIN transport TRR ON R.transport_id = TRR.transport_id 
    LEFT JOIN accommodation ACC ON R.accommodation_id = ACC.accommodation_id 
    LEFT JOIN accommodation_reference ACCR ON ACC.accommodation_reference_id = ACCR.accommodation_reference_id 
    LEFT JOIN activity ACT ON PR.package_reference_id = ACT.package_reference_id 
    LEFT JOIN passenger PSS ON R.reservation_id = PSS.reservation_id 
    LEFT JOIN payment PAY ON R.reservation_id = PAY.reservation_id 
WHERE 
    R.client_id = ? 
    AND R.reservation_id = ? 
GROUP BY 
    R.reservation_id 
ORDER BY 
    R.reservation_date DESC;";
        if ($_SESSION['type'] == "company" and isset($url[4])) {
            $reservation = $this->_model->executeQuery($sql, [$url[4], $url[3]]);
        } else {
            $reservation = $this->_model->executeQuery($sql, [$_SESSION['id'], $url[3]]);
        }
        if (isset($reservation)) {
            $reservation = $reservation[0];
            $this->_view = new view("invoice");
            $this->_view->buildUp(array("reservation" => $reservation, "data" => $this->_model->extract("invoice.json")));
        } else {
            header('location:' . URL . 'reservation/notification/error');
        }
    }

    private function payReservation($url)
    {
        if (!isset($url[4])) {
            header('location:' . URL . 'reservation/notification/error');
            exit();
        } else {
            if ($url[4] == "bank") {
                $method = "bank_transfer";
            } elseif ($url[4] == "card") {
                $method = 'credit_card';
            }
            $sql = "UPDATE `payment` p SET `payment_status`='completed', `payment_method` = ? WHERE reservation_id = (SELECT reservation_id FROM reservation WHERE reservation_id = ? and client_id = ?);";
            $this->_model->executeQuery($sql, [$method, $url[3], $_SESSION['id']]);
            $sql = "UPDATE `reservation` SET `status`='confirmed' WHERE reservation_id = ? and client_id = ?";
            $this->_model->executeQuery($sql, [$url[3], $_SESSION['id']]);
            header("location:" . URL . "reservation/notification/payed");
            exit();
        }
    }

    private function cancelReservation($url)
    {
        if ($_SESSION['type'] == "company" and isset($url[4])) {
            $id = $url[4];
            $redirect = "dashboard";
        } else {
            $id = $_SESSION['id'];
            $redirect = "reservation/notification/cancelled";
        }
        $sql = "UPDATE `payment` p SET `payment_status`='refunded' WHERE reservation_id = (SELECT reservation_id FROM reservation WHERE reservation_id = ? and client_id = ?);";
        $this->_model->executeQuery($sql, [$url[3], $id]);
        $sql = "UPDATE `reservation` SET `status`='cancelled'  WHERE reservation_id = ? and client_id = ?";
        $this->_model->executeQuery($sql, [$url[3], $id]);


        header("location:" . URL . $redirect);
        exit();
    }

    private function commentReservation()
    {
        if (!isset($_POST['rating']) and !isset($_POST['comment'])) {
            header('location:' . URL . 'reservation/error');
            exit();
        } else {
            if (isset($_POST['accommodation_id']) or isset($_POST['transport_id']) or isset($_POST['package_id'])) {
                if (isset($_POST['accommodation_id']) and $_POST['accommodation_id'] != NULL) {
                    $accommodation = $_POST['accommodation_id'];
                } else {
                    $accommodation = NULL;
                }
                if (isset($_POST['transport_id']) and $_POST['transport_id'] != NULL) {
                    $transport = $_POST['transport_id'];
                } else {
                    $transport = NULL;
                }
                if (isset($_POST['package_id']) and $_POST['package_id'] != NULL) {
                    $package = $_POST['package_id'];
                } else {
                    $package = NULL;
                }

                $sql = "INSERT INTO `comments`(`client_id`, `package_id`, `transport_id`, `accommodation_id`, `rating`, `comments`) VALUES (?, ?, ?, ?, ?, ?)";
                $params = [
                    $_SESSION['id'],
                    $package,
                    $transport,
                    $accommodation,
                    $_POST['rating'],
                    htmlspecialchars($_POST['comment'])
                ];

                $this->_model->executeQuery($sql, $params);
                header("location:" . URL . "reservation/notification/comment");
                exit();
            } else {
                header('location:' . URL . 'reservation/error');
                exit();
            }
        }
    }
}
