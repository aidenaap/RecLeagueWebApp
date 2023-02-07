<?php
    session_start();

    // ===== Login / Register functions ===== //
    // register form validation
    function emptyInputSignup($name, $pwrd, $cfrm_pwrd) {
        $result = false;
        if (empty($name) || empty($pwrd) || empty($cfrm_pwrd)) {
            $result = true;
        } else {
            $result = false;
        }

        return $result;
    }   
    
    function invalidUsername($name) {
        if (!preg_match(("/^[a-zA-Z0-9]*$/"), $name)) {
            $result = true;
        } else {
            $result = false;
        }
        
        return $result;
    }

    function invalidPassword($pwrd) {
        if (!preg_match(("/^[A-Za-z\d@$!%*?&]/"), $pwrd)) {
            $result = true;
        } else {
            $result = false;
        }
        
        return $result;
    }

    function passwordMatch($pwrd, $cfrm_pwrd) {
        if ($pwrd !== $cfrm_pwrd) {
            $result = true;
        } else {
            $result = false;
        }
        return $result;
    }

    // admin register validation function
    function checkAdminPass($admin_pwrd) {

        $result = false;
        if (null !== apache_getenv("ADMIN_KEY")) {
            if ($admin_pwrd == apache_getenv("ADMIN_KEY")) {
                $result = true;
            } else {
                $result =  false;
            }

            return $result;
        } else {
            return false;
        }
    }

    // create user functions
    function createUser($conn, $name, $pwrd, $league_id) {
        $sql = "INSERT INTO users (username, userpass, league_id) VALUES (?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../register.php?error=stmtfailed");
            exit();
        }

        $hashedPwrd = password_hash($pwrd, PASSWORD_DEFAULT);

        mysqli_stmt_bind_param($stmt, 'ssi', $name, $hashedPwrd, $league_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("location: ../login.php?success=registered");
        exit();
    }

    function createAdminUser($conn, $name, $pwrd) {
        $sql = "INSERT INTO users (username, userpass, adminrole) VALUES (?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../register.php?error=stmtfailed");
            exit();
        }

        $hashedPwrd = password_hash($pwrd, PASSWORD_DEFAULT);
        $adminrole = "admin";

        mysqli_stmt_bind_param($stmt, 'sss', $name, $hashedPwrd, $adminrole);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("location: ../login.php?success=adminregistered");
        exit();
    }

    // login form validation
    function emptyInputLogin($name, $pwrd) {
        if (empty($name) || empty($pwrd)) {
            $result = true;
        } else {
            $result = false;
        }

        return $result;
    }

    // ===== Login Functions ===== //
    function loginAdminSessionSetup($conn) {
        // get first league by user
        $u_id = $_SESSION["user_id"];
        $sql = "SELECT id, league_name FROM leagues WHERE user_id = $u_id";
        $result = mysqli_query($conn, $sql);

        // if there are leagues, set first league in session
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $l_id = $row['id'];
            $_SESSION["league_id"] = $l_id;

            // get seasons by league
            $sql = "SELECT id, season_name FROM seasons WHERE league_id = $l_id";
            $result = mysqli_query($conn, $sql);
            
            // if there are seasons set first season in session
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $sesh_id = $row['id'];
                $_SESSION["season_id"] = $sesh_id;

                // get teams by season
                $sql = "SELECT id, team_name FROM teams WHERE season_id = $sesh_id";
                $result = mysqli_query($conn, $sql);

                // if there are teams set first team in session
                if (mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);
                    $t_id = $row['id'];
                    $t_name = $row['team_name'];
                    $_SESSION["team_id"] = $t_id;
                    $_SESSION["team_name"] = $t_name;

                // if no teams
                } else {
                    $_SESSION["team_id"] = 999;
                    $_SESSION["team_name"] = "none";
                }

            // if no seasons (no teams too)
            } else {
                $_SESSION["season_id"] = 999;
                $_SESSION["team_id"] = 999;
                $_SESSION["team_name"] = "none";
            }
            
        // if no leagues (no seasons/teams too)
        } else {
            $_SESSION["league_id"] = 999;
            $_SESSION["season_id"] = 999;
            $_SESSION["team_id"] = 999;
            $_SESSION["team_name"] = "none";
        }
    }

    function loginSessionSetup($conn, $league_id) {
        // set league id
        $_SESSION['league_id'] = $league_id;
        // set season id
        $sql = "SELECT id, season_name FROM seasons WHERE league_id = $league_id";
        $result = mysqli_query($conn, $sql);

        // if seasons exists, set season id
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $s_id = $row['id'];
            $_SESSION['season_id'] = $s_id;

            // set team id
            $sql = "SELECT id, team_name FROM teams WHERE season_id = $s_id";
            $result = mysqli_query($conn, $sql);

            // if team exists, set id otherwise set team to 999
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $t_id = $row['id'];
                $_SESSION['team_id'] = $t_id;
            } else {
                $_SESSION['team_id'] = 999;
            }

        // if no season exists, set season and team id to standard
        } else {
            $_SESSION['season_id'] = 999;
            $_SESSION['team_id'] = 999;
        }
    }

    function loginUser($conn, $name, $pwrd){
        $userExists = userExists($conn, $name);

        if ($userExists === false) {
            header("location: ../register.php?error=usernotexist");
            exit();
        }

        $hashedPwrd = $userExists['userpass'];
        $checkPwrd = password_verify($pwrd, $hashedPwrd);

        if ($checkPwrd === false) {
            header("location: ../login.php?error=wronglogin");
            exit();
        } else if ($checkPwrd === true) {
            // set session information
            $_SESSION["username"] = $userExists["username"];
            $_SESSION["loggedin"] = true;
            $_SESSION["admin"] = $userExists["adminrole"];
            $_SESSION["user_id"] = $userExists["id"];

            // set season & team session variables
            $league_id = $userExists['league_id'];
            
            if ($league_id !== NULL) {
                loginSessionSetup($conn, $league_id);
            } else {
                loginAdminSessionSetup($conn);
            }
            
            header("location: ../standings.php");
            exit();
        }
    }

    // ===== Getter Functions ===== //
    function leagueExists($conn, $league_name) {
        $sql = "SELECT * FROM leagues WHERE league_name = ?";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../admin.php?error=stmtfailed");
            exit();
        }

        mysqli_stmt_bind_param($stmt, 's', $league_name);
        mysqli_stmt_execute($stmt);

        $resultData = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($resultData)) {
            return $row;
        } else {
            $result = false;
            return $result;
        }

        mysqli_stmt_close($stmt);
    }

    function seasonExists($conn, $season_name) {
        $sql = "SELECT * FROM seasons WHERE season_name = ?";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../admin.php?error=stmtfailed");
            exit();
        }

        mysqli_stmt_bind_param($stmt, 's', $season_name);
        mysqli_stmt_execute($stmt);

        $resultData = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($resultData)) {
            return $row;
        } else {
            $result = false;
            return $result;
        }

        mysqli_stmt_close($stmt);
    }

    function teamExists($conn, $team_name) {
        $sql = "SELECT * FROM teams WHERE team_name = ?";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../admin.php?error=stmtfailed");
            exit();
        }

        mysqli_stmt_bind_param($stmt, 's', $team_name);
        mysqli_stmt_execute($stmt);

        $resultData = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($resultData)) {
            return $row;
        } else {
            $result = false;
            return $result;
        }

        mysqli_stmt_close($stmt);
    }

    function userExists($conn, $name) {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../register.php?error=stmtfailed");
            exit();
        }

        mysqli_stmt_bind_param($stmt, 's', $name);
        mysqli_stmt_execute($stmt);

        $resultData = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($resultData)) {
            return $row;
        } else {
            $result = false;
            return $result;
        }

        mysqli_stmt_close($stmt);
    }

    // ===== Session update functions ===== //
    // UPDATE current league_id and season_id by view
    function updateLeagueSesh($conn, $league_name) {
        $leagueExists = leagueExists($conn, $league_name);

        if ($leagueExists === false) {
            header("location: ../admin.php?error=stmtfailed");
            exit();
        }

        $l_id = $leagueExists['id'];
        $_SESSION["league_id"] = $l_id;

        // update current season pertaining to league chosen
        $sql = "SELECT id, season_name FROM seasons WHERE league_id = $l_id";
        $result = mysqli_query($conn, $sql);

        if ($row = mysqli_fetch_assoc($result)) {
            $s_id = $row["id"];
        } else {
            $s_id = 999;
        }

        $_SESSION["season_id"] = $s_id;

        header("location: ../admin.php?success=leagueupdated");
        exit();
    }

    function updateCurrentSeason($conn, $season_name) {
        $seasonExists = seasonExists($conn, $season_name);

        if ($seasonExists === false) {
            header("location: ../admin.php?error=stmtfailed");
            exit();
        }

        $sesh_id = $seasonExists['id'];
        $_SESSION["season_id"] = $sesh_id;
        header("location: ../admin.php?success=seasonupdated");
        exit();
    }

    function updateStandingsSeason($conn, $season_name) {
        $seasonExists = seasonExists($conn, $season_name);

        if ($seasonExists === false) {
            header("location: ../standings.php?error=stmtfailed");
            exit();
        }

        $sesh_id = $seasonExists['id'];
        $_SESSION["season_id"] = $sesh_id;

        // if there is team id already set, figure it out
        if ($_SESSION["team_id"] != 999) {
            $sql = "SELECT id FROM teams WHERE season_id = $sesh_id";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                
                $_SESSION["team_id"] = $row["id"];
            } else {
                header("location: ../standings.php?error=stmtfailed");
                exit();
            }
        }

        header("location: ../standings.php?success=seasonupdated");
        exit();
    }

    // ===== League functions ===== //
    // INSERT and DROP leagues
    function createLeague($conn, $league_name, $league_code) {
        $sql = "INSERT INTO leagues (league_name, league_code, user_id) VALUES (?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../admin.php?error=stmtfailed");
            exit();
        }

        $uid = $_SESSION["user_id"];

        mysqli_stmt_bind_param($stmt, 'ssi', $league_name, $league_code, $uid);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("location: ../admin.php?success=leaguecreated");
        exit();
    }

    function dropLeague($conn, $league_name) {
        $sql = "DELETE FROM leagues WHERE league_name = ?";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../admin.php?error=stmtfailed");
            exit();
        }

        mysqli_stmt_bind_param($stmt, 's', $league_name);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("location: ../admin.php?success=leaguedeleted");
        exit();
    }

    // ===== Season functions ===== //
    // helper functions for season form validation
    function emptyInputNewSeason($season_name, $fees){
        if (empty($season_name) || empty($fees)) {
            return true;
        } else {
            return false;
        }
    }

    function invalidSeasonName($season_name){
        if (!preg_match(("/^[a-zA-Z0-9_.- ]*$/"), $season_name)) {
            $result = true;
        } else {
            $result = false;
        }
        return $result;
    }

    function invalidFeeTotal($fees){
        if (!preg_match(("/^\d+(?:\.\d{0,2})?$/"), $fees)) {
            $result = true;
        } else {
            $result = false;
        }
        return $result;
    }

    // INSERT and DROP seasons
    function createSeason($conn, $season_name, $fees) {
        $sql = "INSERT INTO seasons (season_name, fees_total, league_id) VALUES (?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../admin.php?error=stmtfailed");
            exit();
        }

        $l_id = $_SESSION["league_id"];

        $seasonExists = seasonExists($conn, $season_name);
        $s_id = $seasonExists['id'];
        $_SESSION["season_id"] = $s_id;

        mysqli_stmt_bind_param($stmt, 'sii', $season_name, $fees, $l_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("location: ../admin.php?success=seasoncreated");
        exit();
    }

    function dropSeason($conn, $season_name){
        $sql = "DELETE FROM seasons WHERE season_name = ?";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../admin.php?error=stmtfailed");
            exit();
        }

        mysqli_stmt_bind_param($stmt, 's', $season_name);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("location: ../admin.php?success=seasondeleted");
        exit();
    }

    // ===== Team Functions ===== //
    // helper functions for team form validation
    function emptyInputNewTeam($team_name, $fees_paid){
        if (empty($team_name) || empty($fees_paid)) {
            $result = true;
        } else
            $result = false;
        return $result;
    }

    function invalidTeamName($team_name){
        if (preg_match("/[^A-Za-z0-9' ]+/", $team_name)) {
            $result = true;
        } else {
            $result = false;
        }
        return $result;
    }

    // INSERT, UPDATE, and DROP teams
    function createTeam($conn, $team_name, $fees_paid){
        $sql = "INSERT INTO teams (team_name, fees_paid, season_id) VALUES (?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../admin.php?error=stmtfailed");
            exit();
        }

        $sid = $_SESSION["season_id"];

        mysqli_stmt_bind_param($stmt, 'sii', $team_name, $fees_paid, $sid);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("location: ../admin.php?success=teamcreated");
        exit();
    }

    function updateTeam($conn, $new_wins, $new_losses, $new_ties, $new_fees, $team_id) {
        $sql = "UPDATE teams SET wins = ?, losses = ?, ties = ?, fees_paid = ?, WHERE id = ?";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../admin.php?error=stmtfailed");
            exit();
        } 
        
        mysqli_stmt_bind_param($stmt, 'iiiii', $new_wins, $new_losses, $new_ties, $new_fees, $team_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("location: ../admin.php?success=teamupdated");
        exit();
    }

    function dropTeam($conn, $team_name) {
        $sql = "DELETE FROM teams WHERE team_name = ?";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../admin.php?error=stmtfailed");
            exit();
        }

        mysqli_stmt_bind_param($stmt, 's', $team_name);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("location: ../admin.php?success=teamdeleted");
        exit();
    }

    // ===== Game Functions ===== //
    // invalidScore : input - scores of games, output - if invalid score return true, otherwise false
    // type: helper for createMatch
    function invalidScore($home_score, $away_score) {
        if ($home_score <= 0 or $away_score <= 0) {
            $result = true;
        } else {
            $result = false;
        }
        return $result;
    }

    // whoWon: input - teams' scores, output - string "home"||"away"||"tie"
    // type: helper for createMatch and dropMatch
    function whoWon($home_score, $away_score) {
        if ($home_score > $away_score) {
            $result = "home";
        } else if ($away_score > $home_score) {
            $result = "away";
        } else {
            $result = "tie";
        }
        return $result;
    }

    // createMatch: input - dbconn, team id's, team scores
    // desc: INSERT the respective fields into matches table, UPDATE teams table's wins/losses/ties
    function createMatch($conn, $homeid, $home_score, $awayid, $away_score) {
        // ===== First INSERT a new game into matches table ===== //
        $sql = "INSERT INTO matches (home_team_id, away_team_id, home_score, away_score, season_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../admin.php?error=stmtfailed");
            exit();
        }

        $sesh_id =  $_SESSION["season_id"];

        mysqli_stmt_bind_param($stmt, 'iiiii', $homeid, $awayid, $home_score, $away_score, $sesh_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // ===== then, UPDATE the teams wins/losses/ties ===== //
        // get info on teams records
        $sql = "SELECT * FROM teams WHERE id = $homeid";
        $result = mysqli_query($conn, $sql);
        $home_row = mysqli_fetch_assoc($result);

        $sql = "SELECT * FROM teams WHERE id = $awayid";
        $result = mysqli_query($conn, $sql);
        $away_row = mysqli_fetch_assoc($result);

        // get match result to determine who won
        $match_result = whoWon($home_score, $away_score);

        // if home team wins
        if ($match_result == "home") {
            // update home team wins and points
            $home_wins = $home_row["wins"] + 1;
            $home_points = $home_row["points"] + 2;
            $away_losses = $away_row["losses"] + 1;

            $sql = "UPDATE teams SET wins = ?, points = ? WHERE id = ?";
            $stmt = mysqli_stmt_init($conn);

            if (!mysqli_stmt_prepare($stmt, $sql)) {
                header("location: ../admin.php?error=stmtfailed");
                exit();
            }

            mysqli_stmt_bind_param($stmt, 'iii', $home_wins, $home_points, $homeid);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // update away team losses
            $sql = "UPDATE teams SET losses = ? WHERE id = ?";
            $stmt = mysqli_stmt_init($conn);

            if (!mysqli_stmt_prepare($stmt, $sql)) {
                header("location: ../admin.php?error=stmtfailed");
                exit();
            }

            mysqli_stmt_bind_param($stmt, 'ii', $away_losses, $awayid);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        // if away team wins
        } else if ($match_result == "away") {
            // update away wins and points
            $away_wins = $away_row["wins"] + 1;
            $away_points = $away_row["points"] + 2;
            $home_losses = $home_row["losses"] + 1;

            $sql = "UPDATE teams SET wins = ?, points = ? WHERE id = ?";
            $stmt = mysqli_stmt_init($conn);

            if (!mysqli_stmt_prepare($stmt, $sql)) {
                header("location: ../admin.php?error=stmtfailed");
                exit();
            }

            mysqli_stmt_bind_param($stmt, 'iii', $away_wins, $away_points, $awayid);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // update home losses
            $sql = "UPDATE teams SET losses = ? WHERE id = ?";
            $stmt = mysqli_stmt_init($conn);

            if (!mysqli_stmt_prepare($stmt, $sql)) {
                header("location: ../admin.php?error=stmtfailed");
                exit();
            }

            mysqli_stmt_bind_param($stmt, 'ii', $home_losses, $homeid);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        } else {
            // update ties and points
            $away_ties = $away_row["ties"] + 1;
            $away_points = $away_row["ties"] + 1;
            $home_ties = $home_row["ties"] + 1;
            $home_points = $away_row["ties"] + 1;

            // update away team
            $sql = "UPDATE teams SET ties = ?, points = ? WHERE id = ?";
            $stmt = mysqli_stmt_init($conn);

            if (!mysqli_stmt_prepare($stmt, $sql)) {
                header("location: ../admin.php?error=stmtfailed");
                exit();
            }

            mysqli_stmt_bind_param($stmt, 'iii', $away_ties, $away_points, $awayid);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);


            // update home team
            $sql = "UPDATE teams SET ties = ?, points = ? WHERE id = ?";
            $stmt = mysqli_stmt_init($conn);

            if (!mysqli_stmt_prepare($stmt, $sql)) {
                header("location: ../admin.php?error=stmtfailed");
                exit();
            }

            mysqli_stmt_bind_param($stmt, 'iii', $home_ties, $home_points, $homeid);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        
        header("location: ../admin.php?success=matchcreated");
        exit();
    }

    // dropMatch: input - dbconn, game id
    // desc: UPDATE teams table's wins/losses/ties, then DELETE match from matches table
    function dropMatch($conn, $game_id) {
        // === first UPDATE teams stats === //
        // query corresponding team id's
        $sql = "SELECT * FROM matches WHERE id= $game_id";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);

        // get team information
        $home_score = $row["home_score"];
        $away_score = $row["away_score"];
        $homeid = $row["home_team_id"];
        $awayid = $row["away_team_id"];

        // query team rows
        $sql = "SELECT * FROM teams WHERE id = $homeid";
        $result = mysqli_query($conn, $sql);
        $home_row = mysqli_fetch_assoc($result);

        $sql = "SELECT * FROM teams WHERE id = $awayid";
        $result = mysqli_query($conn, $sql);
        $away_row = mysqli_fetch_assoc($result);

        // use whoWon function to determine who won
        $match_result = whoWon($home_score, $away_score);

        // based on who won, remove wins/losses/ties accordingly
        if ($match_result == "home") {
            $home_wins = $home_row["wins"] - 1;
            $home_points = $home_row["points"] - 2;
            $away_losses = $away_row["losses"] - 1;
            // update home team
            $sql = "UPDATE teams SET wins = ?, points = ? WHERE id = ?";
            $stmt = mysqli_stmt_init($conn);

            if (!mysqli_stmt_prepare($stmt, $sql)) {
                header("location: ../admin.php?error=stmtfailed");
                exit();
            }

            mysqli_stmt_bind_param($stmt, 'iii', $home_wins, $home_points, $homeid);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // update away team
            $sql = "UPDATE teams SET losses = ? WHERE id = ?";
            $stmt = mysqli_stmt_init($conn);

            if (!mysqli_stmt_prepare($stmt, $sql)) {
                header("location: ../admin.php?error=stmtfailed");
                exit();
            }

            echo "been in there";
            mysqli_stmt_bind_param($stmt, 'ii', $away_losses, $awayid);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);


        } else if ($match_result == "away") {
            $away_wins = $away_row["wins"] - 1;
            $away_points = $away_row["points"] - 2;
            $home_losses = $home_row["losses"] - 1;
            // update home team
            $sql = "UPDATE teams SET losses = ? WHERE id = ?";
            $stmt = mysqli_stmt_init($conn);

            if (!mysqli_stmt_prepare($stmt, $sql)) {
                header("location: ../admin.php?error=stmtfailed");
                exit();
            }

            mysqli_stmt_bind_param($stmt, 'ii', $home_losses, $homeid);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // update away team
            $sql = "UPDATE teams SET wins = ?, points = ? WHERE id = ?";
            $stmt = mysqli_stmt_init($conn);

            if (!mysqli_stmt_prepare($stmt, $sql)) {
                header("location: ../admin.php?error=stmtfailed");
                exit();
            }

            mysqli_stmt_bind_param($stmt, 'iii', $away_wins, $away_points, $awayid);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

        } else {
            $home_ties = $home_row["ties"] - 1;
            $home_points = $home_row["points"] - 1;
            $away_ties = $away_row["ties"] - 1;
            $away_points = $home_row["points"] - 1;
            // update home team
            $sql = "UPDATE teams SET ties = ?, points = ? WHERE id = ?";
            $stmt = mysqli_stmt_init($conn);

            if (!mysqli_stmt_prepare($stmt, $sql)) {
                header("location: ../admin.php?error=stmtfailed");
                exit();
            }

            mysqli_stmt_bind_param($stmt, 'iii', $home_ties, $home_points, $homeid);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // update away team
            $sql = "UPDATE teams SET ties = ?, points = ? WHERE id = ?";
            $stmt = mysqli_stmt_init($conn);

            if (!mysqli_stmt_prepare($stmt, $sql)) {
                header("location: ../admin.php?error=stmtfailed");
                exit();
            }

            mysqli_stmt_bind_param($stmt, 'iii', $away_ties, $away_points, $awayid);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        // delete from matches
        $sql = "DELETE FROM matches WHERE id = ?";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../admin.php?error=stmtfailed");
            exit();
        }

        mysqli_stmt_bind_param($stmt, 'i', $game_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("location: ../admin.php?success=gamedeleted");
        exit();
    }
?>