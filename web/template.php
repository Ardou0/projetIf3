<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');
?>

<!DOCTYPE html>
<html lang="<?= LANG ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Protest+Strike&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= URL ?>public/css/template.css">
    <link rel="stylesheet" href="<?= URL ?>public/css/header.css">
    <title><?= $title ?></title>
    <?= $css ?>
</head>
<body>
    <?= $header ?>
    <?= $content ?>
</body>
</html>