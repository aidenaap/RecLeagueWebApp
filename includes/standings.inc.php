<?php
    // admin backend file to handle different form submits and respective processes
    //if form submitted properly
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once 'config.inc.php';
        require_once 'functions.inc.php';

        // update season view
        if (isset($_POST['change_standings'])) {
            $season_name = $_POST['season_choice'];

            updateStandingsSeason($conn, $season_name);
        } 
    } else {
        header("location: ../standings.php");
    }

?>