<?php
define('DB_HOST', 'helmi.cs.colostate.edu');
define('DB_USER', 'YOUR_NETID');
define('DB_PASS', 'YOUR_PASSWORD');
define('DB_NAME', 'YOUR_NETID');

define('SSL_CERT', '/usr/local/ssl/server-cert.pem');
define('SSL_CA',   '/usr/local/ssl/ca-cert.pem');

$conn = mysqli_init();
if (!$conn) {
    die('mysqli_init failed.');
}
$conn->ssl_set(SSL_CERT, NULL, SSL_CA, NULL, NULL);
mysqli_options($conn, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, true);
if (!mysqli_real_connect($conn, DB_HOST, DB_USER, DB_PASS, DB_NAME)) {
    die('Connection failed: ' . mysqli_connect_error());
}

//sql query to CREATE DATABASE colors, include all 10 colors with Hex codes 
//$colorsAvailable = ["Red" FF0000,"Orange" FFA500,"Yellow" FFFF00,"Green" 008000,"Blue" 0000FF,"Purple" 800080,"Grey" 808080,"Brown" 964B00,"Black" 000000,"Teal" 008080];
//sql query to ADD color, enter in name and a hex code, if either exists -> "This color already exists in the table." or if a name or hex code is left out -> "Please enter a name!" or "Please enter a hex value!"
//give message to confirm before deleting -> check if there are 2 colors, (if yes -> stop and give error -> "The table cannot have fewer than 2 colors.") -> delete color from list

$sql = "CREATE TABLE colors (id INT AUTO_INCREMENT NOT NULL, colorName VARCHAR(50) NOT NULL, hex_value VARCHAR(8) NOT NULL)";
$sqlBaseValues = "INSERT INTO colors (id, colorName, hex_value) 
        VALUES ('Red', 'FF0000'), 
               ('Orange', 'FFA500'), 
               ('Yellow', 'FFFF00'), 
               ('Green', '008000'), 
               ('Blue', '0000FF'), 
               ('Purple', '800080'), 
               ('Grey', '808080'), 
               ('Brown', '964B00'), 
               ('Black', '000000'), 
               ('Teal', '008080')";
$sqlAddColor = "INSERT INTO colors (id, colorName, hex_value) VALUES ($colorName, $hex_value)";
$sqlFindColor = "SELECT * FROM colors WHERE colorName = $selectedColor"; //run for both add and delete to check for errors
$sqlDeleteColor = "DELETE FROM colors WHERE colorName = $selectedColor";
if ($conn->query($sql) === TRUE) {
//show the table on the webpage
}

$conn->close();
?>

<?php 
//set global var of number of elements in table -> $numberOfColors

//Add method
if(isset($_POST[""])){   
    //check if color is in database ->  $existingColor = colorName , $existingHexValue = hex_value
    if($colorName === $existingColor || $hex_value === $existingHexValue){ 
        echo("This color already exists in the table.");
    } 
    if($colorName === null || $hex_value === null){
        echo("Not enough information. Please enter a name AND hex value.");
    }
    else{  
        $sqlAddColor = "INSERT INTO colors (id, colorName, hex_value) VALUES ($colorName, $hex_value)";
        } 
} 

//Edit method
if(isset($_POST[""])){
    //check if color is in database 
    if($selectedColor === $newColorName || ){ 
        echo("This color already exists in the table.")
        } 
    else{ 
        //update color in table 
        } 
} 

//Deletion method
if(isset($_POST[""])){ 
    $numberOfColors = (int)$_POST["numberOfColors"];  
    if($numberOfColors <= 2){ 
          $sqlDeleteColor = "DELETE FROM colors WHERE colorName = $selectedColor";
        } 
        else{ 
            echo("The table cannot have fewer than 2 colors."); 
        } 
} 
?> 

//Add form
<form method="POST" action="db.php"> 
    <label><p>Color Name:&nbsp;</p></label> 
    <input type="text" name="colorName"></input><br> 
    <label><p>Hex Value:&nbsp;</p></label> 
    <input type="text" name="hex_value"></input><br> 
    <label>Add Color</label>
    <input type="submit" value="Submit"></input> 
</form> 

//Edit form
<form method="POST" action="db.php"> 
    //to do: change select color to drop down
    <label><p>Select Color:&nbsp;</p></label> 
    <input type="text" name="selectedColor"></input><br> 
    <label><p>New Name:&nbsp;</p></label> 
    <input type="text" name="newColorName"></input><br> 
    <label><p>New Hex Value:&nbsp;</p></label> 
    <input type="text" name="newHex_value"></input><br> 
    <label>Save Changes</label>
    <input type="submit" value="Submit"></input> 
</form> 

//Deletion form
<form method="POST" action="db.php"> 
    //to do: change select color to drop down
    <label><p>Select Color:&nbsp;</p></label> 
    <input type="text" name="selectedColor"></input><br> 
    <label>Delete Selected</label>
    <input type="submit" value="Submit"></input> 
</form> 
