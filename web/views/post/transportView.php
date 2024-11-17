<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

if(!isset($method)) {
    $method = "new";
}
?>

<section id="transport-section">
    <h1><?= $data['title'] ?></h1>
    <form action="<?= URL ?>offer/<?= $method ?>/transport/<?= isset($transport) ? $transport['transport_reference_id'] : '' ?>" method="POST">
        <input type="text" name="name" value="transport" hidden>
        <select name="type" required>
            <option value="" <?= empty($transport['transport_type']) ? 'selected' : '' ?>><?= $data['choose'] ?></option>
            <option value="plane" <?= (isset($transport['transport_type']) and $transport['transport_type'] == "plane") ? 'selected' : '' ?>><?= $data['transport']['plane'] ?></option>
            <option value="train" <?= (isset($transport['transport_type']) and $transport['transport_type'] == "train") ? 'selected' : '' ?>><?= $data['transport']['train'] ?></option>
            <option value="bus" <?= (isset($transport['transport_type']) and $transport['transport_type'] == "bus") ? 'selected' : '' ?>><?= $data['transport']['bus'] ?></option>
            <option value="car" <?= (isset($transport['transport_type']) and $transport['transport_type'] == "car") ? 'selected' : '' ?>><?= $data['transport']['car'] ?></option>
        </select>
        <select name="destination" required>
            <option value="" <?= empty($transport['destination_id']) ? 'selected' : '' ?>><?= $data['choose'] ?></option>

            <?php

            foreach ($destinations as $destination) {
            ?>
                <option value="<?= $destination['destination_id'] ?>" <?= (isset($transport['destination_id']) and $transport['destination_id'] == $destination['destination_id']) ? 'selected' : '' ?>><?= $destination['city'] ?></option>
            <?php
            }

            ?>
        </select>
        <input type="text" name="provider" required placeholder="<?= $data['provider'] ?>" <?= isset($transport['provider_name']) ? 'value="'.$transport['provider_name'].'"' : '' ?>>
        <input type="text" name="seat" required placeholder="<?= $data['seat'] ?>" <?= isset($transport['seat_available']) ? 'value="'.$transport['seat_available'].'"' : '' ?>>
        <input type="text" name="ticket" required placeholder="<?= $data['ticket'] ?>" <?= isset($transport['ticket_format']) ? 'value="'.$transport['ticket_format'].'"' : '' ?>>
        <input type="number" name="price" required placeholder="<?= $data['price'] ?>" <?= isset($transport['price']) ? 'value="'.$transport['price'].'"' : '' ?>>
        <input type="submit" value="<?= $data['publish'] ?>">
    </form>
</section>