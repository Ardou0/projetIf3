<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<section id="dashboard-section">
    <div class="header-section">
        <div class="report">
            <h1><?= $data['reservation']['title'] ?></h1>
            <div class="confirmed-reservation">
                <div class="count-reservation">
                    <?= $summarize['confirmed_reservations_count'] ?>
                </div>
                <div class="title-count">
                    <?= $data['reservation']['confirmed'] ?>
                </div>
            </div>
            <div class="annex-reservation">
                <div class="pending-reservation">
                    <div class="count-reservation">
                        <?= $summarize['pending_reservations_count'] ?>
                    </div>
                    <div class="title-count">
                        <?= $data['reservation']['pending'] ?>
                    </div>
                </div>
                <div class="cancelled-reservation">
                    <div class="count-reservation">
                        <?= $summarize['cancelled_reservations_count'] ?>
                    </div>
                    <div class="title-count">
                        <?= $data['reservation']['cancelled'] ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="report">
            <h1><?= $data['payment']['title'] ?></h1>
            <canvas id="donut-finance" width="175px" height="175px"></canvas>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
            <script>
                var ctx = document.getElementById('donut-finance').getContext('2d');
                var myDonutChart = new Chart(ctx, {
                    type: 'doughnut', // Type de graphique : Donut
                    data: {
                        labels: ['<?= $data['payment']['received'] ?>', '<?= $data['payment']['pending'] ?>', '<?= $data['payment']['refunded'] ?>'],
                        datasets: [{
                            label: 'Payment Data',
                            data: [<?= $summarize['total_payment_received'] ?>, <?= $summarize['total_payment_pending'] ?>, <?= $summarize['total_payment_refunded'] ?>],
                            backgroundColor: [
                                'rgba(2, 52, 54, 0.7)',
                                'rgba(255, 136, 0, 0.7)',
                                'rgba(255, 60, 0, 0.7)'
                            ],
                            borderColor: [
                                'rgba(2, 52, 54, 1)',
                                'rgba(255, 136, 0, 1)',
                                'rgba(255, 60, 0, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    font: {
                                        family: 'Roboto',
                                        size: 12
                                    },
                                    color: 'rgba(0, 0, 0, 0.671)',
                                    boxWidth: 10,
                                    padding: 10
                                },
                            },

                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        let value = context.raw || 0;
                                        return label + ': ' + value.toFixed(2);
                                    }
                                }
                            },
                            datalabels: {
                                display: true,
                                color: 'black',
                                font: {
                                    family: 'Roboto',
                                    size: 12,
                                },
                                anchor: 'center',
                                align: 'center',
                                formatter: function(value, context) {
                                    return value.toFixed(2) + "<?= CURRENCY ?>";
                                }
                            }
                        }
                    },
                    plugins: [ChartDataLabels]
                });
            </script>
        </div>
        <div class="report">
            <h1><?= $data['rating']['title'] ?></h1>
            <div class="average-rating">
                <div class="count-reservation">
                    <?php
                    for ($i = 0; $i < $ratings['average_rating']; $i++) {
                    ?>
                        <span class="rate">☆</span>
                    <?php
                    }
                    ?>
                </div>
                <div class="title-count">
                    <?= $data['rating']['average'] ?>
                </div>
            </div>
            <div class="total-rating">
                <div class="count-reservation">
                    <?= $ratings['total_reviews'] ?>
                </div>
                <div class="title-count">
                    <?= $data['rating']['total'] ?>
                </div>
            </div>
        </div>
    </div>
    <div class="body-section">
        <h1><?= $data['clients']['title'] ?></h1>
        <table>
            <tbody>

                <tr class="first-line">
                    <td><?= $data['clients']['id'] ?></td>
                    <td><?= $data['clients']['first'] ?></td>
                    <td><?= $data['clients']['last'] ?></td>
                    <td><?= $data['clients']['phone'] ?></td>
                    <td><?= $data['clients']['email'] ?></td>
                    <td><?= $data['clients']['reservation'] ?></td>
                    <td><?= $data['clients']['status'] ?></td>
                    <td><?= $data['clients']['action']['title'] ?></td>
                </tr>
                <?php

                if (isset($clients)) {
                    foreach ($clients as $client) {
                ?>
                        <tr>
                            <td><?= $client['client_id'] ?></td>
                            <td><?= $client['first_name'] ?></td>
                            <td><?= $client['last_name'] ?></td>
                            <td><?= $client['phone_number'] ?></td>
                            <td><?= $client['email'] ?></td>
                            <td>
                                <?= $client['reservation_id'] ?>
                                <a class="more" href="<?= URL . "reservation/invoice/" . $client['reservation_id'] . "/" . $client['client_id'] ?>" text="<?= $data['clients']['action']['see'] ?>"><?= $data['clients']['action']['see'] ?></a>
                            </td>
                            <td><?= $client['status'] ?></td>
                            <td>
                                <?php

                                if ($client['status'] != "cancelled") {
                                ?>
                                    <a href="<?= URL . "reservation/cancel/" . $client['reservation_id'] . "/" . $client['client_id'] ?>" text="<?= $data['clients']['action']['cancel'] ?>"><?= $data['clients']['action']['cancel'] ?></a>
                                <?php
                                }

                                ?>
                            </td>
                        </tr>
                <?php
                    }
                }

                ?>
            </tbody>
        </table>
    </div>
    <div class="footer-section">
        <h1><?= $data['offers']['title'] ?></h1>
        <table>
            <tbody>

                <tr class="first-line">
                    <td><?= $data['offers']['id'] ?></td>
                    <td><?= $data['offers']['type'] ?></td>
                    <td><?= $data['offers']['destination'] ?></td>
                    <td><?= $data['offers']['name'] ?></td>
                    <td><?= $data['offers']['details'] ?></td>
                    <td><?= $data['offers']['price'] ?></td>
                    <td><?= $data['offers']['action']['title'] ?></td>
                </tr>
                <?php


                if (isset($packages)) {
                    foreach ($packages as $package) {
                ?>
                        <tr>
                            <td><?= $package['package_reference_id'] ?></td>
                            <td><?= $data['offers']['package'] ?></td>
                            <td><?= $package['city'] ?></td>
                            <td><?= $package['description'] ?></td>
                            <td>
                                <?php
                                if (isset($activities)) {
                                    foreach ($activities as $activity) {
                                        if ($activity["package_reference_id"] == $package['package_reference_id']) {
                                            echo $activity['activity_name'] . "<br>";
                                        }
                                    }
                                }
                                ?>
                            </td>
                            <td><?= $package['price'] ?></td>
                            <td>
                                <a href="<?= URL . "offer/delete/package/" . $package['package_reference_id'] ?>" text="<?= $data['offers']['action']['delete'] ?>"><?= $data['offers']['action']['delete'] ?></a>
                                <a href="<?= URL . "offer/edit/package/" . $package['package_reference_id'] ?>" text="<?= $data['offers']['action']['edit'] ?>"><?= $data['offers']['action']['edit'] ?></a>
                                <a href="<?= URL . "travel/show/" . $package['package_reference_id'] ?>" text="<?= $data['offers']['action']['see'] ?>"><?= $data['offers']['action']['see'] ?></a>
                            </td>
                        </tr>
                    <?php
                    }
                }

                if (isset($accommodations)) {
                    foreach ($accommodations as $accommodation) {
                    ?>
                        <tr>
                            <td><?= $accommodation['accommodation_reference_id'] ?></td>
                            <td><?= $data['offers']['accommodation'] ?></td>
                            <td><?= $accommodation['city'] ?></td>
                            <td><?= $accommodation['provider_name'] ?></td>
                            <td><?= $accommodation['amenities'] ?></td>
                            <td><?= $accommodation['price_per_night'] ?></td>
                            <td>
                                <a href="<?= URL . "offer/delete/accommodation/" . $accommodation['accommodation_reference_id'] ?>" text="<?= $data['offers']['action']['delete'] ?>"><?= $data['offers']['action']['delete'] ?></a>
                                <a href="<?= URL . "offer/edit/accommodation/" . $accommodation['accommodation_reference_id'] ?>" text="<?= $data['offers']['action']['edit'] ?>"><?= $data['offers']['action']['edit'] ?></a>
                                <a href="<?= URL . "accommodations/show/" . $accommodation['accommodation_reference_id'] ?>" text="<?= $data['offers']['action']['see'] ?>"><?= $data['offers']['action']['see'] ?></a>
                            </td>
                        </tr>
                    <?php
                    }
                }

                if (isset($transports)) {
                    foreach ($transports as $transport) {
                    ?>
                        <tr>
                            <td><?= $transport['transport_reference_id'] ?></td>
                            <td><?= $data['offers']['transport'] ?></td>
                            <td><?= $transport['city'] ?></td>
                            <td><?= $transport['provider_name'] ?></td>
                            <td><?= $transport['transport_type'] ?></td>
                            <td><?= $transport['price'] ?></td>
                            <td>
                                <a href="<?= URL . "offer/delete/transport/" . $transport['transport_reference_id'] ?>" text="<?= $data['offers']['action']['delete'] ?>"><?= $data['offers']['action']['delete'] ?></a>
                                <a href="<?= URL . "offer/edit/transport/" . $transport['transport_reference_id'] ?>" text="<?= $data['offers']['action']['edit'] ?>"><?= $data['offers']['action']['edit'] ?></a>
                                <a href="<?= URL . "transport/show/" . $transport['transport_reference_id'] ?>" text="<?= $data['offers']['action']['see'] ?>"><?= $data['offers']['action']['see'] ?></a>
                            </td>
                        </tr>
                <?php
                    }
                }

                ?>
            </tbody>
        </table>
        <div class="new-offers">
            <h1><?= $data['create'] ?></h1>
            <form action="<?= URL ?>offer/new" method="POST">
                <select name="type" required>
                    <option value="" selected><?= $data['search']['choose'] ?></option>
                    <option value="package"><?= $data['search']['package'] ?></option>
                    <option value="accommodation"><?= $data['search']['accommodation'] ?></option>
                    <option value="transport"><?= $data['search']['transport'] ?></option>
                </select>
                <input type="submit" value="<?= $data['offers']['new'] ?>">
            </form>
        </div>
    </div>
</section>