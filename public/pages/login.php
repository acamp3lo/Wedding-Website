<?php

declare(strict_types = 1);

require_once(__DIR__ . '/../../src/utils/session.class.php');
$session = new Session(false);

require_once(__DIR__ . '/templates/common.php');

require_once(__DIR__ . '/../../src/utils/bootstrap.php');

$config = getConfig('config');
drawHeader($session, 'Admin Login', 'admin', $config);
drawBody();
drawFooter($config);

?>


<?php function drawBody() { ?>
    <main>
        <h1>Admin Login</h1>
        <form id="loginForm" action="/actions/login.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Log In</button>
        </form>
    </main>
<?php } ?>