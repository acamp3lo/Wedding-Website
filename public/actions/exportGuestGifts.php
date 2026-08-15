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
    
    $db = getDatabaseConnection();

    if( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
        $session->addMessage('error', 'Invalid request method.');
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // Clear buffer to prevent any stray characters from corrupting the CSV
    if (ob_get_level()) ob_end_clean();

    // Fetch Guest Contrubutions data
    $stmt = $db->prepare('
        SELECT 
            gg.guest_name AS Guest_Name,
            g.name AS Gift_Name,
            gg.gift_value AS Contribution_Amount,
            gg.message AS Message,
            gg.gift_id AS Gift_ID
        FROM guest_gifts gg
        LEFT JOIN gifts g ON gg.gift_id = g.id;
    ');
    $stmt->execute();
    $firstRow = $stmt->fetch(PDO::FETCH_ASSOC);

    // Set Headers for Download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=guest_gifts_' . date('Y-m-d') . '.csv');

    // Create File Pointer
    $output = fopen('php://output', 'w');

    // Write UTF-8 BOM for Excel compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    $config = getConfig('config');
    // Write Headers and First Row
    if( $firstRow ) {
        fputcsv($output, array_keys($firstRow), $config['CSV_delimiter'] ?? ',', '"', "\\"); // Write headers
        fputcsv($output, $firstRow, $config['CSV_delimiter'] ?? ',', '"', "\\");             // Write the first row data
    }

    // Write Remaining Rows
    while( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
        fputcsv($output, $row, $config['CSV_delimiter'] ?? ',', '"', "\\");
    }

    fclose($output);
    exit;
?>