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
    require_once(__DIR__ . '/../../src/utils/bootstrap.php');

    $db = getDatabaseConnection();

    if( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
        $session->addMessage('error', 'Invalid request method.');
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    if( isset($_POST['confirmation_id']) ) {
        $id = (int)$_POST['confirmation_id'];
    } else {
        $session->addMessage('error', 'Guest Confirmation ID is required.');
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    if( GuestConfirmation::deleteGuestConfirmation($db, $id) ) {
        $session->addMessage('success', 'Guest confirmation removed successfully!');
    } else {
        $session->addMessage('error', 'An error occurred while removing the guest confirmation. Please try again.');
    }

    header('Location: ' . $_SERVER['HTTP_REFERER']);
?>