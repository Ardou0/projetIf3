<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

?>


<section id="profile-section">
    <h1><?= $data['title'] ?></h1>
    <p class="  "><?= $data['description'] ?></p>
    <div class="login-data">
        <h3><?= $data["login"]["title"] ?></h3>
        <form id="login-form" action="<?= URL ?>profile/update/login" method="POST">
            <div class="login-form-element">
                <label for="email"><?= $data['login']['mail'] ?> :</label>
                <input id="email" name="email" type="email" value="<?= $_SESSION['email'] ?>" disabled>
            </div>
            <div class="login-form-element">
                <label for="oldpassword"><?= $data['login']['password'] ?> :</label>
                <input id="oldpassword" name="oldpass" type="password" value="**********" disabled>
            </div>
            <div class="login-form-element login-edit hidden-update-login">
                <label for="newpassword"><?= $data['login']['newer'] ?></label>
                <input id="newpassword" type="password" name="newpass" placeholder="<?= $data['typeHere'] ?>">
            </div>
            <input type="submit" value="<?= $data['update'] ?>">
        </form>
        <script>
            let editLogin = false;
            document.getElementById("login-form").addEventListener('submit', (e) => {
                if (!editLogin) {
                    e.preventDefault();
                    document.querySelector(".login-edit").classList.remove("hidden-update-login");
                    const inputs = document.getElementById("login-form").querySelectorAll('input');

                    // Parcourir les inputs et enlever l'attribut 'disabled'
                    inputs.forEach(input => {
                        if (input.hasAttribute('disabled')) {
                            input.removeAttribute('disabled');
                        }
                    });
                    editLogin = true
                }

            })
        </script>
    </div>
    <div class="personal-data">
        <h3><?= $data["personal"][$_SESSION['type']]["title"] ?></h3>

        <form id="personal-form" action="<?= URL ?>profile/update/data" method="POST">
            <?php

            if ($_SESSION['type'] == "client") {
            ?>
                <div class="personal-form-element">
                    <label for="first-name"><?= $data['personal']['client']['first-name'] ?> :</label>
                    <input id="first-name" name="first-name" type="text" value="<?= $_SESSION['first_name'] ?>" disabled>
                </div>
                <div class="personal-form-element">
                    <label for="last-name"><?= $data['personal']['client']['last-name'] ?> :</label>
                    <input id="last-name" name="last-name" type="text" value="<?= $_SESSION['last_name'] ?>" disabled>
                </div>
                <div class="personal-form-element">
                    <label for="phone"><?= $data['personal']['client']['phone'] ?> :</label>
                    <input id="phone" name="phone" type="text" <?= (isset($_SESSION['phone']) && $_SESSION['phone'] == '') ? 'value="' . $_SESSION['phone'] . '"' : 'placeholder="' . $data['typeHere'] . '"' ?>" disabled>
                </div>
                <div class="personal-form-element">
                    <label for="birth"><?= $data['personal']['client']['birth'] ?> :</label>
                    <input id="birth" name="birth" type="date" disabled>
                </div>
                <div class="personal-form-element">
                    <label for="preference"><?= $data['personal']['client']['preference'] ?> :</label>
                    <select id="preference" name="preference" disabled>
                        <option value="" selected><?= $data['search']['choose'] ?></option>
                        <option value="plane" <?= (isset($_POST['transport']) && $_POST['transport'] == 'plane') ? 'selected' : '' ?>><?= $data['search']['transport']['plane'] ?></option>
                        <option value="bus" <?= (isset($_POST['transport']) && $_POST['transport'] == 'bus') ? 'selected' : '' ?>><?= $data['search']['transport']['bus'] ?></option>
                        <option value="car" <?= (isset($_POST['transport']) && $_POST['transport'] == 'car') ? 'selected' : '' ?>><?= $data['search']['transport']['car'] ?></option>
                        <option value="train" <?= (isset($_POST['transport']) && $_POST['transport'] == 'train') ? 'selected' : '' ?>><?= $data['search']['transport']['train'] ?></option>
                    </select>
                </div>
            <?php
            }
            if ($_SESSION['type'] == "company") {
            ?>
                <div class="personal-form-element">
                    <label for="name"><?= $data['personal']['company']['name'] ?> :</label>
                    <input id="name" name="full_name" type="text" value="<?= $_SESSION['full_name'] ?>" disabled>
                </div>
                <div id="previewImageContainer">

                </div>
                <div class="personal-form-element">
                    <label for="company_picture"><?= $data['personal']['company']['picture'] ?> :</label>
                    <div class="file-name file-name-disabled">
                        <?= $data['personal']['company']['select'] ?>
                    </div>
                    <input id="company_picture" name="picture" type="file" accept="image/png, image/jpeg, image/jpg" disabled hidden onchange="previewImage()">
                </div>
            <?php
            }

            ?>
            <input type="submit" value="<?= $data['update'] ?>">
        </form>
        <script>
            let editPersonal = false;
            document.getElementById("personal-form").addEventListener('submit', (e) => {
                if (!editPersonal) {
                    e.preventDefault();
                    const inputs = document.getElementById("personal-form").querySelectorAll('input');
                    if (document.querySelector(".file-name")) {
                        document.querySelector(".file-name").classList.remove("file-name-disabled")
                    }
                    if (document.getElementById("preference")) {
                        document.getElementById("preference").removeAttribute("disabled")
                    }
                    // Parcourir les inputs et enlever l'attribut 'disabled'
                    inputs.forEach(input => {
                        if (input.hasAttribute('disabled')) {
                            input.removeAttribute('disabled');
                        }
                    });
                    editPersonal = true
                }

            })

            const fileHandler = document.querySelector('.file-name');

            fileHandler.addEventListener('click', () => {
                document.getElementById("company_picture").click();
            })

            function previewImage() {
                const fileInput = document.getElementById('company_picture');
                const file = fileInput.files[0];
                const imagePreviewContainer = document.getElementById('previewImageContainer');

                if (file.type.match('image.*')) {
                    const reader = new FileReader();

                    reader.addEventListener('load', function(event) {
                        const imageUrl = event.target.result;
                        const image = new Image();

                        image.addEventListener('load', function() {
                            imagePreviewContainer.innerHTML = ''; // Vider le conteneur au cas où il y aurait déjà des images.
                            imagePreviewContainer.appendChild(image);
                            imagePreviewContainer.classList.add("previewed");
                            fileHandler.innerText = fileInput.files[0].name;
                        });

                        image.src = imageUrl;
                        image.style.width = 'auto'; // Indiquez les dimensions souhaitées ici.
                        image.style.height = '100%'; // Vous pouvez également utiliser "px" si vous voulez spécifier une hauteur.
                    });

                    reader.readAsDataURL(file);
                }
            }
        </script>
    </div>

</section>