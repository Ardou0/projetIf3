<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

if (!isset($method)) {
    $method = "new";
}
?>

<section id="package-section">
    <h1><?= $data['title'] ?></h1>
    <form action="<?= URL ?>offer/<?= $method ?>/package/<?= isset($package) ? $package['package_reference_id'] : '' ?>" method="POST" enctype="multipart/form-data">
        <input type="text" name="name" value="package" hidden>
        <select name="destination" required>
            <option value="" <?= empty($package['destination_id']) ? 'selected' : '' ?>><?= $data['choose'] ?></option>
            <?php
            foreach ($destinations as $destination) {
            ?>
                <option value="<?= $destination['destination_id'] ?>" <?= (isset($package['destination_id']) and $package['destination_id'] == $destination['destination_id']) ? 'selected' : '' ?>><?= $destination['city'] ?></option>
            <?php
            }
            ?>
        </select>
        <input type="number" name="transport" required placeholder="<?= $data['choose_transport'] ?>" value="<?= isset($package['transport_reference_id']) ? $package['transport_reference_id'] : '' ?>">
        <input type="number" name="accommodation" required placeholder="<?= $data['choose_accommodation'] ?>" value="<?= isset($package['accommodation_reference_id']) ? $package['accommodation_reference_id'] : '' ?>">
        <input type="text" name="itinerary" required placeholder="<?= $data['itinerary'] ?>" <?= isset($itinerary['schedule_description']) ? 'value="' . $itinerary['schedule_description'] . '"' : '' ?>>
        <input type="text" name="emergency_contact" required placeholder="<?= $data['emergency_contact'] ?>" <?= isset($itinerary['emergency_contact']) ? 'value="' . $itinerary['emergency_contact'] . '"' : '' ?>>
        <input type="number" name="duration" required placeholder="<?= $data['duration'] . " " . $data['days'] ?>" <?= isset($package['duration']) ? 'value="' . $package['duration'] . '"' : '' ?>>
        <textarea name="description" required placeholder="<?= $data['description'] ?>"><?= isset($package['description']) ? $package['description'] : '' ?></textarea>

        <div id="activities-section">
            <h2><?= $data['activities_title'] ?></h2>
            <div id="activities-container">
                <?php if (isset($activities) && is_array($activities)) {
                    foreach ($activities as $index => $activity) { ?>
                        <div class="activity-entry">
                            <input type="text" name="activity-<?= $index ?>-name" placeholder="<?= $data['activity_name'] ?>" value="<?= htmlspecialchars($activity['activity_name']) ?>">
                            <input type="text" name="activity-<?= $index ?>-description" placeholder="<?= $data['activity_description'] ?>" value="<?= htmlspecialchars($activity['activity_description']) ?>">
                            <input type="number" name="activity-<?= $index ?>-duration" placeholder="<?= $data['activity_duration'] ?>" value="<?= htmlspecialchars($activity['duration_hours']) ?>">
                            <input type="number" name="activity-<?= $index ?>-id" value="<?= $activity['activity_id'] ?>" hidden>
                            <div class="personal-form-element">
                                <div class="file-name" onclick="document.getElementById('activity-picture-<?= $index ?>').click();">
                                    <?= $data['picture'] ?>
                                </div>
                                <input id="activity-picture-<?= $index ?>" name="activity-<?= $index ?>-picture" type="file" <?= (isset($activity['activity_photo']) && !empty($activity['activity_photo'])) ? '' : 'required' ?> accept="image/png, image/jpeg, image/jpg" hidden onchange="previewActivityImage(<?= $index ?>)">
                                <div id="previewImageContainer-<?= $index ?>" class="img-container">
                                    <img id="preview-image-<?= $index ?>" class="preview-image" src="<?= (isset($activity['activity_photo']) && !empty($activity['activity_photo']) && file_exists(PATH . 'public/img/activities/' . $activity['activity_photo'])) ? URL . 'public/img/activities/' . $activity['activity_photo'] : URL . '' ?>" alt="">
                                </div>
                            </div>
                            <button type="button" class="remove-activity" onclick="removeActivity(this)"><?= $data['remove_activity'] ?></button>
                        </div>
                <?php }
                } ?>
            </div>
            <button type="button" id="add-activity-button"><?= $data['add_activity'] ?></button>
        </div>

        <input type="number" name="price" required placeholder="<?= $data['price'] ?>" <?= isset($package['price']) ? 'value="' . $package['price'] . '"' : '' ?>>
        <input type="submit" value="<?= $data['publish'] ?>">
    </form>
</section>

<script>
    // JavaScript for adding/removing activities dynamically
    const addActivityButton = document.getElementById('add-activity-button');
    const activitiesContainer = document.getElementById('activities-container');
    let activityIndex = <?= isset($activities) ? count($activities) : 0 ?>;

    addActivityButton.addEventListener('click', function() {
        if (activityIndex < 5) {

            const activityEntry = document.createElement('div');
            activityEntry.classList.add('activity-entry');
            activityEntry.innerHTML = `
            <input type="text" name="activity-${activityIndex}-name" placeholder="<?= $data['activity_name'] ?>">
            <input type="text" name="activity-${activityIndex}-description" placeholder="<?= $data['activity_description'] ?>">
            <input type="number" name="activity-${activityIndex}-duration" placeholder="<?= $data['activity_duration'] ?>">
            <input type="number" name="activity-${activityIndex}-id" hidden value="0">
            <div class="personal-form-element">
                <div class="file-name" onclick="document.getElementById('activity-picture-${activityIndex}').click();">
                    <?= $data['picture'] ?>
                </div>
                <input id="activity-picture-${activityIndex}" name="activity-${activityIndex}-picture" type="file" required accept="image/png, image/jpeg, image/jpg" hidden onchange="previewActivityImage(${activityIndex})">
                <div id="previewImageContainer-${activityIndex}" class="img-container">
                    <img id="preview-image-${activityIndex}" alt="" class="preview-image">
                </div>
            </div>
            <button type="button" class="remove-activity" onclick="removeActivity(this)"><?= $data['remove_activity'] ?></button>
        `;
            activitiesContainer.appendChild(activityEntry);
            activityIndex++;
        }
        else {
            alert("<?= $data['reached'] ?>");
        }
    });

    function removeActivity(button) {
        button.parentElement.remove();
    }

    function previewActivityImage(index) {
        const fileInput = document.getElementById(`activity-picture-${index}`);
        const file = fileInput.files[0];
        const imagePreviewContainer = document.getElementById(`previewImageContainer-${index}`);
        const image = document.getElementById(`preview-image-${index}`);

        if (file && file.type.match('image.*')) {
            const reader = new FileReader();

            reader.addEventListener('load', function(event) {
                const imageUrl = event.target.result;

                image.addEventListener('load', function() {
                    imagePreviewContainer.innerHTML = '';
                    imagePreviewContainer.appendChild(image);
                    imagePreviewContainer.classList.add("previewed");
                    fileInput.previousElementSibling.innerText = fileInput.files[0].name;
                });

                image.src = imageUrl;
            });

            reader.readAsDataURL(file);
        }
    }
</script>