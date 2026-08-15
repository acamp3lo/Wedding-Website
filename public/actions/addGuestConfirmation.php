<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../../src/utils/session.class.php');
    $session = new Session(false);

    require_once(__DIR__ . '/../../src/database/connection.php');

    require_once(__DIR__ . '/../../src/utils/bootstrap.php');

    $db = getDatabaseConnection();

    if( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
        $session->addMessage('error', 'Invalid request method.');
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    if( isset($_POST['guest_name']) && is_array($_POST['guest_name']) ) {
        $names = $_POST['guest_name'];
    } else {
        $session->addMessage('error', 'Guest names are required and must be an array.');
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    if( isset($_POST['food_restrictions']) && is_array($_POST['food_restrictions']) ) {
        $foodRestrictions = $_POST['food_restrictions'];
    } else {
        $session->addMessage('error', 'Food restrictions must be an array.');
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
    for( $i = 0; $i < count($names); $i++ ) {
        // Sanitize strings and trim whitespaces
        $clean_name = htmlspecialchars(trim($names[$i]));
        $clean_restriction = htmlspecialchars(trim($foodRestrictions[$i] ?? ''));

        if( empty($clean_name) ) {
            $session->addMessage('error', 'The guest name cannot be empty. Please try again.');
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }

        $stmt = $db->prepare('
            INSERT INTO guest_confirmations (guest_name, food_restrictions, is_attending) VALUES (?, ?, ?)
        ');
        $wasCreated = $stmt->execute(array(
            $clean_name,
            $clean_restriction,
            isset($_POST['confirmation']) && $_POST['confirmation'] === '1' ? 1 : 0
        ));

        if( $wasCreated ) {
            $session->addMessage('success', 'Submitted successfully, thank you!');
        } else {
            $session->addMessage('error', 'An error occurred while submitting the confirmation. Please try again.');
        }
    }

    header('Location: ' . $_SERVER['HTTP_REFERER']);
?>