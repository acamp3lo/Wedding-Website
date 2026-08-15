<?php

declare(strict_types=1);


function drawVenueSVG() {
    $iconPath = __DIR__ . "/../../public/images/venue.svg";
    if( file_exists($iconPath) ) {
        return file_get_contents($iconPath);
    } else {
        return "";
    }
}

?>
