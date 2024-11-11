<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

?>
<section>
    <?php
    foreach ($reservations as $reservation) {
    ?>
        <div class="reservation">
            <h2><?php echo htmlspecialchars($reservation['destination_city']) . " vacation package with flights and hotel"; ?></h2>
            <div class="description">
                <p><strong><?= $data['destination'] ?>:</strong> <?php echo htmlspecialchars($reservation['destination_city']) . ", " . htmlspecialchars($reservation['destination_country']); ?></p>
                <p><strong><?= $data['duration'] ?>:</strong> <?php echo htmlspecialchars($reservation['duration']); ?> jours</p>
                <p><strong><?= $data['price'] ?>:</strong> <?php echo number_format($reservation['total_price'], 2); ?> €</p>
                <p><strong><?= $data['date'] ?>:</strong> Du <?php echo htmlspecialchars($reservation['travel_date_from']); ?> au <?php echo htmlspecialchars($reservation['travel_date_to']); ?></p>
                <p class="<?= $reservation['reservation_status'] ?>"><strong><?= $data['reservation'] ?>:</strong> <?php echo ucfirst($reservation['reservation_status']); ?></p>
                <p class="<?= $reservation['payment_status'] ?>"><strong><?= $data['payment'] ?>:</strong> <?php echo ucfirst($reservation['payment_status']); ?></p>
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
</section>