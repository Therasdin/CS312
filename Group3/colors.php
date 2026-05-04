<?php
require 'db.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $hex = normalizeHex($_POST['hex_value'] ?? '');

        if ($name === '' || $hex === '') {
            $error = 'Please enter both a color name and hex value.';
        } elseif (!preg_match('/^#[0-9A-F]{6}$/', $hex)) {
            $error = 'Please enter a valid hex value like #FF0000.';
        } else {
            $stmt = $conn->prepare("INSERT INTO colors (name, hex_value) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $hex);

			try {
				$stmt->execute();
				$message = 'Color added successfully.';
			} catch (mysqli_sql_exception) {
				$error = 'That color name or hex value already exists.';
			}

            $stmt->close();
        }
    }

    if ($action === 'edit') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $hex = normalizeHex($_POST['hex_value'] ?? '');

        if ($id <= 0 || $name === '' || $hex === '') {
            $error = 'Please select a color and enter both a name and hex value.';
        } elseif (!preg_match('/^#[0-9A-F]{6}$/', $hex)) {
            $error = 'Please enter a valid hex value like #FF0000.';
        } else {
            $stmt = $conn->prepare("UPDATE colors SET name = ?, hex_value = ? WHERE id = ?");
            $stmt->bind_param("ssi", $name, $hex, $id);

			try {
				$stmt->execute();
				$message = 'Color updated successfully.';
			} catch (mysqli_sql_exception) {
				$error = 'That color name or hex value conflicts with another color.';
			}

            $stmt->close();
        }
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $confirm = $_POST['confirm_delete'] ?? '';

        $countResult = $conn->query("SELECT COUNT(*) AS total FROM colors");
        $countRow = $countResult->fetch_assoc();

        if ((int)$countRow['total'] <= 2) {
            $error = 'The table cannot have fewer than 2 colors.';
        } elseif ($confirm !== 'yes') {
            $error = 'Please check the confirmation box before deleting.';
        } else {
            $stmt = $conn->prepare("DELETE FROM colors WHERE id = ?");
            $stmt->bind_param("i", $id);

			try {
				$stmt->execute();
				$message = 'Color deleted successfully.';
			} catch (mysqli_sql_exception) {
				$error = 'Unable to delete color.';
			}

            $stmt->close();
        }
    }

	if ($action === 'reset') {
		$confirm = $_POST['confirm_reset'] ?? '';
		if ($confirm !== 'yes') {
			$error = 'Please check the confirmation box before resetting.';
		}
		restoreDefault($conn);
	}
}

$colors = getAllColors($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Color Selection</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar">
    <div class="logo"><img src="Logo.png" alt="Twist & Tones"></div>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="about.php">About</a></li>
        <li><a href="color.php">Color</a></li>
        <li><a href="colors.php">Color Selection</a></li>
    </ul>
</nav>

<section class="color-post-form">
    <h1 style="color:white;">Color Selection</h1>

    <?php if ($message !== ''): ?>
        <p style="color:#47E1F5;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <p style="color:#FFBB9E;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
</section>

<section class="color-php-logic">

    <h3>Add Color</h3>
    <form method="POST" action="colors.php">
        <input type="hidden" name="action" value="add">
        <input type="text" name="name" placeholder="Color name">
        <input type="text" name="hex_value" placeholder="#FF0000">
        <input type="submit" value="Add Color">
    </form>

    <h3>Edit Color</h3>
    <form method="POST" action="colors.php">
        <input type="hidden" name="action" value="edit">

        <select name="id" class="color-dropdown">
            <?php foreach ($colors as $color): ?>
                <option value="<?php echo $color['id']; ?>">
                    <?php echo htmlspecialchars($color['name'] . ' — ' . $color['hex_value']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <input type="text" name="name" placeholder="New name">
        <input type="text" name="hex_value" placeholder="New hex value">
        <input type="submit" value="Save Changes">
    </form>

    <h3>Delete Color</h3>
    <form method="POST" action="colors.php">
        <input type="hidden" name="action" value="delete">

        <select name="id" class="color-dropdown">
            <?php foreach ($colors as $color): ?>
                <option value="<?php echo $color['id']; ?>">
                    <?php echo htmlspecialchars($color['name'] . ' — ' . $color['hex_value']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label style="color:white; display:block; margin-top:10px;">
            <input type="checkbox" name="confirm_delete" value="yes">
            I confirm I want to delete this color.
        </label>

        <input type="submit" value="Delete Color">
    </form>

	<h3>Restore Default Colors<h3>
	<form method="POST" action="colors.php">
		<input type="hidden" name="action" value="reset">
		<label style="color:white; display:block; margin-top:10px;">
			<input type="checkbox" name="confirm_reset" value="yes">
			<body>I confirm I want to reset colors to deafult 10 options.</body>
		</label>

		<input type="submit" value="Reset Color">
	</form>

    <h3>Current Colors</h3>
    <table>
        <tr>
            <th>Name</th>
            <th>Hex Value</th>
        </tr>

        <?php foreach ($colors as $color): ?>
            <tr>
                <td><?php echo htmlspecialchars($color['name']); ?></td>
                <td><?php echo htmlspecialchars($color['hex_value']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

</section>

<footer>
    <p>© 2026 Twist & Tones | Designed by our team</p>
</footer>

</body>
</html>