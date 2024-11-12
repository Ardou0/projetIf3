<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

?>

<section id="package-view">
    <h1><?= $package['package_description'] ?></h1>

    <div class="section">

        <h2><?= $data["activities"]["title"] ?></h2>
        <?php

        if (isset($activities)) {
        ?>

            <div class="details">
                <?php
                foreach ($activities as $activity) {
                ?>

                    <div class="activity">
                        <div class="container-image">
                            <img src="<?= URL . "public/img/activities/" . $activity['activity_photo'] ?>" alt="<?= $activity['activity_name'] ?>">
                        </div>
                        <div class="container-description">
                            <h3><?= $activity['activity_name'] ?></h3>
                            <p><?= $activity['activity_description'] ?></p>
                        </div>
                    </div>

                <?php
                }
                ?>
            </div>
        <?php
        } else {
        ?>

            <div class="details">
                <?= $data['activities']['none'] ?>
            </div>

        <?php
        }

        ?>
    </div>

    <div class="itinerary-container section">
        <h2><?= $data["itinerary"]["title"] ?></h2>
        <div class="details">
            <p><?= $data['itinerary']['description'] ?> :</p>
            <p><?= $package['schedule_description'] ?></p>
            <p><?= $data['itinerary']['contact'] ?>: <?= $package['emergency_contact'] ?></p>
        </div>
    </div>

    <div class="accommodation-container section">
        <h2><?= $data["accommodation"]["title"] ?></h2>
        <div class="details">
            <div class="container-image">
                <img src="<?= URL . "public/img/accommodations/" . $package['accommodation_photo'] ?>" alt="<?= $package['accommodation_provider'] ?>">
            </div>
            <div class="container-description">
                <h3><?= $package['accommodation_provider'] ?></h3>
                <p><?= $data['accommodation']['room'] ?> : <?= $package['room_type'] ?></p>
                <p><?= $data['accommodation']['plus'] ?> : <?= $package['amenities'] ?></p>
                <p><?= $data['accommodation']['for'] ?> : <?= $package['max_occupants'] ?></p>
            </div>
        </div>
    </div>

    <div class="transport-container section">
        <h2><?= $data["transport"]["title"] ?></h2>
        <div class="details">
            <h3><?= $package['transport_provider'] ?></h3>
            <p><?= $data['transport']['type']['title'] ?> : <?= $data['transport']['type'][$package['transport_type']] ?></p>
            <p><?= $data['transport']['format'] ?> : <?= $package['ticket_format'] ?></p>
            <p><?= $data['transport']['seat'] ?> : <?= $package['seat_available'] ?></p>
        </div>
    </div>

    <div class="price-container section">
        <h2><?= $data["price"]["title"] ?></h2>
        <div class="details">
            <p><?= $data["price"]["pack"] ?> : <?= $package['package_price'] . CURRENCY ?></p>
            <p><?= $data["price"]["accommodation"] ?> : <?= $package['price_per_night'] . CURRENCY ?></p>
            <p><?= $data["price"]["transport"] ?> : <?= $package['transport_price'] . CURRENCY ?></p>
            <p><?= $data["price"]["total"] ?> : <?= $package['package_price'] + $package['transport_price'] + ($package['price_per_night'] * $package['duration']) . CURRENCY ?></p>
        </div>
    </div>

    <div class="section section-inverse">
        <h2><?= $data["book"]["title"] ?></h2>
        <div class="details">
            <form action="<?= URL . "travel/book/" . $package['package_reference_id'] ?>" method="POST">
                <div>
                    <label for="departureDate"><?= $data["departure"] ?> :</label>
                    <input id="departureDate" class="dateInput" type="date" name="departure" required>
                </div>
                <div>
                    <label for="returnDate"><?= $data["return"] ?> :</label>
                    <input id="returnDate" class="dateInput" type="date" name="return" required>
                </div>
                <div>
                    <h3><?= $data['passenger'] ?> :</h3>
                    <ul id="passenger-details">
                        <li class="passenger you">
                            <?= $data['you'] ?>
                        </li>
                    </ul>
                </div>
                <div class="book-buttons">
                    <button type="button" id="add-passenger"><?= htmlspecialchars($data['add_passenger_button']); ?></button>
                    <button type="submit" id="book-button"><?= htmlspecialchars($data['book']['button']); ?></button>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const maxPassengers = <?= htmlspecialchars($package['max_occupants']); ?> - 1;
            let passengerCount = 0;

            document.getElementById('add-passenger').addEventListener('click', function() {
                if (passengerCount < maxPassengers) {
                    const passengerDetails = document.getElementById('passenger-details');
                    const newPassenger = document.createElement('li');
                    newPassenger.classList.add('passenger');
                    newPassenger.innerHTML = `
                    <label><?= htmlspecialchars($data['first_name_label']); ?> : <input type="text" name="passengers-${passengerCount}-first_name" required></label>
                    <label><?= htmlspecialchars($data['last_name_label']); ?> : <input type="text" name="passengers-${passengerCount}-last_name" required></label>
                    <label><?= htmlspecialchars($data['email_label']); ?> : <input type="email" name="passengers-${passengerCount}-email" required></label>
                `;
                    passengerDetails.appendChild(newPassenger);
                    passengerCount++;
                } else {
                    alert("<?= $data['max_passengers_alert']; ?>");
                }
            });
        });

        let duration = <?= $package['duration'] ?>;
        document.querySelectorAll(".dateInput").forEach(element => {
            element.addEventListener('input', () => {
                let date = new Date(element.value)
                if (element.id == "departureDate") {
                    date.setDate(date.getDate() + duration);
                    document.getElementById("returnDate").value = date.toISOString().split('T')[0];
                }
                if (element.id == "returnDate") {
                    date.setDate(date.getDate() - duration);
                    document.getElementById("departureDate").value = date.toISOString().split('T')[0];
                }
            })
        });
    </script>

</section>