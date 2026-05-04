<?php
require 'db.php';

$colorsAvailable = getAllColors($conn);
$maxColors = count($colorsAvailable);

$n = '';
$rowColors = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Color Coordinate</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="colors_dynamic.php">
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

<section>
    <div class="color-post-form">
        <form method="POST" action="color.php"> 
            <label><p>How many Rows and Columns do you want?:&nbsp;</p></label> 
            <input type="text" name="rowsAndColumns" value="<?php echo htmlspecialchars($_POST['rowsAndColumns'] ?? ''); ?>"><br> 

            <label><p>How many colors do you want?:&nbsp;</p></label> 
            <input type="text" name="colors" value="<?php echo htmlspecialchars($_POST['colors'] ?? ''); ?>"><br> 

            <p>Maximum colors currently available: <?php echo $maxColors; ?></p><br>

            <label><p>Generate Table -></p></label> 
            <input type="submit" value="Submit"> 
        </form> 
    </div>

    <div class="color-php-logic">

        <?php 
        $validRows = false;
        $validColors = false;
        $numberOfRowsAndColumns = 0;
        $numberOfColors = 0;

        if (isset($_POST["rowsAndColumns"])) { 
            $numberOfRowsAndColumns = (int)$_POST["rowsAndColumns"];  

            if ($numberOfRowsAndColumns >= 1 && $numberOfRowsAndColumns <= 26) { 
                $validRows = true;
            } else { 
                echo '<p style="color:#FFBB9E;">Please enter a valid range of rows and columns: a number between 1-26.</p>'; 
            } 
        } 

        if (isset($_POST["colors"])) { 
            $numberOfColors = (int)$_POST["colors"];  

            if ($numberOfColors >= 1 && $numberOfColors <= $maxColors) { 
                $validColors = true;
            } else { 
                echo '<p style="color:#FFBB9E;">Please enter a valid range of colors: a number between 1-' . $maxColors . '.</p>'; 
            } 
        } 
        ?>

        <?php
        if ($validRows && $validColors) {
            $initialColors = array_slice($colorsAvailable, 0, $numberOfColors);
            $rowColors = $initialColors;

            echo '<table id="color-table" style="width:100%; border-collapse:collapse;">';

            echo '<thead>';
            echo '<tr>';
            echo '<th style="color:white; width:5%; padding:4px; text-align:center;">Active</th>';
            echo '<th style="color:white; width:20%; padding:4px; text-align:left;">Color</th>';
            echo '<th style="color:white; width:25%; padding:4px; text-align:left;">Preview</th>';
            echo '<th style="color:white; width:50%; padding:4px; text-align:left;">Coordinates</th>';
            echo '</tr>';
            echo '</thead>';

            echo '<tbody>';

            for ($i = 0; $i < $numberOfColors; $i++) {
                $checked = ($i === 0) ? 'checked' : '';

                echo '<tr id="color-row-' . $i . '">';

                echo '<td style="text-align:center; padding:4px;">';
                echo '<input type="radio" name="activeColor" class="color-radio" value="' . $i . '" ' . $checked . '>';
                echo '</td>';

                echo '<td style="padding:4px;">';
                echo '<select class="color-dropdown" data-index="' . $i . '" data-previous="' . htmlspecialchars($initialColors[$i]['hex_value']) . '">';

                foreach ($colorsAvailable as $color) {
                    $selected = ($color['id'] == $initialColors[$i]['id']) ? 'selected' : '';

                    echo '<option value="' . htmlspecialchars($color['hex_value']) . '" data-name="' . htmlspecialchars($color['name']) . '" ' . $selected . '>';
                    echo htmlspecialchars($color['name']);
                    echo '</option>';
                }

                echo '</select>';
                echo '</td>';

                echo '<td class="color-preview" style="padding:5px; background-color:' . htmlspecialchars($initialColors[$i]['hex_value']) . '; color:#000000;">';
                echo htmlspecialchars($initialColors[$i]['name']);
                echo '</td>';

                echo '<td class="color-coords" id="coords-' . $i . '" style="padding:4px; font-size:0.85em;"></td>';

                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';

            echo '<p id="color-warning" style="color:#FFBB9E; margin:4px 0;"></p>';
        }
        ?>

        <?php
        if ($validRows && $validColors) {
            $n = $numberOfRowsAndColumns;

            echo '<h3 style="text-align:center;">Coordinate Grid</h3>';
            echo '<table id="square-table" style="border-collapse:collapse; margin:0 auto;">';

            echo '<tr>';
            echo '<td style="width:30px; height:30px;"></td>';

            for ($col = 0; $col < $n; $col++) {
                $letter = chr(65 + $col);
                echo '<td style="border:1px solid #FFBB9E; text-align:center; color:#ffffff; width:30px; height:30px;">' . $letter . '</td>';
            }

            echo '</tr>';

            for ($row = 1; $row <= $n; $row++) {
                echo '<tr>';
                echo '<td style="border:1px solid #FFBB9E; text-align:center; color:#ffffff; width:30px; height:30px;">' . $row . '</td>';

                for ($col = 0; $col < $n; $col++) {
                    $letter = chr(65 + $col);
                    $coord = $letter . $row;

                    echo '<td class="grid-cell" data-coord="' . $coord . '" style="border:1px solid #FFBB9E; width:30px; height:30px; cursor:pointer;"></td>';
                }

                echo '</tr>';
            }

            echo '</table>';
        }
        ?>

        <?php if ($validRows && $validColors): ?>
            <!-- Keeps merged print.php changes compatible. Do not rename these IDs. -->
            <form method="POST" id="print" action="print.php">
                <input type="hidden" name="rows" value="<?php echo htmlspecialchars($n); ?>">
                <input type="hidden" id="row-coordinates" name="coords" value="">
                <input type="hidden" id="row-colors" name="colors" value="">
                <input type="submit" value="Print View">
            </form>
        <?php endif; ?>

    </div>
</section>

<script>
(function () {
    const numRows = document.querySelectorAll('.color-radio').length;
    const colorCoords = {};

    for (let i = 0; i < numRows; i++) {
        colorCoords[i] = new Set();
    }

    function getActiveRowIndex() {
        const checked = document.querySelector('input[name="activeColor"]:checked');
        return checked ? parseInt(checked.value, 10) : 0;
    }

    function getColorForRow(idx) {
        const dropdowns = document.querySelectorAll('.color-dropdown');
        return dropdowns[idx] ? dropdowns[idx].value : null;
    }

    function sortCoords(coordSet) {
        return [...coordSet].sort((a, b) => {
            const matchA = a.match(/^([A-Z]+)(\d+)$/);
            const matchB = b.match(/^([A-Z]+)(\d+)$/);

            if (!matchA || !matchB) {
                return a.localeCompare(b);
            }

            if (matchA[1] !== matchB[1]) {
                return matchA[1].localeCompare(matchB[1]);
            }

            return parseInt(matchA[2], 10) - parseInt(matchB[2], 10);
        });
    }

    function updateCoordsDisplay(rowIndex) {
        const el = document.getElementById('coords-' + rowIndex);

        if (el) {
            el.textContent = sortCoords(colorCoords[rowIndex]).join(', ');
        }
    }

    document.querySelectorAll('.grid-cell').forEach(cell => {
        cell.addEventListener('click', function () {
            const coord = this.dataset.coord;
            const activeIdx = getActiveRowIndex();
            const activeColor = getColorForRow(activeIdx);

            const prevIdxStr = this.dataset.paintedBy;

            if (prevIdxStr !== undefined) {
                const prevIdx = parseInt(prevIdxStr, 10);

                if (prevIdx !== activeIdx) {
                    colorCoords[prevIdx].delete(coord);
                    updateCoordsDisplay(prevIdx);
                } else {
                    return;
                }
            }

            this.style.backgroundColor = activeColor;
            this.dataset.paintedBy = String(activeIdx);

            colorCoords[activeIdx].add(coord);
            updateCoordsDisplay(activeIdx);
        });
    });

    document.querySelectorAll('.color-dropdown').forEach((dropdown, i) => {
        dropdown.addEventListener('change', function () {
            const allDropdowns = Array.from(document.querySelectorAll('.color-dropdown'));

            const selectedColors = allDropdowns.map(d => d.value);

            const hasDuplicate = selectedColors.some((val, idx) => {
                return val === this.value && idx !== i;
            });

            const warning = document.getElementById('color-warning');

            if (hasDuplicate) {
                this.value = this.dataset.previous;

                if (warning) {
                    warning.textContent = 'This color is already in use. Please choose a different one.';
                }

                return;
            }

            if (warning) {
                warning.textContent = '';
            }

            const newColor = this.value;
            this.dataset.previous = newColor;

            const row = this.closest('tr');
            const previewCell = row ? row.querySelector('.color-preview') : null;
            const selectedOption = this.options[this.selectedIndex];

            if (previewCell) {
                previewCell.style.backgroundColor = newColor;
                previewCell.textContent = selectedOption.dataset.name;
            }

            document.querySelectorAll('.grid-cell[data-painted-by="' + i + '"]').forEach(cell => {
                cell.style.backgroundColor = newColor;
            });
        });
    });
})();
</script>

<script>
document.getElementById('print')?.addEventListener("submit", function(event) {
    const selects = document.querySelectorAll('.color-dropdown');
    const rowColors = [];

    for (let index = 0; index < selects.length; index++) {
        const selectedOption = selects[index].options[selects[index].selectedIndex];
        const colorName = selectedOption.dataset.name;
        const hexValue = selects[index].value;

        rowColors.push(colorName + " — " + hexValue);
    }

    document.getElementById('row-colors').value = JSON.stringify(rowColors);

    const coordinates = document.getElementsByClassName("color-coords");
    const rowCoordinates = [];

    for (let index = 0; index < coordinates.length; index++) {
        rowCoordinates[index] = coordinates[index].textContent;
    }

    document.getElementById('row-coordinates').value = JSON.stringify(rowCoordinates);
});
</script>

<footer>
    <p>© 2026 Twist & Tones | Designed by our team</p>
</footer>

</body>
</html>