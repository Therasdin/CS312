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
        $grid = "Display grid.<br>"; 
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
        $colorTable = "Display color table.<br>"; 
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

                echo '<table style="width:100%; border-collapse: collapse;">';
                for($i=0; $i<$numberOfColors; $i++) {
                    echo '<tr>';
                    // Left column: dropdown (20%)
                    echo '<td style="width:20%;">';
                    echo '<select class="color-dropdown" data-previous="'.$initialColors[$i].'">';
                    foreach($colorsAvailable as $color) {
                        $selected = ($color == $initialColors[$i]) ? "selected" : "";
                        echo '<option value="'.$color.'" '.$selected.'>'.$color.'</option>';
                    }
                    echo '</select>';
                    echo '</td>';

                    // Right column: color preview (80%)
                    echo '<td style="width:80%; padding:5px; background-color:'.$initialColors[$i].'; color:#000000;">';
                    echo $initialColors[$i];
                    echo '</td>';

                    echo '</tr>';
                }
                echo '</table>';
                echo '<p id="color-warning" style="color:red;"></p>';
            }
        }

        // ===== STEP 4.4: Bottom Coordinate Grid =====
        if(isset($_POST["rowsAndColumns"])) {
            $n = (int)$_POST["rowsAndColumns"];
            if($n >= 1 && $n <= 26) {
                echo '<h3 style="text-align:center;">Coordinate Grid</h3>';
                echo '<table style="border-collapse: collapse; margin: 0 auto;">';

                // Top row with letters
                echo '<tr><td style="width:30px; height:30px;"></td>'; // empty top-left
                for($col=0; $col<$n; $col++) {
                    $letter = chr(65 + $col); // A=65
                    echo '<td style="border: 1px solid #FFBB9E; text-align:center; color: #ffffff">'.$letter.'</td>';
                }
                echo '</tr>';

                // Rows with numbers and empty cells
                for($row=1; $row<=$n; $row++) {
                    echo '<tr>';
                    echo '<td style="border: 1px solid #FFBB9E; text-align:center; color: #ffffff"; width:30px; height:30px;>'.$row.'</td>'; // left column
                    for($col=1; $col<=$n; $col++) {
                        echo '<td style="border: 1px solid #FFBB9E; width:30px; height:30px;"></td>';
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
            <input type="hidden" name="colors" value="<?php echo $numberOfColors ?? ''; ?>">
            <input type="submit" value="Print View">
        </form>

    </div>
</section>

<script>
document.querySelectorAll('.color-dropdown').forEach(dropdown => {
    dropdown.addEventListener('change', function() {
        let selectedColors = Array.from(document.querySelectorAll('.color-dropdown')).map(d => d.value);
        let duplicates = selectedColors.filter((item, index) => selectedColors.indexOf(item) !== index);

        if(duplicates.length > 0) {
            this.value = this.dataset.previous;
            document.getElementById('color-warning').textContent = "This color is already in use. Please choose a different one.";
        } else {
            this.dataset.previous = this.value;
            document.getElementById('color-warning').textContent = "";

            let row = this.parentElement.parentElement;
            let previewCell = row.cells[1];
            previewCell.style.backgroundColor = this.value;
            previewCell.textContent = this.value;
        }
    });
});
</script>

<footer>
    <p>© 2026 Twist & Tones | Designed by our team</p>
</footer>
</body>
</html>
