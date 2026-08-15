<?php

declare(strict_types = 1);

require_once(__DIR__ . '/../../src/utils/session.class.php');
$session = new Session(true);

// Check if user is logged in
if( !$session->isLoggedIn() ) {
    $session->addMessage('error', 'You dont have permission to access this page. Please login and try again.');
    header("Location: /pages/login.php");
    exit;
}

require_once(__DIR__ . '/../../src/database/connection.php');

require_once(__DIR__ . '/../../src/utils/guestGift.class.php');

require_once(__DIR__ . '/templates/common.php');

require_once(__DIR__ . '/../../src/icons/delete.svg.php');

require_once(__DIR__ . '/../../src/utils/bootstrap.php');

$config = getConfig('config');
if( !$config['enable_gift_list'] ) {
    $session->addMessage('error', 'Gift list functionality is disabled.');
    header("Location: /");
    exit;
}

$db = getDatabaseConnection();
$guestGifts = GuestGift::getAllGuestGifts($db);

if( $guestGifts === null || count($guestGifts) <= 0 ) {
    $session->addMessage('error', 'No contributions have been made yet.');
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
    exit;
}

drawHeader($session, 'Admin Panel', 'admin', $config);
drawBody($db, $guestGifts);
drawFooter($config);

?>


<?php function drawBody(PDO $db, array $guestGifts) { ?>
    <main>
        <h1>Admin Panel - Guest Contributions</h1>

        <div class="tableContainer">
            <table>
                <thead>
                    <tr>
                        <th scope="col">Contribution ID</th>
                        <th scope="col">Guest Name(s)</th>
                        <th scope="col">Gift ID</th>
                        <th scope="col">Gift Name</th>
                        <th scope="col">Contributed Value</th>
                        <th scope="col">Message</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach( $guestGifts as $contribution ) { ?>
                        <tr>
                            <td><?= $contribution->getId() ?></td>
                            <td><?= $contribution->getGuestName() ?></td>
                            <td><?= $contribution->getGiftId() ?></td>
                            <td><?= $contribution->getGiftName() ?></td>
                            <td><?= $contribution->getContribution() ?></td>
                            <td><?= $contribution->getMessage() ?></td>
                            <td >
                                <a onclick="openDeleteContributionModal(this)"
                                    data-contribution-id="<?= $contribution->getId() ?>" 
                                    data-guest-name="<?= $contribution->getGuestName() ?>"
                                    data-gift-name="<?= $contribution->getGiftName() ?>"
                                    data-contribution="<?= $contribution->getContribution() ?>"
                                    data-message="<?= $contribution->getMessage() ?>">
                                    <?= drawDeleteSVG(); ?>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <td colspan="6">Total number of contributions:</td>
                    <td><?php echo count($guestGifts); ?></td>
                </tfoot>
            </table>
        </div>
    </main>

    <aside id="deleteContributionModal" class="deleteModal">
        <h3>Are you sure you want to permanently delete this contribution?</h3>

        <section class="modalInfo">
            <p id="contributionInfoId"><b>Contribution ID:</b><span></span></p>
            <p id="contributionInfoGuestName"><b>Guest Name(s):</b><span></span></p>
            <p id="contributionInfoGiftName"><b>Gift:</b><span></span></p>
            <p id="contributionInfoContribution"><b>Contributed Value:</b><span></span></p>
            <p id="contributionInfoMessage"><b>Message:</b><span></span></p>
        </section>

        <form action="/actions/deleteGuestGift.php" method="POST">
            <input id="guestContributionId" type="hidden" name="contribution_id" required>
            <button type="button" onclick="closeDeleteContributionModal()">Cancel</button>
            <button type="submit">Yes</button>
        </form>
    </aside>
<?php } ?>