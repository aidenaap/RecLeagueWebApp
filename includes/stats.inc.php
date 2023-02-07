<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config.inc.php';
    require_once 'functions.inc.php';

    if (isset($_POST['change_team_view'])) {

        $team_name = $_POST['team_stat_change'];

        $teamExists = teamExists($conn, $team_name);
        
        $team_id = $teamExists['id'];
        $_SESSION["team_id"] = $team_id;
        header("location: ../stats.php?success=changedTeam");
        exit();
    }

} else {
    header("location: ../stats.php");
    exit();
}

?>