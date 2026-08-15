<?php

declare(strict_types = 1);

require_once(__DIR__ . '/../../src/utils/session.class.php');
$session = new Session(true);

// Check if user is logged in
if( !$session->isLoggedIn() ) {
    $session->addMessage('error', 'You do not have permission to access this page. Please log in and try again.');
    header("Location: /pages/login.php");
    exit;
}

require_once(__DIR__ . '/../../src/database/connection.php');

require_once(__DIR__ . '/../../src/utils/guestConfirmation.class.php');

require_once(__DIR__ . '/templates/common.php');

require_once(__DIR__ . '/../../src/icons/delete.svg.php');
require_once(__DIR__ . '/../../src/icons/true.svg.php');
require_once(__DIR__ . '/../../src/icons/false.svg.php');

require_once(__DIR__ . '/../../src/utils/bootstrap.php');

$config = getConfig('config');
if( !$config['enable_rsvp'] ) {
    $session->addMessage('error', 'RSVP functionality is disabled.');
    header("Location: /");
    exit;
}

$db = getDatabaseConnection();
$guestConfirmations = GuestConfirmation::getAllGuestConfirmations($db);

if( $guestConfirmations === null || count($guestConfirmations) <= 0 ) {
    $session->addMessage('error', 'No guest confirmations found.');
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
    exit;
}

drawHeader($session, 'Admin Panel - Guest Confirmations', 'admin', $config);
drawBody($db, $guestConfirmations);
drawFooter($config);

?>


<?php function drawBody(PDO $db, array $guestConfirmations) { ?>
    <main>
        <h1>Admin Panel - Guest Confirmations</h1>

        <div class="tableContainer">
            <table>
                <thead>
                    <tr>
                        <th scope="col">Confirmation ID</th>
                        <th scope="col">Guest Name</th>
                        <th scope="col">Attending?</th>
                        <th scope="col">Food Restrictions</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach( $guestConfirmations as $confirmation ) { ?>
                        <tr>
                            <td><?= $confirmation->getId() ?></td>
                            <td><?= $confirmation->getGuestName() ?></td>
                            <td>
                                <?php if( $confirmation->isAttending() ) {
                                    drawTrueSVG();
                                } else {
                                    drawFalseSVG();
                                } ?>
                            </td>
                            <td><?= $confirmation->getFoodRestrictions() ?></td>
                            <td ><a onclick="openDeleteConfirmationModal(this)"
                                    data-confirmation-id="<?= $confirmation->getId() ?>" 
                                    data-guest-name="<?= $confirmation->getGuestName() ?>"><?= drawDeleteSVG(); ?></a></td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <td colspan="4">Total Confirmations:</td>
                    <td><?php echo count($guestConfirmations); ?></td>
                </tfoot>
            </table>
        </div>
    </main>

    <aside id="deleteConfirmationModal" class="deleteModal">
        <h3>Are you sure you want to permanently delete this confirmation?</h3>

        <section class="modalInfo">
            <p id="confirmationInfoId"><b>Confirmation ID:</b><span></span></p>
            <p id="confirmationInfoGuestName"><b>Guest Name:</b><span></span></p>
        </section>

        <form action="/actions/deleteGuestConfirmation.php" method="POST">
            <input id="guestConfirmationId" type="hidden" name="confirmation_id" required>
            <button type="button" onclick="closeDeleteConfirmationModal()">Cancel</button>
            <button type="submit">Yes</button>
        </form>
    </aside>
<?php } ?>