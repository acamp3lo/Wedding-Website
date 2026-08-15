<?php

declare(strict_types = 1);


class GiftIcon {
    private int $id;
    
    public function __construct(int $id) {
        $this->id = $id;
    }

    static function drawIcon(int $id) : string {
        $iconPath = __DIR__ . "/../icons/svg/gifts/" . $id . ".svg";
        if( file_exists($iconPath) ) {
            return file_get_contents($iconPath);
        } else {
            return "";
        }
    }
    
    public function getId() : int {
        return $this->id;
    }
}

?>