<?php
define('DB_HOST', 'helmi.cs.colostate.edu');
define('DB_USER', 'EID');			// EDIT LOCALLY
define('DB_PASS', 'Password');		// EDIT LOCALLY
define('DB_NAME', 'EID');			// EDIT LOCALLY

define('SSL_CERT', '/usr/local/ssl/server-cert.pem');
define('SSL_CA', '/usr/local/ssl/ca-cert.pem');

$conn = mysqli_init();

if (!$conn) {
    die('mysqli_init failed.');
}

$conn->ssl_set(SSL_CERT, NULL, SSL_CA, NULL, NULL);
mysqli_options($conn, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, true);

if (!mysqli_real_connect($conn, DB_HOST, DB_USER, DB_PASS, DB_NAME)) {
    die('Connection failed: ' . mysqli_connect_error());
}

function setupColorsTable($conn) {
    $sql = "
        CREATE TABLE IF NOT EXISTS colors (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) NOT NULL UNIQUE,
            hex_value VARCHAR(7) NOT NULL UNIQUE
        );
    ";

    if (!$conn->query($sql)) {
        die('Could not create colors table: ' . $conn->error);
    }

    $result = $conn->query("SELECT COUNT(*) AS total FROM colors;");
    $row = $result->fetch_assoc();

    if ((int)$row['total'] === 0) {
        $baseColors = [
            ['Red', '#FF0000'],
            ['Orange', '#FFA500'],
            ['Yellow', '#FFFF00'],
            ['Green', '#008000'],
            ['Blue', '#0000FF'],
            ['Purple', '#800080'],
            ['Grey', '#808080'],
            ['Brown', '#964B00'],
            ['Black', '#000000'],
            ['Teal', '#008080']
        ];

        $stmt = $conn->prepare("INSERT INTO colors (name, hex_value) VALUES (?, ?);");

        foreach ($baseColors as $color) {
            $stmt->bind_param("ss", $color[0], $color[1]);
            $stmt->execute();
        }

        $stmt->close();
    }
}

function restoreDefault($conn) {
	$clearEntries = 'TRUNCATE TABLE colors;';
	$conn->query($clearEntries);
	$restoreDefault = 'INSERT INTO colors (name, hex_value) VALUES (?, ?);';
	$baseColors = [
			['Red', '#FF0000'],
			['Orange', '#FFA500'],
			['Yellow', '#FFFF00'],
			['Green', '#008000'],
			['Blue', '#0000FF'],
			['Purple', '#800080'],
			['Grey', '#808080'],
			['Brown', '#964B00'],
			['Black', '#000000'],
			['Teal', '#008080']
	];
	$stmt = $conn->prepare($restoreDefault);
	foreach ($baseColors as $color) {
		$stmt->bind_param("ss", $color[0], $color[1]);
		$stmt->execute();
	}
	$stmt->close();
}

function getAllColors($conn) {
    $colors = [];
    $result = $conn->query("SELECT id, name, hex_value FROM colors ORDER BY id ASC;");

    while ($row = $result->fetch_assoc()) {
        $colors[] = $row;
    }

    return $colors;
}

function normalizeHex($hex) {
    $hex = trim($hex);

    if ($hex !== '' && $hex[0] !== '#') {
        $hex = '#' . $hex;
    }

    return strtoupper($hex);
}

setupColorsTable($conn);
?>