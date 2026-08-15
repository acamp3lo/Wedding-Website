<?php

declare(strict_types = 1);

require_once(__DIR__ . '/../../src/utils/session.class.php');
$session = new Session(false);

require_once(__DIR__ . '/templates/common.php');

require_once(__DIR__ . '/../../src/icons/add.svg.php');
require_once(__DIR__ . '/../../src/icons/close.svg.php');

require_once(__DIR__ . '/../../src/utils/bootstrap.php');

$config = getConfig('config');
if( !$config['enable_rsvp'] ) {
    $session->addMessage('error', 'RSVP functionality is disabled.');
    header("Location: /");
    exit;
}

drawHeader($session, 'RSVP', 'guestConfirmations', $config);
drawBody($config);
drawFooter($config);

?>


<?php function drawBody(array $config) { ?>
    <main>
        <h1>RSVP</h1>
        <p>Please confirm your attendance by <?= htmlspecialchars($config['rsvp_deadline']) ?>.</p>

        <form action='/actions/addGuestConfirmation.php' method="POST">
            <section id="guestsInfo">
                <div class="guestInfo">
                    <button class="removeGuestButton" type="button" style="display: none;"><?= drawCloseSVG() ?></button>
                    <div>
                        <label for="guestName_1" class="guestNameLabel">Name:</label>
                        <input type="text" class="guestName" id="guestName_1" name="guest_name[]" placeholder="" required>
                    </div>
                    <div>
                        <label for="foodRestrictions_1" class="foodRestrictionsLabel">Food Restrictions (Optional):</label>
                        <input type="text" class="foodRestrictions" id="foodRestrictions_1" name="food_restrictions[]" placeholder="">
                    </div>
                </div>
                <button id="addGuestButton" type="button"><?= drawAddSVG() ?>Add Another Guest</button>
                <fieldset>
                    <div>
                        <input type="radio" id="confirmYes" name="confirmation" value="1" required>
                        <label for="confirmYes">Yes, I will be there!</label>
                    </div>
                    <div>
                        <input type="radio" id="confirmNo" name="confirmation" value="0">
                        <label for="confirmNo">Unfortunately, I won't be able to make it.</label>
                    </div>
                </fieldset>
                <input type="submit" value="Submit">
            </section>
        </form>
    </main>
<?php } ?>