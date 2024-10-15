<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

?>

<section>
    <div class="forms">
        <div class="main-form">
            <div class="login-form form">
                <h1 onclick="rollMenu('login')"><?= $data['login']['button'] ?></h1>
                <form id="login" action="<?= URL ?>login" method="post" class="center-column">
                    <div class="selector">
                        <label for="account-type-login"><?= $data['register']['iam'] ?></label>
                        <select name="type" id="account-type-login" required>
                            <?php
                            foreach ($data['register']['type'] as $key => $value) {
                            ?>
                                <option value="<?= $key ?>"><?= $value ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>      
                    <input type="email" name="email" placeholder="<?= $data['login']['id'] ?>" required />
                    <input type="password" name="password" placeholder="<?= $data['login']['password'] ?>" required />
                    <div class="remember-me">
                        <input id="remember-me" type="checkbox" name="remember" />
                        <label for="remember-me"><?= $data['login']['remember'] ?></label>
                    </div>
                    <button class="btn btn-primary" type="submit">
                        <?= $data['login']['button'] ?>
                    </button>
                </form>
            </div>
            <div class="register-form form col hidden-form">
                <h1 onclick="rollMenu('sign')"><?= $data['register']['button'] ?></h1>
                <form id="signup" action="<?= URL ?>register" method="post" class="center-column">
                    <div class="selector">
                        <label for="account-type"><?= $data['register']['iam'] ?></label>
                        <select name="type" id="account-type" required>
                            <?php
                            foreach ($data['register']['type'] as $key => $value) {
                            ?>
                                <option value="<?= $key ?>"><?= $value ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <input type="text" name="firstname" placeholder="<?= $data['register']['firstname'] ?>" required />
                    <input id="name_input" type="text" name="name" placeholder="<?= $data['register']['name'] ?>" required />
                    <input type="email" name="email" placeholder="<?= $data['register']['id'] ?>" required />
                    <input type="password" name="password" placeholder="<?= $data['register']['password'] ?>" required />
                    <button class="btn btn-primary" type="submit">
                        <?= $data['register']['button'] ?>
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

        document.getElementById("account-type").addEventListener("input", () => {
            if (document.getElementById("account-type").value == "company") {
                document.getElementById("name_input").style.display = "none";
                document.getElementById("name_input").removeAttribute("required");
            } else {
                document.getElementById("name_input").value = "";
                document.getElementById("name_input").style.display = "block";
                document.getElementById("name_input").setAttribute("required", "")
            }
        })
    </script>
</section>