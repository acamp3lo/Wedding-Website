<?php

declare(strict_types = 1);

function getConfig(string $fileName) : array {
    // Read the JSON file
    $jsonString = file_get_contents(__DIR__ . '/../../config/' . $fileName . '.json');

    if( $jsonString === false ) {
        die("Error: Configuration file could not be read.");
    }

    // Decode it into an associative PHP array
    $config = json_decode($jsonString, true);

    // Fallback/Error handling
    if( !$config ) {
        die("Error: Configuration file could not be loaded.");
    }
    return $config;
}

?>