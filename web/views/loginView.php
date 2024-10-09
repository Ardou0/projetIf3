<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

?>

<section>
    <div class="forms">
        <div class="main-form">
            <div class="login-form form">
                <h1 onclick="rollMenu('login')">Connexion</h1>
                <form id="login" action="<?= URL ?>login" method="post" class="center-column">
                    
                    <button class="btn btn-primary" type="submit">
                        Connexion
                    </button>
                </form>
            </div>
            <div class="register-form form col hidden-form">
                <h1 onclick="rollMenu('sign')">Inscription</h1>
                <form id="signup" action="<?= URL ?>register" method="post" class="center-column">
                    
                    <button class="btn btn-primary" type="submit">
                        Inscription
                    </button>
                </form>
            </div>
        </div>
    </div>
    <script>
        function rollMenu(roll) {
            if (roll == "sign") {
                document.querySelector('.register-form').classList.remove('hidden-form');
                document.querySelector('.login-form').classList.add('hidden-form');
            }
            if (roll == "login") {
                document.querySelector('.login-form').classList.remove('hidden-form');
                document.querySelector('.register-form').classList.add('hidden-form');
            }
        }
    </script>
</section>