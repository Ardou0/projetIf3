<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

?>

<section>
    <h1><?= $data['error'] ?> : <?= $error ?></h1>
    <a href="<?= URL ?>"><?= $data['getSafe'] ?></a>
</section>