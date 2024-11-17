<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');
?>
<section id="invoice-body">

    <div id="header-invoice">
        <h1 style="border-radius: 5px; margin-top:50px;"><?php echo $data['header_title']; ?></h1>
    </div>

    <!-- Informations du client -->
    <div class='section'>
        <h2><?php echo $data['client_title']; ?></h2>
        <div class='details'>
            <p><strong><?php echo $data['client_name_label']; ?> :</strong> <?php echo $reservation['client_first_name'] . ' ' . $reservation['client_last_name']; ?></p>
            <p><strong><?php echo $data['client_email_label']; ?> :</strong> <?php echo $reservation['client_email']; ?></p>
            <p><strong><?php echo $data['client_phone_label']; ?> :</strong> <?php echo $reservation['phone_number']; ?></p>
        </div>
    </div>

    <!-- Détails de la réservation -->
    <div class='section'>
        <h2><?php echo $data['reservation_details_title']; ?></h2>
        <div class='details'>
            <p><strong><?php echo $data['reservation_id_label']; ?> :</strong> <?php echo $reservation['reservation_id']; ?></p>
            <p><strong><?php echo $data['destination_label']; ?> :</strong> <?php echo $reservation['destination_city'] . ', ' . $reservation['destination_country']; ?></p>
            <p><strong><?php echo $data['travel_dates_label']; ?> :</strong> <?php echo $data['travel_date_from_label']; ?> <?php echo $reservation['travel_date_from']; ?> <?php echo $data['travel_date_to_label']; ?> <?php echo $reservation['travel_date_to']; ?></p>
            <p><strong><?php echo $data['reservation_status_label']; ?> :</strong> <?php echo $reservation['reservation_status']; ?></p>
            <p><strong><?php echo $data['passengers_label']; ?> :</strong></p>
            <ul>
                <?php if ($reservation['passenger_first_names']) {
                    $passenger_first_names = explode(',', $reservation['passenger_first_names']);
                    $passenger_last_names = explode(',', $reservation['passenger_last_names']);
                    for ($i = 0; $i < count($passenger_first_names); $i++) {
                        echo '<li>' . $passenger_first_names[$i] . ' ' . $passenger_last_names[$i] . '</li>';
                    }
                } else {
                    echo $data['none'];
                }

                ?>
            </ul>
        </div>
    </div>


    <!-- Détails du forfait (affiché seulement si le forfait existe) -->
    <?php if (!empty($reservation['package_description'])) { ?>
        <div class='section'>
            <h2><?php echo $data['package_details_title']; ?></h2>
            <div class='details'>
                <p><strong><?php echo $data['package_description_label']; ?> :</strong> <?php echo $reservation['package_description']; ?></p>
                <p><strong><?php echo $data['package_duration_label']; ?> :</strong> <?php echo $reservation['package_duration']; ?> <?php echo $data['days_label']; ?></p>
                <p><strong><?php echo $data['package_price_label']; ?> :</strong> <?php echo $reservation['package_price']; ?> EUR</p>
            </div>
        </div>
    <?php } ?>


    <!-- Détails de l'itinéraire (affiché seulement si le forfait existe) -->
    <?php if (!empty($reservation['itinerary_id'])) { ?>
        <div class="itinerary-container section">
            <h2><?= $data["itinerary"]["title"] ?></h2>
            <div class="details">
                <p><?= $data['itinerary']['description'] ?> :</p>
                <p><?= $reservation['itinerary_schedule'] ?></p>
                <p><?= $data['itinerary']['contact'] ?>: <?= $reservation['emergency_contact'] ?></p>
            </div>
        </div>
    <?php } ?>

    <!-- Itinéraire (affiché seulement si l'itinéraire existe) -->
    <?php if (!empty($reservation['itinerary_schedule'])) { ?>
        <div class='section'>
            <h2><?php echo $data['itinerary_title']; ?></h2>
            <div class='details'>
                <p><strong><?php echo $data['itinerary_description_label']; ?> :</strong> <?php echo $reservation['itinerary_schedule']; ?></p>
                <p><strong><?php echo $data['emergency_contact_label']; ?> :</strong> <?php echo $reservation['emergency_contact']; ?></p>
            </div>
        </div>
    <?php } ?>

    <!-- Activités (affiché seulement si les activités existent) -->
    <?php if (!empty($reservation['activity_names'])) { ?>
        <div class='section'>
            <h2><?php echo $data['activities_title']; ?></h2>
            <div class='details breadcrumb'>
                <?php
                $activities = explode(',', $reservation['activity_names']);
                $activity_descriptions = explode(',', $reservation['activity_descriptions']);
                $activity_durations = explode(',', $reservation['activity_durations']);
                foreach ($activities as $index => $activity) {
                    echo '<strong>' . $activity . '</strong> - ' . $activity_descriptions[$index] . ' (' . $data['duration_label'] . ': ' . $activity_durations[$index] . ' ' . $data['hours_label'] . ')<br>';
                } ?>
            </div>
        </div>
    <?php } ?>

    <!-- Transport (affiché seulement si le transport existe) -->
    <?php if (!empty($reservation['transport_provider'])) { ?>
        <div class='section'>
            <h2><?php echo $data['transport_title']; ?></h2>
            <div class='details'>
                <p><strong><?php echo $data['transport_provider_label']; ?> :</strong> <?php echo $reservation['transport_provider']; ?></p>
                <p><strong><?php echo $data['transport_type_label']; ?> :</strong> <?php echo $reservation['transport_type']; ?></p>
                <p><strong><?php echo $data['transport_price_label']; ?> :</strong> <?php echo $reservation['transport_price']; ?> EUR</p>
            </div>
        </div>
    <?php } ?>

    <!-- Hébergement (affiché seulement si l'hébergement existe) -->
    <?php if (!empty($reservation['accommodation_provider'])) { ?>
        <div class='section'>
            <h2><?php echo $data['accommodation_title']; ?></h2>
            <div class='details'>
                <p><strong><?php echo $data['accommodation_provider_label']; ?> :</strong> <?php echo $reservation['accommodation_provider']; ?></p>
                <p><strong><?php echo $data['room_type_label']; ?> :</strong> <?php echo $reservation['room_type']; ?></p>
                <p><strong><?php echo $data['price_per_night_label']; ?> :</strong> <?php echo $reservation['price_per_night']; ?> EUR</p>
            </div>
        </div>
    <?php } ?>

    <!-- Paiement -->
    <div class='section'>
        <h2><?php echo $data['payment_title']; ?></h2>
        <div class='details'>
            <p><strong><?php echo $data['payment_amount_label']; ?> :</strong> <?php echo $reservation['payment_amount']; ?> EUR</p>
            <p><strong><?php echo $data['payment_date_label']; ?> :</strong> <?php echo $reservation['payment_date']; ?></p>
        </div>
    </div>
</section>