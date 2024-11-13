<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');
?>
<section>
    <?php
    foreach ($reservations as $reservation) {
    ?>
        <div class="reservation">
            <h2><?php echo htmlspecialchars($reservation['destination_city']) . " : " . $data['motd']; ?></h2>
            <div class="description">
                <p><strong><?= $data['destination'] ?>:</strong> <?php echo htmlspecialchars($reservation['destination_city']) . ", " . htmlspecialchars($reservation['destination_country']); ?></p>
                <p><strong><?= $data['duration'] ?>:</strong> <?php echo htmlspecialchars($reservation['duration_days']); ?> jours</p>
                <p><strong><?= $data['price'] ?>:</strong> <?php echo number_format($reservation['total_price'], 2); ?> €</p>
                <p><strong><?= $data['date'] ?>:</strong> <?= $data['from'] ?> <?php echo htmlspecialchars($reservation['travel_date_from']); ?> <?= $data['to'] ?> <?php echo htmlspecialchars($reservation['travel_date_to']); ?></p>
                <p class="<?= $reservation['reservation_status'] ?>"><strong><?= $data['reservation'] ?>:</strong> <?php echo ucfirst($reservation['reservation_status']); ?></p>
                <p class="<?= $reservation['payment_status'] ?>"><strong><?= $data['payment'] ?>:</strong> <?php echo ucfirst($reservation['payment_status']); ?></p>
                <p><?= $data['taken'] ?>: <?php echo htmlspecialchars($reservation['reservation_date']); ?></p>
            </div>
            <div class="buttons">
                <a class="details-button" href="<?= URL . "reservation/invoice/" . $reservation['reservation_id'] ?>">
                    <?= $data['invoice'] ?>
                </a>
                <?php if ($reservation['payment_status'] === 'pending') { ?>
                    <a class="pay-button" href="<?= URL . "reservation/pay/" . $reservation['reservation_id'] ?>">
                        <?= $data['pay'] ?>
                    </a>
                <?php } ?>

                <?php if ($reservation['reservation_status'] === 'confirmed' && strtotime($reservation['travel_date_from']) > time()) { ?>
                    <a class="cancel-button" href="<?= URL . "reservation/cancel/" . $reservation['reservation_id'] ?>">
                        <?= $data['cancel'] ?>
                    </a>
                <?php } ?>

                <?php if ($reservation['has_comment'] === 0 && $reservation['reservation_status'] === 'completed') {

                    $accommodationId = isset($reservation['accommodation_id']) ? $reservation['accommodation_id'] : 'null';
                    $transportId = isset($reservation['transport_id']) ? $reservation['transport_id'] : 'null';
                    $packageId = isset($reservation['package_id']) ? $reservation['package_id'] : 'null';

                    $jsArray = "{accommodation_id: " . $accommodationId . ", transport_id: " . $transportId . ", package_id: " . $packageId . "}"
                ?>
                    <button class="comment-button" onclick="openRating(<?= $jsArray ?>)">
                        <?= $data['comment'] ?>
                    </button>
                <?php } ?>
            </div>
        </div>
    <?php
    }
    ?>

    <?php

    if ($notification) {
    ?>
        <div class="notification notification-<?= $notification ?>">
            <?= $data["notification"][$notification] ?>
        </div>
        <script>
            setTimeout(() => {
                document.querySelector(".notification").remove();
            }, 3000);
        </script>
    <?php
    }

    ?>

    <div class='comment-section section-hidden'>
        <form action="<?= URL ?>reservation/comment" method="POST">
            <input id="accommodation_id" class="inputId" type="hidden" name="accommodation_id" value="">
            <input id="transport_id" class="inputId" type="hidden" name="transport_id" value="">
            <input id="package_id" class="inputId" type="hidden" name="package_id" value="">
            <input type="number" hidden name="rating" id="inputRating">
            <div>
                <div id="divRating" class="rating">
                    <span id="spanRatingExcellent" title="Parfait" value="5">☆</span>
                    <span id="spanRatingGood" title="Très bien" value="4">☆</span>
                    <span id="spanRatingFair" title="Bien" value="3">☆</span>
                    <span id="spanRatingPoor" title="Mauvais" value="2">☆</span>
                    <span id="spanRatingAwful" title="Nul" value="1">☆</span>
                </div>
                <div id="descriptionRating">

                </div>
            </div>
            <textarea id="commentText" name="comment" placeholder="Écrivez votre avis ici..." required></textarea>
            <div class="buttons-form">
                <button type="submit" class="publish-button">Publier l'avis</button>
                <div class="cancel-button" onclick="closeRating()">Annuler</div>
            </div>
        </form>
    </div>
    <script>
        document.getElementById('divRating').addEventListener('click', function(event) {
            if (event.target.tagName.toLowerCase() != 'span') return;

            if (event.target.classList.contains('rated')) {
                event.target.classList.remove('rated');
            } else {
                Array.prototype.forEach.call(document.getElementsByClassName('rated'), function(el) {
                    el.classList.remove('rated');
                });
                event.target.classList.add('rated');
                document.getElementById("descriptionRating").innerText = event.target.title;
                document.getElementById("inputRating").value = event.target.getAttribute('value');
            }
        });

        function openRating(ids) {
            document.querySelectorAll(".inputId").forEach(element => {
                element.value = ids[element.id];
            });
            document.querySelector(".comment-section").classList.remove("section-hidden");
        }

        function closeRating() {
            document.getElementById("descriptionRating").innerText = "";
            document.getElementById("inputRating").value = "";
            if (document.querySelector(".rated")) {
                document.querySelector(".rated").classList.remove("rated");
            }
            document.getElementById("commentText").innerText = "";
            document.querySelectorAll(".inputId").forEach(element => {
                element.value = ""
            });
            document.querySelector(".comment-section").classList.add("section-hidden");

        }
    </script>
</section>