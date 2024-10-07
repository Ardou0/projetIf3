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
            <div class="latest-package">
                <h2><?= $data["latest"]["package"]["title"] ?></h2>
            </div>
            <div class="latest-accomodation">
                <h2><?= $data["latest"]["accomodation"]["title"] ?></h2>
            </div>
            <div class="latest-transport">
                <h2><?= $data["latest"]["transport"]["title"] ?></h2>
            </div>
        </div>
    </div>

</section>