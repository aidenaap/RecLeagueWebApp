<?php
    //  ===== admin backend file to handle different form submits and respective processes ===== //
    //if form submitted properly
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once 'config.inc.php';
        require_once 'functions.inc.php';

        // update league view
        if (isset($_POST['change_league_submit'])) {
            $league_name = $_POST["league_list"];

            updateLeagueSesh($conn, $league_name);
        // add league
        } else if (isset($_POST['new_league_submit'])) {
            $league_name = $_POST['new_league_name'];
            $league_code = $_POST['new_league_code'];

            if (emptyInputNewSeason($league_name, $league_code)) {
                header("location: ../admin.php?error=emptyinput");
                exit();
            }

            if (invalidTeamName($league_name) != false) {
                header("location: ../admin.php?error=invalidseasonname");
                exit();
            }

            if (invalidTeamName($league_code) != false) {
                header("location: ../admin.php?error=invalidseasonname");
                exit();
            }

            createLeague($conn, $league_name, $league_code);
        // drop league
        } else if (isset($_POST['delete_league_submit'])) {
            $name = $_POST['league_delete_list'];

            dropLeague($conn, $name);
        // update season view
        } else if (isset($_POST['change_season_submit'])) {
            $season_name = $_POST['season_list'];

            updateCurrentSeason($conn, $season_name);
        // new season button
        } else if (isset($_POST['new_season_submit'])){
            $season_name = $_POST['new_season_name'];
            $fees = $_POST['new_fees_total'];

            // form validation
            if (emptyInputNewSeason($season_name, $fees) != false) {
                header("location: ../admin.php?error=emptyinput");
                exit();
            }

            if (invalidTeamName($season_name) != false) {
                header("location: ../admin.php?error=invalidseasonname");
                exit();
            }

            if (invalidFeeTotal($fees) != false) {
                header("location: ../admin.php?error=invalidfees");
                exit();
            }

            // proper form, add season
            createSeason($conn, $season_name, $fees);
        // delete season button
        } else if (isset($_POST['delete_season_submit'])) {
            $season_name = $_POST['season_delete_list'];

            // drop that joint
            dropSeason($conn, $season_name);
        // add new team button
        } else if (isset($_POST['add_team'])) {
            $team_name = $_POST['team_name'];
            $fees_paid = $_POST['fees_paid'];

            // form validation
            if (emptyInputNewTeam($team_name, $fees_paid) != false) {
                header("location: ../admin.php?error=emptyinput");
                exit();
            }

            if (invalidTeamName($team_name) != false) {
                header("location: ../admin.php?error=invalidteamname");
                exit();
            }

            if (invalidFeeTotal($fees_paid) != false) {
                header("location: ../admin.php?error=invalidfees");
                exit();
            }

            createTeam($conn, $team_name, $fees_paid);
        // add new game button
        } else if (isset($_POST['add_game'])) {
            $home_team = $_POST['home_team'];
            $home_score = $_POST['home_score'];
            $away_team = $_POST['away_team'];
            $away_score = $_POST['away_score'];

            // form validation
            $homeTeamExists = teamExists($conn, $home_team);
            $awayTeamExists = teamExists($conn, $away_team);

            if (!$homeTeamExists) {
                header('location: ../admin.php?error=unknownfirstteam');
                exit();
            }

            if (!$awayTeamExists) {
                header('location: ../admin.php?error=unknownsecondteam');
                exit();
            }

            if (invalidScore($home_score, $away_score) != false) {
                header('location: ../admin.php?error=invalidscore');
                exit();
            }

            $homeTeamId = $homeTeamExists['id'];
            $awayTeamId = $awayTeamExists['id'];
            
            createMatch($conn, $homeTeamId, $home_score, $awayTeamId, $away_score);

        // edit team button DOES NOT WORK
        } else if (isset($_POST['change_team_info'])) {
            $new_wins = $_POST['edit-team-wins'];
            $new_losses = $_POST['edit-team-losses'];
            $new_ties = $_POST['edit-team-ties'];
            $new_fees = $_POST['edit-team-fees'];
            $team_id = $_POST['edit-team-id'];

            updateTeam($conn, $new_wins, $new_losses, $new_ties, $new_fees, $team_id);
        // delete game buttom
        } else if (isset($_POST['delete_game'])) {
            $game_to_delete = $_POST['game_delete_list'];

            // game to delete should have match id
            dropMatch($conn, $game_to_delete);

        // delete team button
        } else if (isset($_POST['delete_team']))  {
            $team_to_delete = $_POST['team_delete_list'];

            dropTeam($conn, $team_to_delete);
        }
    } else {
        header("location: ../admin.php");
    }
?>