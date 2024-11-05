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
            <form action="<?= URL ?>travel" method="POST" class="destination-form">
                <label for="destination"><?= $data['form']['go'] ?></label>
                <select name="destination" id="destination">
                    <?php
                    foreach ($destinations as $element) {
                    ?>
                        <option value="<?= $element['destination_id'] ?>"><?= $element['city'] . ", " . $element['country'] ?></option>
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
    <section><!-- HTML Interface for Travel Packages -->
        <h1>Packs Voyage</h1>
        <div class="package-container">
            <?php foreach ($packages as $package) : ?>
                <div class="package-card">
                    <div class="package-carousel" id="carousel-<?= $package['package_reference_id'] ?>">
                        <div class="carousel-images">
                            <?php
                            $activityImages = array_filter($activities, fn($activity) => $activity['package_reference_id'] === $package['package_reference_id']);
                            foreach ($activityImages as $index => $activity) : ?>
                                <img src="/images/<?= htmlspecialchars($activity['activity_photo']) ?>" alt="Image of <?= htmlspecialchars($activity['activity_name']) ?>" class="carousel-image <?= $index === 0 ? 'active' : '' ?>">
                            <?php endforeach; ?>
                        </div>
                        <?php if ($activityImages) : ?>
                            <button class="carousel-btn prev-btn" onclick="changeSlide(-1, 'carousel-<?= $package['package_reference_id'] ?>')">❮</button>
                            <button class="carousel-btn next-btn" onclick="changeSlide(1, 'carousel-<?= $package['package_reference_id'] ?>')">❯</button>
                        <?php endif; ?>
                    </div>

                    <div class="package-description">
                        <h3><?= htmlspecialchars($package['description']) ?></h3>
                        <p>Destination: <?= htmlspecialchars($package['city']) ?></p>
                        <p>Duration: <?= htmlspecialchars($package['duration']) ?> days</p>
                        <p>Price: <?= htmlspecialchars($package['price']) ?> <?= CURRENCY ?></p>

                        <h4><?= $activityImages ? 'Activities Included:' : 'No Activities Available' ?></h4>

                        <?php if ($activityImages) : ?>
                            <ul>
                                <?php foreach ($activityImages as $activity) : ?>
                                    <li>
                                        <p><strong><?= htmlspecialchars($activity['activity_name']) ?></strong>: <?= htmlspecialchars($activity['activity_description']) ?></p>
                                        <p>Duration: <?= htmlspecialchars($activity['duration_hours']) ?> hours</p>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <a href="#" class="learn-more-btn">BT en savoir plus</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
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
</section>