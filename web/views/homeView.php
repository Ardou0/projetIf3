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
    <section>
        <h1>Packs Voyage</h1>
        <div class="package-container">
            <?php foreach ($packages as $package) : ?>
                <div class="package-card">
                    <img src="/images/package_default.jpg" alt="Image of <?php echo htmlspecialchars($package['city']); ?>">
                    <div class="package-description">
                        <h3><?php echo htmlspecialchars($package['city']) . ', ' . htmlspecialchars($package['country']); ?></h3>
                        <p><?php echo htmlspecialchars($package['description']); ?></p>
                        <p>Duration: <?php echo htmlspecialchars($package['duration']); ?> days</p>
                        <p>Price: <?php echo htmlspecialchars($package['price']); ?> €</p>
                        <a href="#" class="learn-more-btn">BT en savoir plus</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</section>