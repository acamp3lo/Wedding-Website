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

    if( isset($_FILES['json_file']) && $_FILES['json_file']['error'] == 0 ) {
        $fileTmpPath = $_FILES['json_file']['tmp_name'];

        if( file_exists($fileTmpPath) ) {
            // We use a transaction so that if the file is corrupted, we don't do a partial import
            $db->beginTransaction();

            try {
                $jsonContents = file_get_contents($fileTmpPath);
                if( $jsonContents === false ) {
                    throw new RuntimeException('Unable to read gift_list.json.');
                }

                $data = json_decode($jsonContents, true, 512, JSON_THROW_ON_ERROR);
                $newGifts = $data['gifts'] ?? $data;

                if( !is_array($newGifts) ) {
                    throw new RuntimeException('The JSON file does not contain a valid gifts list.');
                }

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

                foreach( $newGifts as $gift ) {
                    $lineNumber++;

                    if( !is_array($gift) ) {
                        continue;
                    }

                    $id = isset($gift['id']) ? (int)$gift['id'] : 0;
                    $name = trim((string)($gift['name'] ?? ''));
                    $value = isset($gift['value']) ? (int)$gift['value'] : (isset($gift['total_value']) ? (int)$gift['total_value'] : 0);

                    if( $id <= 0 || $name === '' || $value <= 0 ) {
                        continue;
                    }

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

            } catch( JsonException $e ) {
                $db->rollBack();
                $session->addMessage('error', 'The gift list JSON is invalid: ' . $e->getMessage());
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
        } else {
            $session->addMessage('error', 'JSON file was not found.');
        }
    } else {
        $session->addMessage('error', 'Error while loading JSON file.');
    }

    header('Location: ' . '/index.php');
?>