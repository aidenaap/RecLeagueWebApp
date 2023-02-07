<!-- Login page -->
<?php include_once 'header.php'; ?>

<div class="login--container">
    <h1>Welcome back to your<br />Rec-League App!</h1>
    <h4>Enter cedentials below to get started!</h4>
    <!-- upon form submit refer to this php file -->
    <form action="includes/login.inc.php" method="post">
        <input type="text" name="username" placeholder="Enter Username" />
        <input type="password" name="password" placeholder="Enter Password" />
        <button type="submit" name="submit" class="login-btn">Submit</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register Here</a></p>

    <?php
    if (isset($_GET["error"])) {
        if ($_GET["error"] == "emptyinput") {
            echo "<p class=\"flash error\">Input all fields!</p>";
        } else if ($_GET["error"] == "usernotexist") {
            echo "<p class=\"flash error\">Username does not exist!</p>";
        } else if ($_GET["error"] == "wronglogin") {
            echo "<p class=\"flash error\">Incorrect login info</p>";
        }
    }

    if (isset($_GET["success"])) {
        if ($_GET["success"] == "registered") {
            echo "<p class=\"flash succeeded\">Account successfully created!</p>";
        } else if ($_GET["success"] == "adminregistered") {
            echo "<p class=\"flash succeeded\">Admin account successfully created!</p>";
        }
    }
    ?>
</div>

</body>
</html>