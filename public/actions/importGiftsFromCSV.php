<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../../src/utils/session.class.php');
    $session = new Session(true);

    require_once(__DIR__ . '/../../src/database/connection.php');
    require_once(__DIR__ . '/../../src/utils/deleteMissingGifts.php');
    require_once(__DIR__ . '/../../src/utils/bootstrap.php');

    $db = getDatabaseConnection();

    // Check if user is logged in
    if( !$session->isLoggedIn() ) {
        $session->addMessage('error', 'You do not have permission to access this page. Please log in and try again.');
        header("Location: /pages/login.php");
        exit;
    }

    if( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
        $session->addMessage('error', 'Invalid request method.');
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
        exit();
    }

    if( isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0 ) {
        $fileTmpPath = $_FILES['csv_file']['tmp_name'];
        
        if( ($handle = fopen($fileTmpPath, "r")) !== FALSE ) {
            // We use a transaction so that if the file is corrupted, we don't do a partial import
            $db->beginTransaction();

            try {                
                // Initialize contributed_value to 0 for NEW records
                $stmt = $db->prepare('
                    INSERT INTO gifts (id, name, total_value, contributed_value) 
                    VALUES (:id, :name, :total_value, 0)
                    ON DUPLICATE KEY UPDATE 
                        name = VALUES(name), 
                        total_value = VALUES(total_value)
                ');

                $lineNumber = 0;
                $importCount = 0;
                $delimiter = detectCsvDelimiter($fileTmpPath);
                $newGifts = array();

                while( ($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE ) {
                    $lineNumber++;
                    if ($lineNumber === 1) continue; // Skip header

                    // Basic validation: check if row has enough columns
                    if (count($row) < 3) continue;

                    $id    = (int)trim($row[0]);
                    $name  = trim($row[1]);
                    $value = (int)trim($row[2]);

                    array_push($newGifts, array(
                        'id' => $id,
                        'name' => $name,
                        'value' => $value
                    ));

                    $stmt->execute([
                        ':id'          => $id,
                        ':name'        => $name,
                        ':total_value' => $value
                    ]);
                    $importCount++;
                }

                deleteMissingGifts($db, $newGifts);

                $db->commit();
                $session->addMessage('success', "Import completed: $importCount items processed.");

            } catch( PDOException $e ) {
                $db->rollBack();
                
                // Friendly error message for the CHECK constraint
                if( str_contains($e->getMessage(), 'constraint') ) {
                    $session->addMessage('error', "Error on item $lineNumber: The total value of the gift cannot be smaller than the amount already contributed.");
                } else {
                    $session->addMessage('error', "Error on item $lineNumber: " . $e->getMessage());
                }
            } catch( RuntimeException $e ) {
                $db->rollBack();
                $session->addMessage('error', $e->getMessage());
            }

            fclose($handle);
        }
    } else {
        $session->addMessage('error', 'Error while loading file.');
    }

    header('Location: ' . '/index.php');


    function detectCsvDelimiter(string $path): string {
        $handle = fopen($path, 'r');
        if ($handle === false) {
            return ';';
        }

        $line = fgets($handle);
        fclose($handle);

        if ($line === false) {
            return ';';
        }

        return strpos($line, ';') !== false ? ';' : ',';
    }
?>