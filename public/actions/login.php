<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../../src/utils/session.class.php');
    $session = new Session(false);

    require_once(__DIR__ . '/../../src/utils/bootstrap.php');

    $adminConfig = getConfig('admin_config');
    // Admin credentials
    // Change the $adminConfig['password_hash'] to a secure hash of your desired password in config/admin.json.
    // You can use "echo password_hash('YOUR_PASSWORD', PASSWORD_DEFAULT);" to get the hash of your password.
    $admin_password_hash = $adminConfig['password_hash'];
    // Change the $adminConfig['username'] to your desired admin username in config/admin.json.
    $admin_username = $adminConfig['username'];

    if( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
        $session->addMessage('error', 'Invalid request method.');
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    $user = $_POST['username'];
    $pass = $_POST['password'];

    if( $user === $admin_username && password_verify($pass, $admin_password_hash) ) {
        $session->login();
        $session->addMessage('success', 'Login successful!');
        header("Location: /pages/admin.php");
        exit();
    } else {
        $session->addMessage('error', 'Invalid credentials. Please try again.');
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
?>