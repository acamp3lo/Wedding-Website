<?php

declare(strict_types = 1);

require_once(__DIR__ . '/../../src/utils/gift.class.php');

/**
 * Deletes gifts from the database that are not in the new gift array.
 *
 * @param PDO $db Database connection.
 * @param array $newGifts New gifts array.
 * @return bool True when at least 1 gifts was removed from the database, false otherwise.
 */
function deleteMissingGifts(PDO $db, array $newGifts) : bool {
    // Get the current gifts
    $stmt = $db->prepare('
        SELECT * FROM gifts;
    ');
    $stmt->execute();
    $oldGifts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if( !isset($oldGifts) ) {
        return false;
    }

    $arraySize = count($newGifts);
    $newGiftsMap = array_fill(0, $arraySize, false);    // Maps the matches between the new gifts and the old ones.
    $deletedGifts = false;
    foreach( $oldGifts as $oldGift ) {
        $match = false;
        for( $j = 0; $j < $arraySize; $j++ ) {
            if( $newGiftsMap[$j] ) {
                continue;
            }
            if( isset($newGifts[$j]['id']) && (int)$newGifts[$j]['id'] === (int)$oldGift['id'] ) {
                $match = true;
                $newGiftsMap[$j] = true;
                break;
            }
        }
        if( !$match ) {
            if( (int)$oldGift['contributed_value'] === 0 ) {
                if( !Gift::deleteGift($db, (int)$oldGift['id']) ) {
                    throw new RuntimeException('Unable to remove old gift #' . $oldGift['id'] . '.');
                } else {
                    $deletedGifts = true;
                }
            } else {
                throw new RuntimeException('Cannot remove gift #' . $oldGift['id'] . ' "' . $oldGift['name'] . '" because it already has contributions.');
            }
        }
    }
    return $deletedGifts;
}

?>