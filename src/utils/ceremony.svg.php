<?php

declare(strict_types=1);


function drawCeremonySVG() {
    $iconPath = __DIR__ . "/../../public/images/ceremony.svg";
    if( file_exists($iconPath) ) {
        return file_get_contents($iconPath);
    } else {
        return "";
    }
}

?>