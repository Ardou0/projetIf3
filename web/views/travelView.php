<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

?>

<section id="travel-content">
    <section class="filter-bar-container">
        <form id="filter-bar" method="POST" action="<?= URL ?>travel" class="filter-form">

            <!-- Filtre par Prix -->
            <div class="filter-item">
                <label for="price-min"><?= $data['search']['price'] ?> :</label>
                <input type="number" id="price-min" name="price_min" placeholder="Min" step="0.01"
                    value="<?= isset($_POST['price_min']) ? htmlspecialchars($_POST['price_min']) : '' ?>">
                <input type="number" id="price-max" name="price_max" placeholder="Max" step="0.01"
                    value="<?= isset($_POST['price_max']) ? htmlspecialchars($_POST['price_max']) : '' ?>">
            </div>

            <!-- Filtre par Lieu (Destination) -->
            <div class="filter-item">
                <label for="destination"><?= $data['search']['place'] ?> :</label>
                <select id="destination" name="destination">
                    <option value="" <?= empty($_POST['destination']) ? 'selected' : '' ?>><?= $data['search']['choose'] ?></option>
                    <?php

                    foreach ($destinations as $location) {
                    ?>

                        <option value="<?= $location['destination_id'] ?>" <?= (isset($_POST['destination']) && $_POST['destination'] == $location['destination_id']) ? 'selected' : '' ?>><?= $location['city'] ?></option>
                    <?php
                    }

                    ?>

                </select>
                <label for="destination"><?= $data['search']['continent']['title'] ?> :</label>
                <select id="continent" name="continent_name">
                    <option value="" <?= empty($_POST['continent']) ? 'selected' : '' ?>><?= $data['search']['choose'] ?></option>
                    <?php
                    $continents = ['eu', 'na', 'sa', 'af', 'as', 'oc'];
                    foreach ($continents as $location) {
                    ?>
                        <option value="<?= htmlspecialchars($location) ?>" <?= (isset($_POST['continent_name']) && $_POST['continent_name'] == $location) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($data['search']['continent'][$location]) ?>
                        </option>
                    <?php
                    }
                    ?>
                </select>

            </div>

            <!-- Filtre par Nombre de Personnes -->
            <div class="filter-item">
                <label for="people"><?= $data['search']['people'] ?> :</label>
                <select id="people" name="people">
                    <option value="" <?= empty($_POST['people']) ? 'selected' : '' ?>><?= $data['search']['choose'] ?></option>
                    <option value="seul" <?= (isset($_POST['people']) && $_POST['people'] == 'seul') ? 'selected' : '' ?>><?= $data['search']['alone'] ?> (1)</option>
                    <option value="couple" <?= (isset($_POST['people']) && $_POST['people'] == 'couple') ? 'selected' : '' ?>><?= $data['search']['couple'] ?> (2)</option>
                    <option value="famille" <?= (isset($_POST['people']) && $_POST['people'] == 'famille') ? 'selected' : '' ?>><?= $data['search']['family'] ?> (3+)</option>
                </select>
            </div>

            <!-- Filtre par Nombre d'Activités -->
            <div class="filter-item">
                <label for="activity-count"><?= $data['search']['activities'] ?> :</label>
                <input type="number" id="activity-count" name="activity_count" placeholder="Min activités"
                    value="<?= isset($_POST['activity_count']) ? htmlspecialchars($_POST['activity_count']) : '' ?>">
            </div>

            <!-- Filtre par Durée en Jours -->
            <div class="filter-item">
                <label for="date-min"><?= $data['search']['date']['departure'] ?> :</label>
                <input type="date" id="date-min" name="date_min" placeholder="Départ"
                    value="<?= isset($_POST['date_min']) ? htmlspecialchars($_POST['date_min']) : '' ?>">
                <label for="date-min"><?= $data['search']['date']['return'] ?> :</label>
                <input type="date" id="date-max" name="date_max" placeholder="Retour"
                    value="<?= isset($_POST['date_max']) ? htmlspecialchars($_POST['date_max']) : '' ?>">
            </div>
            <script>
                let date = new Date();
                date.setDate(date.getDate() + 1);
                document.getElementById("date-min").min = date.toISOString().split('T')[0]
                document.getElementById("date-max").min = date.toISOString().split('T')[0];
            </script>

            <!-- Bouton de soumission avec icône SVG -->
            <button type="submit" class="search-button">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#FFFFFF" viewBox="0 0 297 297" width="20" height="20">
                    <path d="M156.215,53.603c-13.704-13.704-31.924-21.251-51.302-21.251c-19.38,0-37.6,7.548-51.302,21.251
                    c-3.855,3.855-3.855,10.105,0,13.96c3.856,3.854,10.104,3.854,13.96,0c9.974-9.975,23.236-15.469,37.342-15.469
                    c14.106,0,27.368,5.494,37.342,15.469c20.59,20.59,20.59,54.094,0,74.685c-3.855,3.855-3.855,10.105,0,13.96
                    c1.928,1.927,4.454,2.891,6.98,2.891s5.052-0.964,6.98-2.891C184.503,127.92,184.503,81.891,156.215,53.603z" />
                    <path d="M289.054,250.651l-93.288-93.288c23.145-40.108,17.591-92.372-16.674-126.637C159.278,10.912,132.933,0,104.913,0
                    c-28.022,0-54.365,10.912-74.18,30.727C10.918,50.542,0.007,76.884,0.007,104.906c0,28.021,10.912,54.365,30.727,74.179
                    c20.452,20.451,47.315,30.676,74.179,30.676c18.145,0,36.287-4.672,52.456-14.003l93.289,93.287
                    c5.127,5.129,11.946,7.954,19.197,7.954c7.253,0,14.071-2.824,19.198-7.953C299.639,278.461,299.639,261.238,289.054,250.651z
                    M104.913,190.029c-21.806-0.003-43.619-8.303-60.219-24.904c-16.086-16.085-24.945-37.471-24.945-60.219
                    s8.859-44.134,24.945-60.219c16.085-16.086,37.471-24.945,60.219-24.945c22.748,0,44.133,8.859,60.219,24.945
                    c33.205,33.205,33.205,87.233,0,120.438C148.528,181.729,126.724,190.031,104.913,190.029z" />
                </svg>
            </button>
        </form>
    </section>



    <div class="package-container">
        <?php if ($packages) { ?>
            <?php foreach ($packages as $package) : ?>
                <div class="package-card">
                    <div class="package-carousel" id="carousel-<?= $package['package_reference_id'] ?>">
                        <div class="carousel-images">
                            <?php
                            $activityImages = array_filter($package['activities'], fn($activity) => $activity['package_reference_id'] === $package['package_reference_id']);
                            if (count($activityImages) <= 1) {
                                $showCarousel = 0;
                            } else {
                                $showCarousel = 1;
                            }
                            $firstElement = true;
                            if (count($activityImages) >= 1) {
                                foreach ($activityImages as $activity) { ?>
                                    <img src="<?= URL . "public/img/activities/" . htmlspecialchars($activity['activity_photo']) ?>"
                                        alt="Image of <?= htmlspecialchars($activity['activity_name']) ?>"
                                        class="carousel-image <?= $firstElement ? 'active' : '' ?>">
                                    <?php $firstElement = false; ?>
                                <?php
                                }
                            } else {
                                ?>
                                <img src="<?= URL . "public/img/accommodations/" . htmlspecialchars($package['accommodation_photo']) ?>"
                                    alt="Image of <?= htmlspecialchars($package['provider_name']) ?>"
                                    class="carousel-image active">
                            <?php
                            }
                            ?>
                        </div>
                        <?php if ($showCarousel) : ?>
                            <button class="carousel-btn prev-btn" onclick="changeSlide(-1, 'carousel-<?= $package['package_reference_id'] ?>')">❮</button>
                            <button class="carousel-btn next-btn" onclick="changeSlide(1, 'carousel-<?= $package['package_reference_id'] ?>')">❯</button>
                        <?php endif; ?>
                    </div>

                    <div class="package-description">
                        <h3><?= htmlspecialchars($package['description']) ?></h3>
                        <div class="package-advantages">
                            <p><?= $data['packs']['destination'] ?>: <?= htmlspecialchars($package['city']) ?></p>
                            <p><?= $data['packs']['duration'] ?>: <?= htmlspecialchars($package['duration']) ?> <?= $data['packs']['days'] ?></p>
                            <p><?= $data['packs']['price'] ?>: <?= htmlspecialchars($package['price']) ?> <?= CURRENCY ?></p>
                            <p><?= $data['packs']['transport']['title'] ?>: <?= $data['packs']['transport'][htmlspecialchars($package['transport_type'])] ?></p>

                            <div>

                                <h4><?= $activityImages ? $data['packs']['activities'] . ':' : '' ?></h4>
                                <?php if ($activityImages) : ?>
                                    <ul>
                                        <?php foreach ($activityImages as $activity) : ?>
                                            <li>
                                                <p><strong><?= htmlspecialchars($activity['activity_name']) ?></strong></p>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>

                        <a href="<?= URL . "travel/show/" . $package['package_reference_id'] ?>" class="learn-more-btn"><?= $data['packs']['more'] ?></a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php
        } else {
        ?>
            <p><?= $data['no-offers'] ?></p>
        <?php
        }
        ?>
    </div>

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

    <script>
        function changeSlide(direction, carouselId) {
            const carousel = document.getElementById(carouselId);
            const images = carousel.querySelectorAll('.carousel-image');
            let currentIndex = Array.from(images).findIndex(img => img.classList.contains('active'));

            images[currentIndex].classList.remove('active');
            currentIndex = (currentIndex + direction + images.length) % images.length;
            images[currentIndex].classList.add('active');
        }
    </script>
</section>