<?php

declare(strict_types = 1);

require_once(__DIR__ . '/../../src/utils/session.class.php');
$session = new Session(false);

require_once(__DIR__ . '/../../src/database/connection.php');

require_once(__DIR__ . '/../../src/utils/gift.class.php');
require_once(__DIR__ . '/../../src/utils/giftIcon.class.php');

require_once(__DIR__ . '/../../src/icons/close.svg.php');

require_once(__DIR__ . '/templates/common.php');

require_once(__DIR__ . '/../../src/utils/bootstrap.php');

$config = getConfig('config');
if( !$config['enable_gift_list'] ) {
    $session->addMessage('error', 'Gift list functionality is disabled.');
    header("Location: /");
    exit;
}

$db = getDatabaseConnection();
$gifts = Gift::getAllGifts($db);

$config = getConfig('config');
drawHeader($session, 'Gift List', 'guestGifts', $config);
drawBody($db, $gifts, $config);
drawFooter($config);

?>


<?php 

function drawBody(PDO $db, array $gifts, array $config) { ?>
    <main>
        <h1>Gift List</h1>
        <?php foreach ($config['gift_list_message'] as $paragraph) { ?>
            <p><?= htmlspecialchars($paragraph) ?></p>
        <?php } ?>

        <section id="giftsGrid">
            <?php foreach($gifts as $gift) {
                $total_value = $gift->getTotalValue();
                $contributed_value = $gift->getContributedValue($db);
                $remaining_value = $gift->getRemainingValue($db); ?>
                <article class="gift">
                    <?php if( $total_value > 9999 ) { ?>
                        <p class="giftName">
                            <?php echo htmlspecialchars($gift->getName()); ?>
                        </p>
                        <?= drawGiftIcon($gift->getId(), $config) ?>
                        <p>
                            <?php
                            echo number_format($contributed_value, 0) . " / " . "&#8734; €";
                            ?>
                        </p>
                    <?php } else { ?>
                        <p class="giftName">
                            <?php
                            echo htmlspecialchars($gift->getName());
                            ?>
                        </p>
                        <?= drawGiftIcon($gift->getId(), $config) ?>
                        <p>
                            <?php
                            echo number_format($contributed_value, 0) . " / " . number_format($total_value, 0) . " €";
                            ?>
                        </p>
                        <progress value="<?= $contributed_value ?>" max="<?= $total_value ?>"></progress>
                    <?php } ?>
                    <div class="giftActions">
                        <?php if( $remaining_value > 0 ) {
                            if( $total_value > 9999 ) { ?>
                                <button style="margin-top: 1.5rem;" data-gift-id="<?= $gift->getId() ?>" data-remaining-value="<?= $remaining_value ?>" data-gift-name="<?= htmlspecialchars($gift->getName()) ?>" onclick="openGuestGiftModal(this, 0)">Contribute</button>
                            <?php } else if( $remaining_value === $total_value ) { ?>
                                <button data-gift-id="<?= $gift->getId() ?>" data-remaining-value="<?= $remaining_value ?>" data-gift-name="<?= htmlspecialchars($gift->getName()) ?>" onclick="openGuestGiftModal(this, 0)">Offer a portion</button>
                                <button data-gift-id="<?= $gift->getId() ?>" data-remaining-value="<?= $remaining_value ?>" data-gift-name="<?= htmlspecialchars($gift->getName()) ?>" onclick="openGuestGiftModal(this, 1)">Offer complete</button>
                            <?php } else { ?>
                                <button data-gift-id="<?= $gift->getId() ?>" data-remaining-value="<?= $remaining_value ?>" data-gift-name="<?= htmlspecialchars($gift->getName()) ?>" onclick="openGuestGiftModal(this, 0)">Offer a portion</button>
                            <?php } ?>
                        <?php } else { ?>
                            <p>Complete</p>
                        <?php } ?>
                    </div>
                </article>
            <?php } ?>
        </section>
    </main>
    <aside id="guestGiftModal">
        <header>
            <h2>Offer a Gift</h2>
            <button class="closeBtn" onclick="closeGuestGiftModal()"><?= drawCloseSVG() ?></button>
        </header>
        <h3 id="itemName"></h3>
        <section class="bankInfo">
            <p><strong>IBAN: </strong>0000 0000 0000 0000 0000 0000 0</p>
        </section>
        <?php if( count($config['gift_list_instructions']) > 0 ) { ?>
            <section class="instructions">
                <?php foreach ($config['gift_list_instructions'] as $paragraph) { ?>
                    <p><?= htmlspecialchars($paragraph) ?></p>
                <?php } ?>
            </section>
        <?php } ?>
        <form id="guestGiftForm" action='/actions/addGuestGift.php' method="POST">
            <section class="guestInfo">
                <div>
                    <label for="guestName" class="guestNameLabel">Name/s:</label>
                    <input type="text" class="guestName" id="guestName" name="guest_name" placeholder="" required>
                </div>
                <div>
                    <label for="value">Value:</label>
                    <input type="number" class="value" id="value" name="value" min="0" step="1" required>
                </div>
                <div>
                    <label for="message">Message to the couple (Optional):</label>
                    <textarea id="message" class="message" name="message" rows="5"></textarea>
                </div>
            </section>
            <input id="giftId" type="hidden" name="gift_id" required>
            <input type="submit" value="Submit">
        </form>
    </aside>

    <aside id="confirmationModal">
        <h3>Are you sure you want to make this contribution?</h3>

        <div class="contributionInfo">
            <p id="contributionInfoGuestName"><b>Name/s of the contributor/s:</b><span></span></p>
            <p id="contributionInfoGiftName"><b>Item:</b><span></span></p>
            <p id="contributionInfoContribution"><b>Contributed Value:</b><span></span></p>
            <p id="contributionInfoMessage"><b>Message to the couple:</b><span></span></p>
        </div>

        <div class="confirmationActions">
            <button type="button" onclick="closeConfirmationModal()">Cancel</button>
            <button type="button" onclick="submitGuestGiftFormFromConfirmation()">Submit</button>
        </div>
    </aside>
<?php } ?>


<?php

function drawGiftIcon(int $gift_id, array $config) : string {
    if( htmlspecialchars($config['gift_list_image_format']) === 'svg' ) {
        return GiftIcon::drawIcon($gift_id);
    } else {
        return '<img class="giftImage" src="/images/gifts/' . $gift_id . '.' . htmlspecialchars($config['gift_list_image_format']) . '" alt="Gift Image" />';
    }
}

?>