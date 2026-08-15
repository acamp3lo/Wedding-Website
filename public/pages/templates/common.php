<?php
declare(strict_types=1);

require_once(__DIR__ . '/../../../src/icons/close.svg.php');
require_once(__DIR__ . '/../../../src/icons/admin.svg.php');
?>


<?php function drawHeader(Session $session, string $title, ?string $fileName, array $config) { ?>
    <!DOCTYPE html>
    <html lang="en-US">
        <head>
            <title><?= htmlspecialchars($config['bride_name']) ?> & <?= htmlspecialchars($config['groom_name']) ?> - <?= $title ?></title>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="icon" type="image/x-icon" href="/favicon.ico">
            <link rel="stylesheet" href="/css/common.css">
            <script src="/js/common.js" defer></script>
            <?php if( $fileName !== null ) { ?>
                <link rel="stylesheet" href="/css/<?= $fileName ?>.css">
                <script src="/js/<?= $fileName ?>.js" defer></script>
            <?php } ?>
        </head>

        <body>
            <header>
                <div class="logo">
                    <a href="/"><?= htmlspecialchars($config['bride_name']) ?> & <?= htmlspecialchars($config['groom_name']) ?></a>
                </div>
                <nav id="mainNav">
                    <button class="nav-toggle" aria-label="toggle navigation">
                        <span class="hamburger"></span>
                    </button>
                    <ul>
                        <li><a href="/">Homepage</a></li>
                        <?php if( $config['enable_accommodations'] ) { ?>
                            <li><a href="/pages/accommodations.php">Where to Stay</a></li>
                        <?php } ?>
                        <?php if( $config['enable_gift_list'] ) { ?>
                            <li><a href="/pages/guestGifts.php">Gift List</a></li>
                        <?php } ?>
                        <?php if( $config['enable_rsvp'] ) { ?>
                            <li><a id="rsvp-link" href="/pages/guestConfirmations.php">RSVP</a></li>
                        <?php } ?>
                    </ul>
                </nav>
            </header>

            <section id="messages">
                <div id="messagesContainer">
                    <?php foreach( $session->getMessages() as $message ) { ?>
                        <article class="<?=$message['type']?>">
                            <span><?=$message['text']?></span>
                            <div class="closeIcon"><?= drawCloseSVG() ?></div>
                        </article>
                    <?php } ?>
                </div>
            </section>
<?php } ?>

<?php function drawFooter(array $config) { ?>
            <footer>
                <div>
                    <p><?= htmlspecialchars($config['bride_name']) ?> & <?= htmlspecialchars($config['groom_name']) ?> - <?= htmlspecialchars($config['wedding_date']) ?></p>
                    <p>Contacts: <span><?= htmlspecialchars($config['bride_phone']) ?>, <?= htmlspecialchars($config['groom_phone']) ?></span></p>
                </div>
                <div>
                    <?php if( $config['enable_admin_link'] && ($config['enable_rsvp'] || $config['enable_gift_list']) ) { ?>
                        <a href="/pages/admin.php" class="svgContainer"><?= drawAdminSVG() ?></a>
                    <?php } ?>
                    <?php if( $config['enable_rsvp'] ) { ?>
                        <a href="/pages/guestConfirmations.php">RSVP</a>
                    <?php } ?>
                </div>
            </footer>
        </body>
    </html>
<?php } ?>