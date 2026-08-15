<?php

declare(strict_types = 1);


class Accommodation {
    private string $name;
    private string $location;
    private string $link;
    private string $image_name;
    private string $distance;
    
    public function __construct(string $name, string $location, string $link, string $image_name, string $distance) {
        $this->name = $name;
        $this->location = $location;
        $this->link = $link;
        $this->image_name = $image_name;
        $this->distance = $distance;
    }

    public static function getAllAccommodations() : array {
        // Read the JSON file
        $jsonContents = file_get_contents(__DIR__ . '/../../config/accommodations.json');
        if( $jsonContents === false ) {
            throw new RuntimeException('Unable to read accommodatioins.json.');
        }
        // Decode it into an associative PHP array
        $data = json_decode($jsonContents, true, 512, JSON_THROW_ON_ERROR);
        $accommodations = $data['accommodations'] ?? $data;

        if( !is_array($accommodations) ) {
            throw new RuntimeException('The JSON file does not contain a valid accommodations list.');
        }

        return array_map([self::class, 'fromArray'], $accommodations);
    }

    private static function fromArray(array $data) : Accommodation {
        return new Accommodation(
            htmlspecialchars($data['name']),
            htmlspecialchars($data['location']),
            htmlspecialchars($data['link']),
            htmlspecialchars($data['image_name']),
            htmlspecialchars($data['distance'])
        );
    }

    public function getName() : string {
        return $this->name;
    }
    public function getLocation() : string {
        return $this->location;
    }
    public function getLink() : string {
        return $this->link;
    }
    public function getImageName() : string {
        return $this->image_name;
    }
    public function getDistance() : string {
        return $this->distance;
    }
}

?>