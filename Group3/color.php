<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
<nav class="navbar">
    <div class="logo"><img src="Logo.png" alt="Twist & Tones"></div>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="about.php">About</a></li>
        <li><a href="color.php">Color</a></li>
    </ul>
</nav>

<section>
    <div class="color-post-form">
        <form method="POST" action="color.php"> 
            <label><p>How many Rows and Columns do you want?:&nbsp;</p></label> 
            <input type="text" name="rowsAndColumns"></input><br> 
            <label><p>How many colors do you want?:&nbsp;</p></label> 
            <input type="text" name="colors"></input><br> 
            <label><p>Generate Table -></p></label> 
            <input type="submit" value="Submit"></input> 
        </form> 
    </div>

    <div class="color-php-logic">
        <?php 
        $grid = ""; 
        if(isset($_POST["rowsAndColumns"])){ 
            $numberOfRowsAndColumns = (int)$_POST["rowsAndColumns"];  
            if($numberOfRowsAndColumns >= 1 && $numberOfRowsAndColumns <= 26){ 
                echo($grid); 
            } 
            else{ 
                echo("Please enter a valid range of rows and columns: a number between 1-26.<br>"); 
            } 
        } 
        ?> 

        <?php 
        $colorTable = ""; 
        if(isset($_POST["colors"])){ 
            $numberOfColors = (int)$_POST["colors"];  
            if($numberOfColors >= 1 && $numberOfColors <= 10){ 
                echo($colorTable); 
            } 
            else{ 
                echo("Please enter a valid range of colors: a number between 1-10.<br>"); 
            } 
        } 
        ?>

        <?php
        // ===== STEP 4.3: Top Color Table =====
        if(isset($_POST["colors"])) {
            $numberOfColors = (int)$_POST["colors"];
            if($numberOfColors >= 1 && $numberOfColors <= 10) {
                $colorsAvailable = ["Red","Orange","Yellow","Green","Blue","Purple","Grey","Brown","Black","Teal"];
                $initialColors = array_slice($colorsAvailable, 0, $numberOfColors);
				$rowColors = $initialColors;

                echo '<table id="color-table"; style="width:100%; border-collapse: collapse;">';
                echo '<thead>';
                echo '<tr>';
                echo '<th style="width:5%;  padding:4px; text-align:center;">Active</th>';
                echo '<th style="width:20%; padding:4px; text-align:left;">Color</th>';
                echo '<th style="width:25%; padding:4px; text-align:left;">Preview</th>';
                echo '<th style="width:50%; padding:4px; text-align:left;">Coordinates</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                for($i=0; $i<$numberOfColors; $i++) {
                    $checked = ($i === 0) ? 'checked' : '';
                    echo '<tr id="color-row-'.$i.'">';

                    echo '<td style="text-align:center; padding:4px;">';
                    echo '<input type="radio" name="activeColor" class="color-radio" value="'.$i.'" '.$checked.'>';
                    echo '</td>';
 
                    echo '<td style="padding:4px;">';
                    echo '<select class="color-dropdown" data-index="'.$i.'" data-previous="'.$initialColors[$i].'">';
                    foreach($colorsAvailable as $color) {
                        $selected = ($color == $initialColors[$i]) ? "selected" : "";
                        echo '<option value="'.$color.'" '.$selected.'>'.$color.'</option>';
                    }
                    echo '</select>';
                    echo '</td>';

                    // Right column: color preview (80%)
                    echo '<td class="color-preview" style="padding:5px; background-color:'.$initialColors[$i].'; color:#000000;">';
                    echo $initialColors[$i];
                    echo '</td>';

                    echo '<td class="color-coords" id="coords-'.$i.'" style="padding:4px; font-size:0.85em;"></td>';
                    echo '</tr>';
                }
				echo '</tbody>';
                echo '</table>';
                echo '<p id="color-warning" style="color:red; margin:4px 0;"></p>';
            }
        }

        // ===== STEP 4.4: Bottom Coordinate Grid =====
        if(isset($_POST["rowsAndColumns"])) {
            $n = (int)$_POST["rowsAndColumns"];
            if($n >= 1 && $n <= 26) {
                echo '<h3 style="text-align:center;">Coordinate Grid</h3>';
                echo '<table id="square-table"; style="border-collapse: collapse; margin: 0 auto;">';

                // Top row with letters
                echo '<tr>';
                echo '<td style="width:30px; height:30px;"></td>';
                for($col=0; $col<$n; $col++) {
                    $letter = chr(65 + $col); // A=65
                    echo '<td style="border:1px solid #FFBB9E; text-align:center; color:#ffffff; width:30px; height:30px;">'.$letter.'</td>';
                }
                echo '</tr>';

                // Rows with numbers and empty cells
                for($row=1; $row<=$n; $row++) {
                    echo '<tr>';
                    echo '<td style="border: 1px solid #FFBB9E; text-align:center; color: #ffffff"; width:30px; height:30px;>'.$row.'</td>'; // left column
                    for($col=0; $col<$n; $col++) {
                        $letter = chr(65 + $col);
                        $coord  = $letter . $row; // e.g. A1, B3
                        echo '<td class="grid-cell" data-coord="'.$coord.'" '
                           . 'style="border:1px solid #FFBB9E; width:30px; height:30px; cursor:pointer;"></td>';
                    }
                    echo '</tr>';
                }

                echo '</table>';
            }
        }
        ?>

        <!-- ===== STEP 5: Print Button ===== -->
        <form method="POST" action="print.php">
            <input type="hidden" name="rows" value="<?php echo $n ?? ''; ?>">
            <input type="hidden" id="row-colors" name="colors"
        		value='<?= htmlspecialchars(json_encode($rowColors), ENT_QUOTES, 'UTF-8') ?>'>
            <input type="submit" value="Print View">
        </form>

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
            if (!matchA || !matchB) return a.localeCompare(b);
            if (matchA[1] !== matchB[1]) return matchA[1].localeCompare(matchB[1]);
            return parseInt(matchA[2], 10) - parseInt(matchB[2], 10);
        });
    }
 
    function updateCoordsDisplay(rowIndex) {
        const el = document.getElementById('coords-' + rowIndex);
        if (el) {
            el.textContent = sortCoords(colorCoords[rowIndex]).join(', ');
        }
    }
 
    function syncHiddenColors() {
        const hidden = document.getElementById('hidden-row-colors');
        if (!hidden) return;
        const colors = Array.from(document.querySelectorAll('.color-dropdown')).map(d => d.value);
        hidden.value = JSON.stringify(colors);
    }
 
    document.querySelectorAll('.grid-cell').forEach(cell => {
        cell.addEventListener('click', function () {
            const coord     = this.dataset.coord;
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
 
            // Paint the cell
            this.style.backgroundColor = activeColor;
            this.dataset.paintedBy     = String(activeIdx);
 
            // Add coordinate to active row's set and refresh display
            colorCoords[activeIdx].add(coord);
            updateCoordsDisplay(activeIdx);
        });
    });
 
    document.querySelectorAll('.color-dropdown').forEach((dropdown, i) => {
        dropdown.addEventListener('change', function () {
            const allDropdowns    = Array.from(document.querySelectorAll('.color-dropdown'));
            const selectedColors  = allDropdowns.map(d => d.value);
            const hasDuplicate    = selectedColors.some(
                (val, idx) => val === this.value && idx !== i
            );
 
            const warning = document.getElementById('color-warning');
 
            if (hasDuplicate) {
                // Revert and warn
                this.value = this.dataset.previous;
                if (warning) {
                    warning.textContent = 'This color is already in use. Please choose a different one.';
                }
                return;
            }
 
            // Valid selection — clear any warning
            if (warning) warning.textContent = '';
 
            const oldColor = this.dataset.previous;
            const newColor = this.value;
            this.dataset.previous = newColor;
 
            // Update the preview cell in this row
            const row         = this.closest('tr');
            const previewCell = row ? row.querySelector('.color-preview') : null;
            if (previewCell) {
                previewCell.style.backgroundColor = newColor;
                previewCell.textContent           = newColor;
            }
 
            // Recolor all grid cells that belong to this row
            document.querySelectorAll('.grid-cell[data-painted-by="' + i + '"]').forEach(cell => {
                cell.style.backgroundColor = newColor;
            });
 
            // Sync hidden input for print form
            syncHiddenColors();
        });
    });
 
    syncHiddenColors();
})();
</script>

<footer>
    <p>© 2026 Twist & Tones | Designed by our team</p>
</footer>
</body>
</html>
