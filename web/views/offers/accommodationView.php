<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');


if ($accommodation['picture']) {
    $img = URL . "public/img/profile/" . $transport['picture'];
} else {
    $img = URL . "public/img/profile/default.jpg";
}
?>

<section id="accommodation-view">
    <h1><?= $data['title'] ?></h1>

    <div class='section enterprise'>
        <h2><?php echo $data['company_title']; ?></h2>
        <div class='details'>
            <div class="img">
                <img src="<?= $img ?>" alt="company_picture">
            </div>
            <div class="text">
                <p><strong><?php echo $data['company_name']; ?> :</strong> <?php echo $accommodation['full_name'] ?></p>
                <p><strong><?php echo $data['company_email']; ?> :</strong> <?php echo $accommodation['email']; ?></p>
            </div>
        </div>
    </div>

    <div class="accommodation-container section">
        <h2><?= $data["accommodation"]["title"] ?></h2>
        <div class="details">
            <div class="container-image">
                <img src="<?= URL . "public/img/accommodations/" . $accommodation['accommodation_photo'] ?>" alt="<?= $package['accommodation_provider'] ?>">
            </div>
            <div class="container-description">
                <h3><?= $accommodation['provider_name'] ?></h3>
                <p><?= $data['accommodation']['room'] ?> : <?= $accommodation['room_type'] ?></p>
                <p><?= $data['accommodation']['plus'] ?> : <?= $accommodation['amenities'] ?></p>
                <p><?= $data['accommodation']['for'] ?> : <?= $accommodation['max_occupants'] ?></p>
            </div>
        </div>
    </div>

    <div class="price-container section">
        <h2><?= $data["price"]["title"] ?></h2>
        <div class="details">
            <p><?= $data["price"]["accommodation"] ?> : <?= $accommodation['price_per_night'] . CURRENCY ?></p>
        </div>
    </div>

    <div class="section section-inverse">
        <h2><?= $data["book"]["title"] ?></h2>
        <div class="details">
            <form action="<?= URL . "accommodations/book/" . $accommodation['accommodation_reference_id'] ?>" method="POST">
                <div>
                    <label for="departureDate"><?= $data["departure"] ?> :</label>
                    <input id="departureDate" class="dateInput" type="date" name="departure" required>
                </div>
                <div>
                    <label for="returnDate"><?= $data["return"] ?> :</label>
                    <input id="returnDate" class="dateInput" type="date" name="return" required>
                </div>
                <div class="book-buttons">
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
            } else {
                echo "<h1>" . $data['nothing'] . "</h1>";
            }

            ?>
        </div>
    </div>
    <script>
        let date = new Date();
        date.setDate(date.getDate() + 1);
        document.getElementById("departureDate").min = date.toISOString().split('T')[0]
        document.getElementById("returnDate").min = date.toISOString().split('T')[0];

        document.querySelectorAll(".dateInput").forEach(element => {
            element.addEventListener('input', () => {
                let departureDate = new Date(document.getElementById("departureDate").value);
                let returnDate = new Date(document.getElementById("returnDate").value);

                // Ensure a minimum of 1 day interval
                if (element.id === "departureDate" && departureDate >= returnDate) {
                    returnDate.setDate(departureDate.getDate() + 1);
                    document.getElementById("returnDate").value = returnDate.toISOString().split('T')[0];
                } else if (element.id === "returnDate" && returnDate <= departureDate) {
                    departureDate.setDate(returnDate.getDate() - 1);
                    document.getElementById("departureDate").value = departureDate.toISOString().split('T')[0];
                }
            });
        });
    </script>

</section>