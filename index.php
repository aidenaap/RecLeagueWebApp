<!-- Opening page of the rec league application -->
<?php
// Require <head> tag and navbar
include_once 'header.php';
?>

<section class="view--container">
    <div class="view--header">
        <h1>Welcome to your rec-league overview!</h1>
        <div class="index--buttons">
            <button onClick="<?php header("location: login.php") ?>">Login</button>
            <button onClick="<?php header("location: register.php") ?>">Register</button>
        </div>
    </div>
</section>

</body>
</html>