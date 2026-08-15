<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../utils/gift.class.php');
    require_once(__DIR__ . '/../utils/guestConfirmation.class.php');
    require_once(__DIR__ . '/../utils/guestGift.class.php');
    require_once(__DIR__ . '/../database/connection.php');
    require_once(__DIR__ . '/../utils/bootstrap.php');

    $config = getConfig('config');

    if( !($config['enable_backup'] ?? false) ) {
        exit(0);
    }

    $backup_frequency = strtolower($config['backup_frequency'] ?? 'daily');
    $backup_intervals = [
        'hourly' => 3600,
        'daily' => 86400,
        'weekly' => 604800,
        'monthly' => 2592000,
    ];

    $backup_interval = $backup_intervals[$backup_frequency] ?? 86400;

    $backupFiles = glob(__DIR__ . '/../../backups/*_backup_*.csv');

    if( $backupFiles !== false && count($backupFiles) > 0 ) {
        $latestFile = null;
        $lastBackupTime = 0;
        foreach( $backupFiles as $file ) {
            $time = filemtime($file);
            if( $time > $lastBackupTime ) {
                $lastBackupTime = $time;
                $latestFile = $file;
            }
        }
        if ((time() - $lastBackupTime) < $backup_interval) {
            exit(0);    // too soon to run again
        }
    }

    $db = getDatabaseConnection();

    $gifts = Gift::getAllGifts($db);
    $guestConfirmations = GuestConfirmation::getAllGuestConfirmations($db);
    $guestGifts = GuestGift::getAllGuestGifts($db);

    createBackupFile($gifts, 'gifts', $config);
    createBackupFile($guestConfirmations, 'guest_confirmations', $config);
    createBackupFile($guestGifts, 'guest_gifts', $config);


    function createBackupFile(array $data, string $table_name, array $config) : void {
        if( empty($data) ) {
            return; // No data to backup
        }

        $backup_dir = __DIR__ . '/../../backups/';
        if( !is_dir($backup_dir) ) {
            mkdir($backup_dir, 0755, true);
        }

        // Unique filename using the table name and a timestamp
        $filename = $table_name . '_backup_' . date('d-m-Y_H-i-s') . '.csv';
        $filepath = $backup_dir . $filename;

        // Clear buffer to prevent any stray characters from corrupting the CSV
        if( ob_get_level() ) ob_end_clean();

        // GENERATE THE CSV FILE
        $file_handle = fopen($filepath, 'w');
        if( !$file_handle ) {
            error_log("Failed to create backup file at: " . $filepath);
            return;
        }
        // Write UTF-8 BOM for Excel compatibility
        fprintf($file_handle, chr(0xEF).chr(0xBB).chr(0xBF));

        // Extract headers from the keys of the very first data row
        $headers = array_keys((array) reset($data));
        fputcsv($file_handle, $headers, $config['CSV_delimiter'] ?? ',', '"', "\\");

        // Loop through the data array and write rows to CSV
        foreach( $data as $row ) {
            // Convert the object instance into an associative array
            $row_array = (array) $row;

            // Loop through every column and intercept boolean values
            foreach( $row_array as $key => $value ) {
                if( is_bool($value) ) {
                    // Convert true to 1 and false to 0 explicitly
                    $row_array[$key] = $value ? 1 : 0;
                }
            }
            // Write the sanitized array to the CSV
            fputcsv($file_handle, $row_array, $config['CSV_delimiter'] ?? ',', '"', "\\");
        }
        fclose($file_handle);
        

        // AUTOMATICALLY DELETE OLD BACKUPS
        $expiry_time = time() - ($config['backup_expiration_days'] * 24 * 60 * 60);

        // Find all CSV backup files matching this specific table prefix
        $files = glob($backup_dir . $table_name . '_backup_*.csv');
        
        if( is_array($files) ) {
            foreach( $files as $file ) {
                // Check if the file's modification time is older than the specified interval
                if( filemtime($file) < $expiry_time ) {
                    unlink($file); // Delete the expired file
                }
            }
        }
    }
?>