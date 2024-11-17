<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

?>

<section id="transport-section">
    <section class="filter-bar-container">
        <form id="filter-bar" method="POST" action="<?= URL ?>transport" class="filter-form">

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
                <label for="transport"><?= $data['search']['transport']['title'] ?> :</label>
                <select id="transport" name="transport">
                    <option value="" <?= empty($_POST['transport']) ? 'selected' : '' ?>><?= $data['search']['choose'] ?></option>
                    <option value="plane" <?= (isset($_POST['transport']) && $_POST['transport'] == 'plane') ? 'selected' : '' ?>><?= $data['search']['transport']['plane'] ?></option>
                    <option value="bus" <?= (isset($_POST['transport']) && $_POST['transport'] == 'bus') ? 'selected' : '' ?>><?= $data['search']['transport']['bus'] ?></option>
                    <option value="car" <?= (isset($_POST['transport']) && $_POST['transport'] == 'car') ? 'selected' : '' ?>><?= $data['search']['transport']['car'] ?></option>
                    <option value="train" <?= (isset($_POST['transport']) && $_POST['transport'] == 'train') ? 'selected' : '' ?>><?= $data['search']['transport']['train'] ?></option>
                </select>
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

    <div class="transport-container">
        <?php if ($transports) { ?>
            <?php foreach ($transports as $transport) : ?>
                <div class="transport-card">
                    <div class="transport-carousel">
                        <div class="carousel-images">
                            <?php

                            if ($transport['transport_type'] == "plane") {
                            ?>

                                <svg xmlns="http://www.w3.org/2000/svg" width="800px" height="800px" viewBox="0 0 24 24" fill="none">
                                    <g clip-path="url(#clip0_15_44)">
                                        <rect width="24" height="24" fill="white" />
                                        <path d="M19.3074 7.63582C19.3074 7.63582 20.4246 5.92462 19.364 4.86396C18.3033 3.8033 16.5921 4.92053 16.5921 4.92053L13.0566 8.45606L5.45753 6.04247L3.57191 7.92809L9.75674 11.7559L7.87112 13.6415L4.40158 13.9432L3.69448 14.6503L7.34315 16.8848L9.60589 20.5617L10.313 19.8546L10.5864 16.3568L12.472 14.4712L16.2998 20.656L18.1854 18.7704L15.7719 11.1714L19.3074 7.63582Z" stroke="#000000" stroke-linejoin="round" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_15_44">
                                            <rect width="24" height="24" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>

                            <?php
                            }

                            if ($transport['transport_type'] == "train") {
                            ?>

                                <svg xmlns="http://www.w3.org/2000/svg" width="800px" height="800px" viewBox="0 0 24 24" fill="none">
                                    <path d="M9.5 22H14.5M8 2H16M12 5V2M4 12H20M17 19L18.5 22M7 19L5.5 22M8.5 15.5H8.51M15.5 15.5H15.51M8.8 19H15.2C16.8802 19 17.7202 19 18.362 18.673C18.9265 18.3854 19.3854 17.9265 19.673 17.362C20 16.7202 20 15.8802 20 14.2V9.8C20 8.11984 20 7.27976 19.673 6.63803C19.3854 6.07354 18.9265 5.6146 18.362 5.32698C17.7202 5 16.8802 5 15.2 5H8.8C7.11984 5 6.27976 5 5.63803 5.32698C5.07354 5.6146 4.6146 6.07354 4.32698 6.63803C4 7.27976 4 8.11984 4 9.8V14.2C4 15.8802 4 16.7202 4.32698 17.362C4.6146 17.9265 5.07354 18.3854 5.63803 18.673C6.27976 19 7.11984 19 8.8 19ZM9 15.5C9 15.7761 8.77614 16 8.5 16C8.22386 16 8 15.7761 8 15.5C8 15.2239 8.22386 15 8.5 15C8.77614 15 9 15.2239 9 15.5ZM16 15.5C16 15.7761 15.7761 16 15.5 16C15.2239 16 15 15.7761 15 15.5C15 15.2239 15.2239 15 15.5 15C15.7761 15 16 15.2239 16 15.5Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>

                            <?php
                            }
                            if ($transport['transport_type'] == "car") {
                            ?>

                                <svg xmlns="http://www.w3.org/2000/svg" width="800px" height="800px" viewBox="0 0 24 24" fill="none">
                                    <path d="M3 8L5.72187 10.2682C5.90158 10.418 6.12811 10.5 6.36205 10.5H17.6379C17.8719 10.5 18.0984 10.418 18.2781 10.2682L21 8M6.5 14H6.51M17.5 14H17.51M8.16065 4.5H15.8394C16.5571 4.5 17.2198 4.88457 17.5758 5.50772L20.473 10.5777C20.8183 11.1821 21 11.8661 21 12.5623V18.5C21 19.0523 20.5523 19.5 20 19.5H19C18.4477 19.5 18 19.0523 18 18.5V17.5H6V18.5C6 19.0523 5.55228 19.5 5 19.5H4C3.44772 19.5 3 19.0523 3 18.5V12.5623C3 11.8661 3.18166 11.1821 3.52703 10.5777L6.42416 5.50772C6.78024 4.88457 7.44293 4.5 8.16065 4.5ZM7 14C7 14.2761 6.77614 14.5 6.5 14.5C6.22386 14.5 6 14.2761 6 14C6 13.7239 6.22386 13.5 6.5 13.5C6.77614 13.5 7 13.7239 7 14ZM18 14C18 14.2761 17.7761 14.5 17.5 14.5C17.2239 14.5 17 14.2761 17 14C17 13.7239 17.2239 13.5 17.5 13.5C17.7761 13.5 18 13.7239 18 14Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>

                            <?php
                            }
                            if ($transport['transport_type'] == "bus") {
                            ?>

                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                    <g id="SVGRepo_iconCarrier">
                                        <path d="M4 10C4 6.22876 4 4.34315 5.17157 3.17157C6.34315 2 8.22876 2 12 2C15.7712 2 17.6569 2 18.8284 3.17157C20 4.34315 20 6.22876 20 10V12C20 15.7712 20 17.6569 18.8284 18.8284C17.6569 20 15.7712 20 12 20C8.22876 20 6.34315 20 5.17157 18.8284C4 17.6569 4 15.7712 4 12V10Z" stroke="#000000" stroke-width="1.5"></path>
                                        <path d="M4 13H20" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M15.5 16H17" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M7 16H8.5" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M6 19.5V21C6 21.5523 6.44772 22 7 22H8.5C9.05228 22 9.5 21.5523 9.5 21V20" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M18 19.5V21C18 21.5523 17.5523 22 17 22H15.5C14.9477 22 14.5 21.5523 14.5 21V20" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M20 9H21C21.5523 9 22 9.44772 22 10V11C22 11.3148 21.8518 11.6111 21.6 11.8L20 13" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M4 9H3C2.44772 9 2 9.44772 2 10V11C2 11.3148 2.14819 11.6111 2.4 11.8L4 13" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M19.5 5H4.5" stroke="#000000" stroke-width="1.5" stroke-linecap="round"></path>
                                    </g>
                                </svg>

                            <?php
                            }

                            ?>
                        </div>
                    </div>

                    <div class="transport-description">
                        <div class="transport-sub-description">
                            <h3><?= htmlspecialchars($transport['provider_name']) ?></h3>
                            <div class="transport-details">
                                <p><?= $data['transports']['location'] ?>: <?= htmlspecialchars($transport['city']) ?></p>
                                <p><?= $data['transports']['seat'] ?>: <?= htmlspecialchars($transport['seat_available']) ?></p>
                                <p><?= $data['transports']['price'] ?>: <?= htmlspecialchars($transport['price']) ?> <?= CURRENCY ?></p>
                            </div>
                        </div>
                        <a href="<?= URL . "transport/show/" . $transport['transport_reference_id'] ?>" class="learn-more-btn"><?= $data['transports']['more'] ?></a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php } else { ?>
            <p><?= $data['no-transports'] ?></p>
        <?php } ?>
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
</section>