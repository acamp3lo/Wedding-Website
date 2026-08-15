<?php

declare(strict_types = 1);

require_once(__DIR__ . '/../../src/utils/session.class.php');
$session = new Session(true);

// Check if user is logged in
if( !$session->isLoggedIn() ) {
    $session->addMessage('error', 'You do not have permission to access this page. Please login and try again.');
    header("Location: /pages/login.php");
    exit();
}

require_once(__DIR__ . '/../../src/database/connection.php');

require_once(__DIR__ . '/templates/common.php');

require_once(__DIR__ . '/../../src/utils/bootstrap.php');

$config = getConfig('config');
if( !$config['enable_rsvp'] && !$config['enable_gift_list'] ) {
    $session->addMessage('error', 'Admin panel is disabled.');
    header("Location: /");
    exit();
}

$db = getDatabaseConnection();

$config = getConfig('config');
drawHeader($session, 'Admin Panel', 'admin', $config);
drawBody($db, $config);
drawFooter($config);

?>


<?php function drawBody(PDO $db, array $config) { ?>
    <main>
        <h1>Admin Panel</h1>
        <section class="adminPanelSection" id="importSection">
            <h2>Import</h2>
            <?php if( $config['enable_gift_list'] ) { ?>
                <p><strong>Caution:</strong> Importing a gift list will overwrite the existing one.</p>
                <form id="importCSVForm" action="/actions/importGiftsFromCSV.php" method="POST" enctype="multipart/form-data">
                    <input type="file" name="csv_file" accept=".csv" required>
                    <button type="submit">Import Gift List (CSV)</button>
                </form>
                <form action="/actions/importGiftsFromJson.php" method="POST">
                    <button type="submit">Import Gift List from gift_list.json</button>
                </form>
            <?php } else { ?>
                <p>Nothing to show. Gift list functionality is disabled.</p>
            <?php } ?>
        </section>

        <section class="adminPanelSection">
            <h2>Export</h2>
            <?php if( $config['enable_rsvp'] ) { ?>
                <form action="/actions/exportConfirmations.php" method="POST">
                    <input type="submit" value="Export Guest Confirmations (CSV)" />
                </form>
            <?php } ?>
            <?php if( $config['enable_gift_list'] ) { ?>
                <form action="/actions/exportGuestGifts.php" method="POST">
                    <input type="submit" value="Export Guest Gifts (CSV)" />
                </form>
                <form action="/actions/exportGifts.php" method="POST">
                    <input type="submit" value="Export Gift List (CSV)" />
                </form>
            <?php } ?>
        </section>
        
        <section class="adminPanelSection">
            <h2>Edit</h2>
            <?php if( $config['enable_rsvp'] ) { ?>
                <a href="/pages/guestConfirmationsAdmin.php">Edit Guest Confirmations</a>
            <?php } ?>
            <?php if( $config['enable_gift_list'] ) { ?>
                <a href="/pages/guestGiftsAdmin.php">Edit Guest Gifts</a>
            <?php } ?>
        </section>

        <form id="logoutForm" action="/actions/logout.php" method="POST">
            <input type="submit" value="Log Out" />
        </form>
    </main>
<?php } ?>