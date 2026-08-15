<?php

declare(strict_types = 1);


class GuestConfirmation {
    private int $id;
    private string $guest_name;
    private string $food_restrictions;
    private bool $is_attending;
    
    public function __construct(int $id, string $guest_name, string $food_restrictions, bool $is_attending = false) {
        $this->id = $id;
        $this->guest_name = $guest_name;
        $this->food_restrictions = $food_restrictions;
        $this->is_attending = $is_attending;
    }

    // Returns an array of all Guest Confirmations objects in the database
    static function getAllGuestConfirmations(PDO $db) : array {
        // Fetch Guest Confirmations data
        $stmt = $db->prepare('
            SELECT id, guest_name, food_restrictions, is_attending
            FROM guest_confirmations;
        ');
        $stmt->execute();

        $guestConfirmations = array();
        while( $confirmation = $stmt->fetch(PDO::FETCH_ASSOC) ) {
            $guestConfirmations[] = new GuestConfirmation(
                (int)$confirmation['id'],
                $confirmation['guest_name'],
                $confirmation['food_restrictions'],
                (bool)$confirmation['is_attending']
            );
        }
        return $guestConfirmations;
    }

    //
    /**
     * Delete a guest confirmation by ID.
     *
     * @param PDO $db Database connection.
     * @param int $id Confirmation ID to delete.
     * @return bool True when a row was deleted, false otherwise.
     */
    static function deleteGuestConfirmation(PDO $db, int $id) : bool {
        if( $id <= 0 ) {
            return false;
        }

        $stmt = $db->prepare('
            DELETE FROM guest_confirmations
            WHERE id = ?;
        ');
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }
    
    public function getId() : int {
        return $this->id;
    }
    public function getGuestName() : string {
        return $this->guest_name;
    }
    public function getFoodRestrictions() : string {
        return $this->food_restrictions;
    }
    public function isAttending() : bool {
        return $this->is_attending;
    }
}

?>