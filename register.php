<!-- Register page -->
<?php include_once 'header.php'; ?>

<div class="login--container">
    <h1>Register today for the<br />Rec-League Admin App!</h1>
    <h4>Create account below to get started!</h4>
    <!-- upon form submit refer to this php file -->
    <form action="includes/sign-up.inc.php" method="post">
        <input type="text" name="username" placeholder="Enter Username" />
        <input type="password" name="password" placeholder="Enter Password" />
        <input type="password" name="confirm_password" placeholder="Confirm Password" />
        <input type="text" name="entered_league_code" placeholder="League Code" />
        <input type="text" name="admin_password" placeholder="Admin Password (if applicable)" />
        <button type="submit" name="submit" class="login-btn">Submit</button>
    </form>
    <p class="register">Already have an account? <a href="login.php">Sign In Here</a></p>

    <!-- handle errors passed upon submit -->
    <?php
        if (isset($_GET["error"])) {
            if ($_GET["error"] == "emptyinput") {
                echo "<p>Input all fields!</p>";
            } else if ($_GET["error"] == "invalidusername") {
                echo "<p class=\"popup error\">Invalid Username!</p>";
            } else if ($_GET["error"] == "invalidpassword") {
                echo "<p class=\"popup error\">Password must have 8 characters, 1 letter, and 1 number!</p>";
            } else if ($_GET["error"] == "passwordmismatch") {
                echo "<p class=\"popup error\">Passwords don't match!</p>";
            } else if ($_GET["error"] == "userexists") {
                echo "<p class=\"popup error\">User already exists!</p>";
            } else if ($_GET["error"] == "stmtfailed") {
                echo "<p class=\"popup error\">Something went wrong. Try again!</p>";
            } else if ($_GET["error"] == "adminpass") {
                echo "<p>Either get it right or leave admin field blank.</p>";
            } else if ($_GET["error"] == "noleaguecode") {
                echo "<p>Must enter a league code</p>";
            } else if ($_GET["error"] == "wrongleaguecode") {
                echo "<p>League Code not found</p>";
            } else if ($_GET["error"] == "none") {
                echo "<p>Successfully created user!</p>";
            }
        }
    ?>
</div>

</body>
</html>