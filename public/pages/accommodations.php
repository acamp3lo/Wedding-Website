<?php

declare(strict_types = 1);

require_once(__DIR__ . '/../../src/utils/session.class.php');
$session = new Session(false);

require_once(__DIR__ . '/../../src/utils/accommodation.class.php');

require_once(__DIR__ . '/templates/common.php');

require_once(__DIR__ . '/../../src/utils/bootstrap.php');

$config = getConfig('config');
if( !$config['enable_accommodations'] ) {
    $session->addMessage('error', 'Accommodations functionality is disabled.');
    header("Location: /");
    exit;
}

$config = getConfig('config');
drawHeader($session, 'Where To Stay', 'accommodations', $config);
drawBody($config);
drawFooter($config);

?>


<?php function drawBody(array $config) { ?>
    <main>
        <h1>Where To Stay</h1>
        <?php foreach ($config['where_to_stay_message'] as $paragraph) { ?>
            <p><?= htmlspecialchars($paragraph) ?></p>
        <?php } ?>
        
        <section id="accommodationsGrid">
            <?php
            $accommodations = Accommodation::getAllAccommodations();
            foreach ($accommodations as $accommodation) { ?>
                <a href="<?= $accommodation->getLink() ?>" target="_blank" class="accommodation">
                    <p class="accommodationName"><?= $accommodation->getName() ?></p>
                    <p class="accommodationLocation">(<?= $accommodation->getLocation() ?>)</p>
                    <img src="/images/accomodations/<?= $accommodation->getImageName() ?>" alt="<?= $accommodation->getName() ?>">
                    <p><?= $accommodation->getDistance() ?></p>
                </a>
            <?php } ?>
        </section>
    </main>
<?php } ?>