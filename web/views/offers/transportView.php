<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');



if ($transport['picture']) {
    $img = URL . "public/img/profile/" . $transport['picture'];
} else {
    $img = URL . "public/img/profile/default.jpg";
}
?>


<section id="transport-view">
    <h1><?= $data['title'] ?></h1>
    
    <div class='section enterprise'>
        <h2><?php echo $data['company_title']; ?></h2>
        <div class='details'>
            <div class="img">
                <img src="<?= $img ?>" alt="company_picture">
            </div>
            <div class="text">
                <p><strong><?php echo $data['company_name']; ?> :</strong> <?php echo $transport['full_name'] ?></p>
                <p><strong><?php echo $data['company_email']; ?> :</strong> <?php echo $transport['email']; ?></p>
            </div>
        </div>
    </div>

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

    <div class="section comments">
        <h2><?= $data["comments"]["title"] ?></h2>
        <div class="details">
            <?php

            if (isset($comments)) {
                foreach ($comments as $comment) {

            ?>

                    <div class="comment">
                        <div class="name-rating">
                            <div class="name">
                                <?= $comment['first_name'] . " " . $comment['last_name'] ?>
                            </div>
                            <div class="rating">
                                <?php
                                for ($i = 0; $i < $comment['rating']; $i++) {
                                ?>

                                    <span class="rate">☆</span>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                        <div class="comment-description">
                            <?= htmlspecialchars($comment['comments']) ?>
                        </div>
                    </div>

            <?php
                }
            }
            else {
                echo "<h1>". $data['nothing'] ."</h1>";
            }

            ?>
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