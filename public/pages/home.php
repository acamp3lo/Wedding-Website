<?php
  declare(strict_types = 1);

  require_once(__DIR__ . '/../../src/utils/session.class.php');
  $session = new Session(false);

  require_once(__DIR__ . '/templates/common.php');

  require_once(__DIR__ . '/../../src/icons/maps.svg.php');

  require_once(__DIR__ . '/../../src/utils/ceremony.svg.php');
  require_once(__DIR__ . '/../../src/utils/venue.svg.php');
  require_once(__DIR__ . '/../../src/utils/bootstrap.php');

  $config = getConfig('config');
  drawHeader($session, 'Homepage', 'home', $config);
  drawBody($config);
  drawFooter($config);
?>

<?php function drawBody(array $config) { ?>
    <main>
        <header>
            <div>
                <?php foreach ($config['header_message'] as $paragraph) { ?>
                    <p><?= htmlspecialchars($paragraph) ?></p>
                <?php } ?>
            </div>
            <div>
                <p><?= htmlspecialchars($config['bride_name']) ?></p>
                <p>&</p>
                <p><?= htmlspecialchars($config['groom_name']) ?></p>
            </div>
            <div>
                <p><?= htmlspecialchars($config['wedding_date']) ?></p>
                <p><?= htmlspecialchars($config['wedding_location']) ?></p>
            </div>
        </header>

        <section id="initialMessage" class="revealOnScroll">
            <?php foreach ($config['homepage_initial_message'] as $paragraph) { ?>
                <p><?= htmlspecialchars($paragraph) ?></p>
            <?php } ?>
        </section>

        <section id="mainInfo">
            <header class="revealOnScroll">
                <h2>About</h2>
                <p><?= htmlspecialchars($config['wedding_date']) ?></p>
            </header>

            <section id="introductionSection" class="revealOnScroll">
                <?php foreach ($config['about_message'] as $paragraph) { ?>
                    <p><?= htmlspecialchars($paragraph) ?></p>
                <?php } ?>
            </section>

            <section id="ceremonySection" class="revealOnScroll">
                <?php
                if( htmlspecialchars($config['homepage_image_format']) === 'svg' ) {
                    echo drawCeremonySVG();
                } else {
                    echo '<img id="ceremonyImage" src="/images/ceremony.' . htmlspecialchars($config['homepage_image_format']) . '" alt="Ceremony Image" />';
                }
                ?>
                <div>
                    <div class="locationInfo">
                        <h3>Ceremony</h3>
                        <h2><?= htmlspecialchars($config['ceremony_location']) ?></h2>
                        <a href="<?= htmlspecialchars($config['ceremony_location_link']) ?>" target="_blank"><?= drawMapsSVG() ?></a>
                    </div>
                </div>
            </section>

            <section id="venueSection" class="revealOnScroll">
                <div>
                    <div class="locationInfo">
                        <h3>Venue</h3>
                        <h2><?= htmlspecialchars($config['venue_location']) ?></h2>
                        <a href="<?= htmlspecialchars($config['venue_location_link']) ?>" target="_blank"><?= drawMapsSVG() ?></a>
                    </div>
                </div>
                <?php
                if( htmlspecialchars($config['homepage_image_format']) === 'svg' ) {
                    echo drawVenueSVG();
                } else {
                    echo '<img id="venueImage" src="/images/venue.' . htmlspecialchars($config['homepage_image_format']) . '" alt="Venue Image" />';
                }
                ?>
            </section>

            <section id="dressCodeSection" class="revealOnScroll">
                <div id="dressCodeInfo">
                    <h3>Dress Code: <span><?= htmlspecialchars($config['dress_code']) ?></span></h3>
                </div>
            </section>
            
            <?php if( $config['enable_accommodations'] ) { ?>
                <section id="accommodationsInfo" class="revealOnScroll">
                    <h3><a href="/pages/accommodations.php">Where to Stay</a>:</h3>
                    <?php foreach ($config['where_to_stay_message'] as $paragraph) { ?>
                        <p><?= htmlspecialchars($paragraph) ?></p>
                    <?php } ?>
                <a href="/pages/accommodations.php">View Accommodations</a>
                </section>
            <?php } ?>
        </section>
    </main>
<?php } ?>