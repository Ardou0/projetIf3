<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

?>

<section>
    <section class="filter-bar-container">
        <form id="filter-bar" method="POST" action="<?= URL ?>travel" class="filter-form">

            <!-- Filtre par Prix -->
            <div class="filter-item">
                <label for="price-min"><?= $trad['search']['price'] ?> :</label>
                <input type="number" id="price-min" name="price_min" placeholder="Min" step="0.01"
                    value="<?= isset($_POST['price_min']) ? htmlspecialchars($_POST['price_min']) : '' ?>">
                <input type="number" id="price-max" name="price_max" placeholder="Max" step="0.01"
                    value="<?= isset($_POST['price_max']) ? htmlspecialchars($_POST['price_max']) : '' ?>">
            </div>

            <!-- Filtre par Lieu (Destination) -->
            <div class="filter-item">
                <label for="destination"><?= $trad['search']['place'] ?> :</label>
                <select id="destination" name="destination">
                    <option value="" <?= empty($_POST['destination']) ? 'selected' : '' ?>><?= $trad['search']['choose'] ?></option>
                    <?php

                    foreach ($destinations as $location) {
                    ?>

                        <option value="<?= $location['destination_id'] ?>" <?= (isset($_POST['destination']) && $_POST['destination'] == $location['destination_id']) ? 'selected' : '' ?>><?= $location['city'] ?></option>
                    <?php
                    }

                    ?>

                </select>
            </div>

            <!-- Filtre par Nombre de Personnes -->
            <div class="filter-item">
                <label for="people"><?= $trad['search']['people'] ?> :</label>
                <select id="people" name="people">
                    <option value="" <?= empty($_POST['people']) ? 'selected' : '' ?>><?= $trad['search']['choose'] ?></option>
                    <option value="seul" <?= (isset($_POST['people']) && $_POST['people'] == 'seul') ? 'selected' : '' ?>><?= $trad['search']['alone'] ?></option>
                    <option value="couple" <?= (isset($_POST['people']) && $_POST['people'] == 'couple') ? 'selected' : '' ?>><?= $trad['search']['couple'] ?></option>
                    <option value="famille" <?= (isset($_POST['people']) && $_POST['people'] == 'famille') ? 'selected' : '' ?>><?= $trad['search']['family'] ?></option>
                </select>
            </div>

            <!-- Filtre par Nombre d'Activités -->
            <div class="filter-item">
                <label for="activity-count"><?= $trad['search']['activities'] ?> :</label>
                <input type="number" id="activity-count" name="activity_count" placeholder="Min activités"
                    value="<?= isset($_POST['activity_count']) ? htmlspecialchars($_POST['activity_count']) : '' ?>">
            </div>

            <!-- Filtre par Durée en Jours -->
            <div class="filter-item">
                <label for="duration-min"><?= $trad['search']['duration'] ?> :</label>
                <input type="number" id="duration-min" name="duration_min" placeholder="Min jours"
                    value="<?= isset($_POST['duration_min']) ? htmlspecialchars($_POST['duration_min']) : '' ?>">
                <input type="number" id="duration-max" name="duration_max" placeholder="Max jours"
                    value="<?= isset($_POST['duration_max']) ? htmlspecialchars($_POST['duration_max']) : '' ?>">
            </div>

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



    <?php if (!empty($packages)): ?>
        <!-- Boucle sur les offres disponibles -->
        <?php foreach ($packages as $package): ?>
            <div class="offer">
                <div class="offer-cell">
                    <h3><?= htmlspecialchars($package['country']); ?></h3>
                    <p><?= htmlspecialchars($package['city']); ?></p>
                </div>
                <div class="offer-cell">
                    <h3><?= htmlspecialchars($package['description']); ?></h3>
                </div>
                <div class="offer-cell">
                    <p>Nombre personnes : <?= htmlspecialchars($package['max_occupants']); ?></p>
                </div>
                <div class="offer-cell">
                    <p>Activités (<?= count($package['activities']); ?>) :</p>
                    <ul>
                        <?php foreach ($package['activities'] as $activity): ?>
                            <li><?= htmlspecialchars($activity['activity_name']); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="offer-cell">
                    <p>Prix : <?= htmlspecialchars($package['price']); ?> €</p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-offers-message"><?= $trad['no-offers'] ?></p>
    <?php endif; ?>
</section>