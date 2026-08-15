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
    require_once(__DIR__ . '/../../src/utils/bootstrap.php');

    require_once(__DIR__ . '/../../src/utils/gift.class.php');

    $db = getDatabaseConnection();

    if( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
        $session->addMessage('error', 'Invalid request method.');
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // Clear buffer to prevent any stray characters from corrupting the CSV
    if (ob_get_level()) ob_end_clean();

    // Fetch Guest Confirmations data
    $stmt = $db->prepare('
        SELECT * FROM gifts;
    ');
    $stmt->execute();

    // Set Headers for Download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=gifts_' . date('Y-m-d') . '.csv');

    // Create File Pointer
    $output = fopen('php://output', 'w');

    // Write UTF-8 BOM for Excel compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // Fetch the first row to determine headers
    $firstRow = $stmt->fetch(PDO::FETCH_ASSOC);

    if( $firstRow ) {
        $config = getConfig('config');
        // Write Headers
        fputcsv($output, array_keys($firstRow), $config['CSV_delimiter'] ?? ',', '"', "\\"); // Write headers

        // Write the first row we just fetched
        fputcsv($output, $firstRow, $config['CSV_delimiter'] ?? ',', '"', "\\");

        // Stream the rest of the rows one by one.
        // This is much better for memory than fetchAll() if the list gets huge.
        while( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
            fputcsv($output, $row, $config['CSV_delimiter'] ?? ',', '"', "\\");
        }
    }

    fclose($output);
    exit;
?>