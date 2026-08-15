<?php

declare(strict_types = 1);


class GuestGift {
    private int $id;
    private string $guest_name;
    private int $gift_id;
    private string $gift_name;
    private int $contribution;
    private string $message;
    
    public function __construct(int $id, string $guest_name, int $gift_id, string $gift_name, int $contribution, string $message = '') {
        $this->id = $id;
        $this->guest_name = $guest_name;
        $this->gift_id = $gift_id;
        $this->gift_name = $gift_name;
        $this->contribution = $contribution;
        $this->message = $message;
    }

    /**
     * Retrieves all guest contributions with associated gift names.
     * 
     * @param PDO $db The database connection instance.
     * @return GuestGift[] An array of GuestGift objects.
     */
    static function getAllGuestGifts(PDO $db) : array {
        try {
            $stmt = $db->prepare('
                SELECT 
                    gg.id,
                    gg.guest_name,
                    gg.gift_id,
                    g.name AS gift_name,
                    gg.gift_value AS contribution,
                    gg.message
                FROM guest_gifts gg
                LEFT JOIN gifts g ON gg.gift_id = g.id;
            ');
            $stmt->execute();

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $guestGifts = [];

            foreach( $rows as $row ) {
                $guestGifts[] = new GuestGift(
                    (int)$row['id'],
                    $row['guest_name'],
                    (int)$row['gift_id'],
                    $row['gift_name'] ?? '',
                    (int)$row['contribution'],
                    $row['message'] ?? ''
                );
            }

            return $guestGifts;

        } catch (PDOException $e) {
            error_log("Database Error in getAllGuestGifts: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Records a guest gift contribution.
     * 
     * @return bool True if successful, false otherwise.
     */
    static function addGuestGift($db, $session, $guest_name, $gift_id, $value, $message = null) : bool {
        try {
            // We use a transaction because the trigger + check constraint are active
            $db->beginTransaction();

            $stmt = $db->prepare('
                INSERT INTO guest_gifts (guest_name, gift_id, gift_value, message) 
                VALUES (?, ?, ?, ?)
            ');
            
            $stmt->execute([
                htmlspecialchars($guest_name),
                $gift_id,
                $value,
                empty($message) ? null : htmlspecialchars($message)
            ]);

            $db->commit();
            $session->addMessage('success', 'Done! Thank you for your contribution!');
            return true;

        } catch (PDOException $e) {
            $db->rollBack();

            // Handle specific business logic errors
            if( str_contains($e->getMessage(), 'limit_check') ) {
                $session->addMessage('error', 'Sorry, someone just contributed to this gift and the remaining amount has changed.');
            } else {
                $session->addMessage('error', 'An error occurred. Please try again later.');
            }
            return false;
        }
    }

    /**
     * Delete a guest gift by ID.
     *
     * @param PDO $db Database connection.
     * @param int $id Contribution ID to delete.
     * @return bool True when a row was deleted, false otherwise.
     */
    static function deleteGuestGift(PDO $db, int $id) : bool {
        if ($id <= 0) {
            return false;
        }

        try {
            $db->beginTransaction();

            $stmt = $db->prepare('
                DELETE FROM guest_gifts
                WHERE id = ?;
            ');
            $stmt->execute([$id]);

            $rowsDeleted = $stmt->rowCount() > 0;

            $db->commit();
            return $rowsDeleted;

        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Database Error in deleteGuestGift: " . $e->getMessage());
            return false;
        }
    }
    
    public function getId() : int {
        return $this->id;
    }
    public function getGuestName() : string {
        return $this->guest_name;
    }
    public function getGiftId() : int {
        return $this->gift_id;
    }
    public function getContribution() : int {
        return $this->contribution;
    }
    public function getGiftName() : string {
        return $this->gift_name;
    }
    public function getMessage() : string {
        return $this->message;
    }
}

?>