<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

?>


<section>
    <div class="content-header">
        <h1><?= $data['header']['title'] ?></h1>
        <h3><?= $data['header']['subtitle'] ?></h3>
    </div>
    <div class="main-search-bar">
        <form action="/search-destination.php" method="POST" class="destination-form">
            <label for="destination"><?= $data['form']['title'] ?></label>
            <select name="destination" id="destination">
                <?php
                    foreach($options as $element) {
                        ?>
                        <option value="<?= $element['city'] ?>"><?= $element['city']. ", ". $element['country'] ?></option>
                        <?php
                    }
                ?>
            </select>

            <button type="submit" class="search-button">Rechercher</button>
        </form>
    </div>
</section>