<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../../src/utils/session.class.php');
    $session = new Session(false);

    // Check if user is logged in
    if( !$session->isLoggedIn() ) {
        $session->addMessage('error', 'You do not have permission to access this page. Please login and try again.');
        header("Location: /pages/login.php");
        exit();
    }

    if( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
        $session->addMessage('error', 'Invalid request method.');
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    $session->logout();
    $session = new Session(false);
    $session->addMessage('success', 'Logout successful!');
    header("Location: /");
    exit();
?>