<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- essential meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- descriptive meta tags -->
    <meta name="author" content="Aiden Perez" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />

    <!-- link me some good ol css -->
    <?php echo "<link rel='stylesheet' type='text/css' href='styles/style.css' />"; ?>
    
    <!-- tab title -->
    <?php echo "<title>Rec-League Administrator</title>"; ?>
</head>
<body>
    <nav class="navbar">
        <p class="logo">RLA</p>
        <div class="nav-links">
            <?php
                // if user logged in show proper tabs
                if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]===true){
                    echo "<a href=\"standings.php\">Standings</a>";
                    echo "<a href=\"stats.php\">Statistics</a>";
                    
                    // if admin show admin tab
                    if (isset($_SESSION["admin"]) && $_SESSION["admin"] !== NULL) {
                        echo "<a href=\"admin.php\">Admin</a>";
                    }
                    echo "<a href=\"includes/logout.inc.php\">Logout</a>";
                } else {
                    echo "<a href=\"login.php\">Login</a>";
                    echo "<a href=\"register.php\">Register</a>";
                }   
            ?>
        </div>
    </nav>