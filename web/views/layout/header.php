<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

?>

<header>
    <div class="logo">
        <div class="logo-container">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="800px" height="800px" viewBox="0 0 16 16" version="1.1" class="si-glyph si-glyph-airplane" fill="#000000">
                <g id="SVGRepo_iconCarrier">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <g transform="translate(1.000000, 0.000000)" fill="#ffffff">
                            <path d="M8.264,10.32 L11.784,15.8 C11.927,15.925 12.106,15.995 12.294,15.999 C12.499,16.001 12.69,15.925 12.825,15.792 C13.214,15.211 12.264,11.476 10.964,7.62 L8.264,10.32 L8.264,10.32 Z" class="si-glyph-fill"> </path>
                            <path d="M14.613,0.42 C14.029,-0.166 13.285,-0.105 12.785,0.393 L8.705,4.457 C6.268,3.681 1.859,2.182 0.195,2.182 C-0.0369999999,2.182 -0.139,2.208 -0.174,2.219 C-0.436,2.504 -0.442,2.941 -0.188,3.231 L5.919,7.231 L2.887,10.251 C2.887,10.251 0.548,9.769 0.206,9.724 C-0.271,9.662 -0.821,10.08 0.204,10.603 C1.401,11.212 2.803,11.92 2.803,11.92 C2.803,11.92 3.666,13.599 4.193,14.524 C4.864,15.643 5.258,15.1 5.178,14.524 C5.099,13.95 4.83,12.009 4.83,12.009 L7.714,9.017 L10.543,6.198 L14.569,2.187 C15.07,1.689 15.195,1.004 14.613,0.42 L14.613,0.42 Z" class="si-glyph-fill"> </path>
                        </g>
                    </g>
                </g>

            </svg>
        </div>
    </div>
    <div class="major-container">
        <div class="menu-button">
            <div class="bar1"></div>
            <div class="bar2"></div>
            <div class="bar3"></div>
        </div>
        <div class="global-menu-container">
            <div class="menu">
                <ul>
                    <?php
                    foreach ($data['menu'] as $key => $element) {
                    ?>
                        <li>
                            <a href="<?= URL . $key ?>" text="<?= $element ?>"><?= $element ?></a>
                        </li>
                    <?
                    }
                    ?>
                </ul>
            </div>
            <div class="account">
                <?php
                if (!isset($_SESSION['email'])) {
                ?>

                    <a href="<?= URL ?>login">
                        <div class="login-btn"><?= $data['login'] ?></div>
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
                                        <a href="<?= URL . $key ?>" text="<?= $value ?>"><?= $value ?></a>
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
        </div>
    </div>
    <script>
        document.querySelector(".menu-button").addEventListener("click", () => {
            document.querySelector(".menu-button").classList.toggle("change-menu");
            document.querySelector(".global-menu-container").classList.toggle("global-menu-container-active");
        });
        document.querySelector('.logo-container').addEventListener("click", () => {
            window.location = "<?= URL ?>";
        })
    </script>
</header>