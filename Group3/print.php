<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
</head>

<body style="width: 8.5in; margin: 0 auto;">

	<br>
	<form method="POST" action="color.php">
		<input type="submit" value="Return to Color View">
	</form>

	<div class="logo"><img src="print_Logo.png" alt="Twist & Tones"></div>
	<h1>Print View</h1>
	<?php
	$rowNumber = (int)$_POST['rows'] ?? 0;
	$rowColors = json_decode($_POST['colors'] ?? '[]', true);
	$rowCoords = json_decode($_POST['coords'] ?? '[]', true);
	if ($rowNumber == 0 || $rowColors == []) {
		echo 'Failed to provide number of Rows and Columns or number of Colors. Please retry with both';
	} else {
		// ===== STEP 4.3: Top Color Table =====
		echo '<table id="color-table"; style="width:100%; border-collapse: collapse;">';
		echo '<tr>';
		echo '<th style="width:20%; border: 1px solid #000000; padding:5px; background-color:#CCCCCC; color:#000000; text-align:left; padding-left:20px;"> Color </th>';
		echo '<th style="width:80%; border: 1px solid #000000; padding:5px; background-color:#CCCCCC; color:#000000;"> Coordinates </th>';
		for ($i=0; $i < count($rowColors); $i++) { 
			echo '<tr>';
			// Left column: dropdown (20%)
			echo '<td style="width:20%; border: 1px solid #000000; padding-left:20px; background-color:#EEEEEE; color:#000000;">';
			echo $rowColors[$i];
			echo '</td>';

			// Right column: color preview (80%)
			echo '<td style="width:80%; border: 1px solid #000000; padding:5px; background-color:#FFFFFF; color:#000000; overflow-wrap: break-word; height: auto; text-align:center;">';
			echo $rowCoords[$i];
			echo '</td>';
			echo '</tr>';
		}
		echo '</table>';

		// ===== STEP 4.4: Bottom Coordinate Grid =====
		echo '<br><h3>Coordinate Grid</h3>';
		echo '<table id="square-table"; style="border-collapse: collapse;">';

		// Top row with letters
		echo '<tr><td style="width:30px; height:30px; background-color=#EEEEEE";></td>'; // empty top-left
		for ($col = 0; $col < $rowNumber; $col++) {
			$letter = chr(65 + $col); // A=65
			echo '<td style="border: 1px solid #AEAEAE; background-color=#CCCCCC; text-align:center; color: #000000">' . $letter . '</td>';
		}
		echo '</tr>';

		// Rows with numbers and empty cells
		for ($row = 1; $row <= $rowNumber; $row++) {
			echo '<tr>';
			echo '<td style="border: 1px solid #B3B3B3; text-align:center; color: #000000"; width:30px; height:30px;>' . $row . '</td>'; // left column
			for ($col = 1; $col <= $rowNumber; $col++) {
				echo '<td style="border: 1px solid #B3B3B3; width:30px; height:30px;"></td>';
			}
			echo '</tr>';
		}

		echo '</table>';
	}
	?>
	<br>

</body>

</html>