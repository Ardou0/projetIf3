<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

if (!isset($method)) {
    $method = "new";
}
?>



<section id="accommodation-section">
    <h1><?= $data['title'] ?></h1>
    <form action="<?= URL ?>offer/<?= $method ?>/accommodation/<?= isset($accommodation) ? $accommodation['accommodation_reference_id'] : '' ?>" method="POST" enctype="multipart/form-data">
        <input type="text" name="name" value="accommodation" hidden>
        <select name="destination" required>
            <option value="" <?= empty($accommodation['destination_id']) ? 'selected' : '' ?>><?= $data['choose'] ?></option>

            <?php

            foreach ($destinations as $destination) {
            ?>
                <option value="<?= $destination['destination_id'] ?>" <?= (isset($accommodation['destination_id']) and $accommodation['destination_id'] == $destination['destination_id']) ? 'selected' : '' ?>><?= $destination['city'] ?></option>
            <?php
            }

            ?>
        </select>
        <input type="text" name="provider" required placeholder="<?= $data['provider'] ?>" <?= isset($accommodation['provider_name']) ? 'value="' . $accommodation['provider_name'] . '"' : '' ?>>

        <div id="previewImageContainer">
            <img id="preview-image" src="<?= (isset($accommodation['accommodation_photo']) && $accommodation['accommodation_photo'] != '' && file_exists(PATH . 'public/img/accommodations/' . $accommodation['accommodation_photo'])) ? URL . 'public/img/accommodations/' . $accommodation['accommodation_photo'] : URL . '' ?>" alt="">
        </div>
        <div class="personal-form-element">
            <div class="file-name">
                <?= $data['picture'] ?>
            </div>
            <input id="company_picture" name="picture" type="file" <?= (isset($accommodation['accommodation_photo']) && $accommodation['accommodation_photo'] != '' && file_exists(PATH . 'public/img/accommodations/' . $accommodation['accommodation_photo'])) ? '' : 'required' ?> accept="image/png, image/jpeg, image/jpg" hidden onchange="previewImage()">
        </div>
        <input type="text" name="room" required placeholder="<?= $data['room'] ?>" <?= isset($accommodation['room_type']) ? 'value="' . $accommodation['room_type'] . '"' : '' ?>>
        <input type="text" name="amenities" required placeholder="<?= $data['amenities'] ?>" <?= isset($accommodation['amenities']) ? 'value="' . $accommodation['amenities'] . '"' : '' ?>>
        <input type="number" name="occupants" required placeholder="<?= $data['occupants'] ?>" <?= isset($accommodation['max_occupants']) ? 'value="' . $accommodation['max_occupants'] . '"' : '' ?>>
        <input type="number" name="price" required placeholder="<?= $data['price'] ?>" <?= isset($accommodation['price_per_night']) ? 'value="' . $accommodation['price_per_night'] . '"' : '' ?>>
        <input type="submit" value="<?= $data['publish'] ?>">
    </form>
    <script>
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
                    const image = document.getElementById("preview-image");

                    image.addEventListener('load', function() {
                        imagePreviewContainer.innerHTML = '';
                        imagePreviewContainer.appendChild(image);
                        imagePreviewContainer.classList.add("previewed");
                        fileHandler.innerText = fileInput.files[0].name;
                    });

                    image.src = imageUrl;
                });

                reader.readAsDataURL(file);
            }
        }
    </script>
</section>