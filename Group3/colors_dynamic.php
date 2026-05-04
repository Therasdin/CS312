<?php
require 'db.php';

header("Content-Type: text/css");

$colors = getAllColors($conn);

foreach ($colors as $color) {
    $className = strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $color['name']));
    echo ".paint-" . $className . " { background-color: " . $color['hex_value'] . "; }\n";
}
?>