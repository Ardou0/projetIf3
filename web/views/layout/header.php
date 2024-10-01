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
                foreach ($menu as $key => $element) {
                    ?>
                        <li>
                            <a href="<?= URL.$key ?>"><?= $element ?></a>
                        </li>
                    <?
                }
            ?>
        </ul>
    </div>
</header>