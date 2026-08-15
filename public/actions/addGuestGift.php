<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../../src/utils/session.class.php');
    $session = new Session(false);

    require_once(__DIR__ . '/../../src/database/connection.php');
    require_once(__DIR__ . '/../../src/utils/gift.class.php');
    require_once(__DIR__ . '/../../src/utils/guestGift.class.php');

    require_once(__DIR__ . '/../../src/utils/bootstrap.php');

    $db = getDatabaseConnection();

    if( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
        $session->addMessage('error', 'Invalid request method.');
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
        exit();
    }

    // Initial Validation
    $gift_id = filter_input(INPUT_POST, 'gift_id', FILTER_VALIDATE_INT);
    $value = filter_input(INPUT_POST, 'value', FILTER_VALIDATE_INT);
    $guest_name = trim($_POST['guest_name'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if( !$gift_id || !$value || empty($guest_name) ) {
        $session->addMessage('error', 'Please fill in all required fields.');
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // Initial PHP-side check (UX check)
    $remaining_value = Gift::getRemainingValueForGift($db, $gift_id);
    
    if( $value <= 0 ) {
        $session->addMessage('error', 'The value must be greater than zero.');
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    if( $value > $remaining_value ) {
        $session->addMessage('error', 'The value exceeds the remaining amount for this gift (' . $remaining_value . '€).');
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // Session messages handled inside the function
    GuestGift::addGuestGift($db, $session, $guest_name, $gift_id, $value, $message);

    header('Location: ' . $_SERVER['HTTP_REFERER']);
?>