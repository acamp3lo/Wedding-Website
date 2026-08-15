<?php

declare(strict_types = 1);


class Gift {
    private int $id;
    private string $name;
    private int $total_value;
    private int $contributed_value;
    
    public function __construct(int $id, string $name, int $total_value, int $contributed_value = 0) {
        $this->id = $id;
        $this->name = $name;
        $this->total_value = $total_value;
        $this->contributed_value = $contributed_value;
    }

    // Returns a Gift object by its ID, or null if not found
    static function getGiftById(PDO $db, int $id) : ?Gift {
        $stmt = $db->prepare('
            SELECT id, name, total_value, contributed_value
            FROM gifts
            WHERE id = ?
        ');
        $stmt->execute(array($id));

        $gift = $stmt->fetch(PDO::FETCH_ASSOC);
        if( $gift ) {
            return new Gift(
                (int)$gift['id'],
                $gift['name'],
                (int)$gift['total_value'],
                (int)$gift['contributed_value']
            );
        } else {
            return null;
        }
    }

    // Returns an array of all Gift objects in the database
    static function getAllGifts(PDO $db) : array {
        $stmt = $db->prepare('
            SELECT id, name, total_value, contributed_value
            FROM gifts
        ');
        $stmt->execute();

        $gifts = array();
        while( $gift = $stmt->fetch(PDO::FETCH_ASSOC) ) {
            $gifts[] = new Gift(
                (int)$gift['id'],
                $gift['name'],
                (int)$gift['total_value'],
                (int)$gift['contributed_value']
            );
        }
        return $gifts;
    }

    /**
     * Delete a gift by ID.
     *
     * @param PDO $db Database connection.
     * @param int $id Gift ID to delete.
     * @return bool True when a row was deleted, false otherwise.
     */
    static function deleteGift(PDO $db, int $id) : bool {
        if( $id <= 0 ) {
            return false;
        }

        try {
            $stmt = $db->prepare('
                DELETE FROM gifts
                WHERE id = ?;
            ');
            $stmt->execute([$id]);

            return $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            error_log("Database Error in deleteGift: " . $e->getMessage());
            return false;
        }
    }

    // Static version of getContributedValue that takes a gift ID instead 
    static function getContributedValueForGift(PDO $db, int $gift_id) : int {
        $stmt = $db->prepare('
            SELECT contributed_value 
            FROM gifts 
            WHERE id = ?
        ');
        $stmt->execute(array($gift_id));

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['contributed_value'] : 0;
    }

    // Returns the remaining value for this gift by subtracting the contributed value from the total value
    public function getRemainingValue(PDO $db) : int {
        $contributed_value = $this->getContributedValue();
        return max($this->total_value - $contributed_value, 0);
    }

    // Returns the remaining value for this gift by subtracting the contributed value from the total value
    static function getRemainingValueForGift(PDO $db, int $gift_id) : int {
        $gift = self::getGiftById($db, $gift_id);
        if( !$gift ) {
            return 0;
        }
        $contributed_value = $gift->getContributedValue();
        return max($gift->getTotalValue() - $contributed_value, 0);
    }

    
    public function getId() : int {
        return $this->id;
    }
    public function getName() : string {
        return $this->name;
    }
    public function getTotalValue() : int {
        return $this->total_value;
    }
    public function getContributedValue() : int {
        return $this->contributed_value;
    }
}

?>