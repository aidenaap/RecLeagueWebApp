<?php

// if they submit the correct way
if (isset($_POST["submit"])) {
    $name = $_POST["username"];
    $pwrd = $_POST["password"];
    $cfrm_pwrd = $_POST["confirm_password"];
    $entered_league_code = $_POST["entered_league_code"];
    $admin_pwrd = $_POST["admin_password"];

    // connect to db and get functions
    require_once "config.inc.php";
    require_once "functions.inc.php";

    // check if any fields are empty
    if (emptyInputSignup($name, $pwrd, $cfrm_pwrd) != false) {
        header("location: ../register.php?error=emptyinput");
        exit();
    }

    // check invalid fields
    if (invalidUsername($name) != false) {
        header("location: ../register.php?error=invalidusername");
        exit();
    }
    if (invalidPassword($pwrd) != false) {
        header("location: ../register.php?error=invalidpassword");
        exit();
    }

    // check password match
    if (passwordMatch($pwrd, $cfrm_pwrd) != false) {
        header("location: ../register.php?error=passwordmismatch");
        exit();
    }

    // check if user exists
    if (userExists($conn, $name) != false) {
        header("location: ../register.php?error=userexists");
        exit();
    }

    // if admin password correct create admin user
    if (checkAdminPass($admin_pwrd) == false) {
        createAdminUser($conn, $name, $pwrd);
        // if field empty move to create normal user
    } else if (empty($admin_pwrd)) {
        goto end;
        // password is something wrong and redirect to register page
    } else {
        header("location: ../register.php?error=adminpass");
        exit();
    }
    end:

    // check for empty code
    if (empty($entered_league_code)) {
        header("location: ../register.php?error=noleaguecode");
        exit();
    // if code input
    } else {
        $sql = "SELECT * FROM leagues WHERE league_code = '$entered_league_code'";
        $result = mysqli_query($conn, $sql);

        // if code exists, set it, otherwise error
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            createUser($conn, $name, $pwrd, $row['id']);
        } else {
            header("location: ../register.php?error=wrongleaguecode");
            exit();
        }
    }

// if improperly got to the page redirect to the register page
} else {
    header("location: ../register.php");
    exit();
}

?>