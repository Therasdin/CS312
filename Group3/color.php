<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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

    <form method="POST" action="color.php"> 
        <label><p>How many Rows and Columns do you want?:&nbsp;</p></label> 
        <input type="text" name="rowsAndColumns"></input><br> 
        <label><p>How many colors do you want?:&nbsp;</p></label> 
        <input type="text" name="colors"></input><br> 
        <label><p>Generate Table -></p></label> 
        <input type="submit" value="Submit"></input> 
    </form> 

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

<footer>
    <p>© 2026 Twist & Tones | Designed by our team</p>
</footer>
</body>
</html>