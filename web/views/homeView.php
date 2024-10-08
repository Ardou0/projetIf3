<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

?>


<section>
    <div class="content-header">
        <div class="main-cover">
            <h1><?= $data['header']['title'] ?></h1>
            <h3><?= $data['header']['subtitle'] ?></h3>
        </div>

        <div class="main-search-bar">
            <h2><?= $data['form']['title'] ?></h2>
            <form action="/search-destination.php" method="POST" class="destination-form">
                <label for="destination"><?= $data['form']['go'] ?></label>
                <select name="destination" id="destination">
                    <?php
                    foreach ($options as $element) {
                    ?>
                        <option value="<?= $element['city'] ?>"><?= $element['city'] . ", " . $element['country'] ?></option>
                    <?php
                    }
                    ?>
                </select>

                <button type="submit" class="search-button"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                        <path d="M120-120v-80h720v80H120Zm70-200L40-570l96-26 112 94 140-37-207-276 116-31 299 251 170-46q32-9 60.5 7.5T864-585q9 32-7.5 60.5T808-487L190-320Z" />
                    </svg></button>
            </form>
        </div>
    </div>
    <div class="content-travel">
        <div class="latest-offers">
            <h1><?= $data["latest"]["title"] ?></h1>

            <div class="latest-package latest">
                <h2><?= $data["latest"]["package"]["title"] ?></h2>
                <div class="horizontal-container">
                    <?php

                    foreach ($package as $element) {
                        foreach ($options as $destination) {
                            if ($destination['destination_id'] == $element['destination_id']) {
                                $title = $destination['city'] . ", " . $destination['country'];
                            }
                        }
                    ?>
                        <div class="card package-card">
                            <h3><?= $title ?></h3>
                            <p><strong><?= $data['latest']['package']['duration'] ?>:</strong> <?= $element['duration'] . " " . $data['latest']['days'] ?></p>
                            <p><strong><?= $data['latest']['package']['price'] ?>:</strong> <?= $element['price'] . CURRENCY ?></p>
                            <p><strong><?= $data['latest']['package']['itinerary'] ?>:</strong> <?= $element['itinerary'] ?></p>
                            <button class="btn-book"><?= $data['latest']['book'] ?></button>
                        </div>

                    <?php
                    }

                    ?>
                </div>
            </div>

            <div class="latest-accommodation latest">
                <h2><?= $data["latest"]["accommodation"]["title"] ?></h2>
                <div class="horizontal-container">
                    <?php

                    foreach ($accommodation as $element) {
                        foreach ($options as $destination) {
                            if ($destination['destination_id'] == $element['destination_id']) {
                                $where = $destination['country'] . ", " . $destination['city'];
                            }
                        }
                    ?>
                        <div class="card accommodation-card">
                            <h3><?= $element['provider_name'] ?></h3>
                            <p><strong><?= $data['latest']['accommodation']['where'] ?>:</strong> <?= $where ?></p>
                            <p><strong><?= $data['latest']['accommodation']['type'] ?>:</strong> <?= $element['room_type'] ?></p>
                            <p><strong><?= $data['latest']['accommodation']['price'] ?>:</strong> <?= $element['price_per_night'] . CURRENCY ?></p>
                            <p><strong><?= $data['latest']['accommodation']['capacity'] ?>:</strong> <?= $element['max_occupants'] ?></p>
                            <p><strong><?= $data['latest']['accommodation']['amenities'] ?>:</strong> <?= $element['amenities'] ?></p>
                            <button class="btn-book"><?= $data['latest']['book'] ?></button>
                        </div>

                    <?php
                    }

                    ?>
                </div>
            </div>

            <div class="latest-transport latest">
                <h2><?= $data["latest"]["transport"]["title"] ?></h2>
                <div class="horizontal-container">
                    <?php

                    foreach ($transport as $element) {
                        foreach ($options as $destination) {
                            if ($destination['destination_id'] == $element['destination_id']) {
                                $where = $destination['country'] . ", " . $destination['city'];
                            }
                        }
                    ?>
                        <div class="card transport-card">
                            <h3><?= $element['provider_name']. " - " .$element['seat_available'] ?></h3>
                            <p><strong><?= $data['latest']['transport']['where'] ?>:</strong> <?= $where ?></p>
                            <p><strong><?= $data['latest']['transport']['type'] ?>:</strong> <?= $element['transport_type'] ?></p>
                            <p><strong><?= $data['latest']['transport']['price'] ?>:</strong> <?= $element['price'] . CURRENCY ?></p>
                            <p><strong><?= $data['latest']['transport']['ticket'] ?>:</strong> <?= $element['ticket_format'] ?></p>
                            <p><strong><?= $data['latest']['transport']['seat'] ?>:</strong> <?= $element['seat_available'] ?></p>
                            <button class="btn-book"><?= $data['latest']['book'] ?></button>
                        </div>

                    <?php
                    }

                    ?>
                </div>
            </div>

        </div>
    </div>

</section>