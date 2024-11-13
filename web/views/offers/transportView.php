<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');
?>


<section id="transport-view">
    <h1><?= $data['title'] ?></h1>


    <div class="transport-container section">
        <h2><?= $data["transport"]["title"] ?></h2>
        <div class="details">
            <h3><?= $transport['provider_name'] ?></h3>
            <p><?= $data['transport']['type']['title'] ?> : <?= $data['transport']['type'][$transport['transport_type']] ?></p>
            <p><?= $data['transport']['format'] ?> : <?= $transport['ticket_format'] ?></p>
            <p><?= $data['transport']['seat'] ?> : <?= $transport['seat_available'] ?></p>
        </div>
    </div>

    <div class="price-container section">
        <h2><?= $data["price"]["title"] ?></h2>
        <div class="details">
            <p><?= $data["price"]["transport"] ?> : <?= $transport['price'] . CURRENCY ?></p>
        </div>
    </div>

    <div class="section section-inverse">
        <h2><?= $data["book"]["title"] ?></h2>
        <div class="details">
            <form action="<?= URL . "transport/book/" . $transport['transport_reference_id'] ?>" method="POST">
                <div>
                    <label for="departureDate"><?= $data["departure"] ?> :</label>
                    <input id="departureDate" class="dateInput" type="date" name="departure" required>
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
            const maxPassengers = 5 - 1;
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
                }
            });
        });
        let date = new Date();
        date.setDate(date.getDate() + 1);
        document.getElementById("departureDate").min = date.toISOString().split('T')[0];
    </script>
</section>