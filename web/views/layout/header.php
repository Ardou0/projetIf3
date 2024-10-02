<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

?>

<header>
    <div class="logo">
        <img src="<?= URL ?>public/img/logo.png" alt="logo">
    </div>
    <div class="menu">
        <ul>
            <?php
            foreach ($data['menu'] as $key => $element) {
            ?>
                <li>
                    <a href="<?= URL . $key ?>"><?= $element ?></a>
                </li>
            <?
            }
            ?>
        </ul>
    </div>
    <div class="account">
        <?php
        if (!isset($_SESSION['user'])) {
        ?>

            <a href="<?= URL ?>login">
                <div class="login-button"><?= $data['login'] ?></div>
            </a>

        <?php
        } else {


        ?>
            <div class="profile">
                <div class="profile-name">
                    <?= $data['logged'] . " " . $_SESSION['username'] ?>
                </div>
                <div class="profile-menu">
                    <ul>
                        <?php
                        foreach ($data[$_SESSION['type']] as $key => $value) {
                        ?>
                            <li>
                                <a href="<?= URL . $key ?>"><?= $value ?></a>
                            </li>
                        <?php
                        }
                        ?>
                    </ul>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
</header>