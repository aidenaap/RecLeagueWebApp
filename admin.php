<!-- admin page -->
<?php include_once 'header.php';?>
<?php include_once 'includes/config.inc.php';?>

<section class="admin--container">
    <!-- admin header with season information to update page -->
    <div class="admin--header">
        <h1>Admin Overview</h1>
        <!-- league choice form -->
        <form class="season-form" action="includes/admin.inc.php" method="POST">
            <label for="league_list">League: </label>
            <select name="league_list">
                <!-- php to get all league names -->
                <?php
                    $uid = $_SESSION["user_id"];
                    $sql = "SELECT id, league_name FROM leagues WHERE user_id = $uid";
                    $result = mysqli_query($conn, $sql);

                    // iterate through leauges
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            // if season currently the one viewed, add selected attribute
                            if ($row["id"] == $_SESSION["league_id"]) {
                                echo "<option selected>".$row["league_name"]."</option>";
                            } else {
                                echo "<option>".$row["league_name"]."</option>";
                            }
                        }
                    }
                ?>
            </select>
            <button type="submit" name="change_league_submit" value="change_league">View</button>
            <a class="season-btn" onclick="openNewLeagueForm()">+</a>
            <a class="season-btn" onclick="openDeleteLeagueForm()">-</a>
        </form>
        <!-- season choice form -->
        <form class="season-form" action="includes/admin.inc.php" method="POST">
            <label for="season_list">Season: </label>
            <select name="season_list">
                <!-- php code to get all season names -->
                <?php
                    $lid = $_SESSION["league_id"];
                    $sql = "SELECT id, season_name FROM seasons WHERE league_id = $lid";
                    $result = mysqli_query($conn, $sql);

                    // iterate through seasons
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            // if season is currently selected show in select bar
                            if ($row["id"] == $_SESSION["season_id"]) {
                                echo "<option selected>".$row["season_name"]."</option>";
                            } else {
                                echo "<option>".$row["season_name"]."</option>";
                            }
                        }
                    }
                ?>
            </select>
            <button type="submit" name="change_season_submit" value="change_season">View</button>
            <a class="season-btn" onclick="openNewSeasonForm()">+</a>
            <a class="season-btn" onclick="openDeleteSeasonForm()">-</a>
        </form>

        <!-- handle error/success GET messages from deleting and inserting seasons -->
        <?php
            if (isset($_GET["error"])) {
                if ($_GET["error"] == "emptyinput") {
                    echo "<p class=\"flash fail\">Input all fields!</p>";
                } else if ($_GET["error"] == "invalidseasonname") {
                    echo "<p class=\"flash fail\">Invalid season name!</p>";
                } else if ($_GET["error"] == "invalidteamname") {
                    echo "<p class=\"flash fail\">Invalid team name!</p>";
                } else if ($_GET["error"] == "invalidfees") {
                    echo "<p class=\"flash fail\">Invalid fee format!</p>";
                } else if ($_GET["error"] == "stmtfailed") {
                    echo "<p class=\"flash fail\">Statement failed, try again!</p>";
                } else if ($_GET["error"] == "invalidscore") {
                    echo "<p class=\"flash fail\">Someone must've scored.</p>";
                } 
            }

            if (isset($_GET["success"])) {
                // league UPDATE, CREATE, and DELETE
                if ($_GET["success"] == "leagueupdated") {
                    echo "<p class=\"flash success\">League view updated!</p>";
                } else if ($_GET["success"] == "leaguecreated") {
                    echo "<p class=\"flash success\">League created!</p>";
                } else if ($_GET["success"] == "leaguedeleted") {
                    echo "<p class=\"flash success\">League deleted!</p>";
                // season creation, deletion, and updates
                } else if ($_GET["success"] == "seasoncreated") {
                    echo "<p class=\"flash success\">Season created!</p>";
                } else if ($_GET["success"] == "seasondeleted") {
                    echo "<p class=\"flash success\">Season deleted!</p>";
                } else if ($_GET["success"] == "seasonupdated") {
                    echo "<p class=\"flash success\">Season view updated!</p>";
                // team creation, deletion, and updates
                } else if ($_GET["success"] == "teamcreated") {
                    echo "<p class=\"flash success\">New team created!</p>";
                } else if ($_GET["success"] == "teamupdated") {
                    echo "<p class=\"flash success\">Team updated!</p>";
                } else if ($_GET["success"] == "teamdeleted") {
                    echo "<p class=\"flash success\">Team deleted!</p>";
                // match creation and deletion
                } else if ($_GET["success"] == "matchcreated") {
                    echo "<p class=\"flash success\">Match created!</p>";
                } else if ($_GET["success"] == "gamedeleted") {
                    echo "<p class=\"flash success\">Match deleted!</p>";
                }
            }
        ?>

        <!-- new league pop up form -->
        <div class="pop-up league" id="new_league_form">
            <p onclick="closeNewLeagueForm()">&times;</p>
            <form class="pop-up-form" action="includes/admin.inc.php" method="POST">
                <label>New League: </label>
                <input type="text" name="new_league_name" placeholder="League Name" />
                <input type="text" name="new_league_code" placeholder="League code for users" />
                <button type="submit" name="new_league_submit" value="new_league">Submit</button>
            </form>
        </div>
        <!-- delete league pop up form -->
        <div class="pop-up league" id="delete_league_form">
            <p onclick="closeDeleteLeagueForm()">&times;</p>
            <form class="pop-up-form" action="includes/admin.inc.php" method="POST">
                <label for="league_delete_list">League to Delete: </label>
                <select name="league_delete_list">
                <!-- php code to get league names -->
                <?php
                    $uid = $_SESSION["user_id"];
                    $sql = "SELECT league_name FROM leagues WHERE user_id = $uid";
                    $result = mysqli_query($conn, $sql);

                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<option>".$row['league_name']."</option>";
                        }
                    }
                ?>
                </select>
                <a class="add-drop-btn" onclick="openDeleteLeagueConfirm()">Submit</a>
                <div class="confirm-delete-pop-up" id="confirm-delete-league">
                    <h3>Are you certain?</h3>
                    <a class="cancel-drop-btn" onclick="closeDeleteLeagueConfirm()">Cancel</a>
                    <button type="submit" name="delete_league_submit" value="delete_league">Confirm</button>
                </div>
            </form>
        </div>

        <!-- new season pop up form -->
        <div class="pop-up" id="new_season_form">
            <p onclick="closeNewSeasonForm()">&times;</p>
            <form class="pop-up-form" action="includes/admin.inc.php" method="POST">
                <label>New Season: </label>
                <input type="text" name="new_season_name" placeholder="Season Name" />
                <input type="number" step="0.01" min="0" name="new_fees_total" placeholder="Fees per Team (#.##)"/>
                <button type="submit" name="new_season_submit" value="new_season">Submit</button>
            </form>
        </div>
        <!-- delete season pop up form -->
        <div class="pop-up" id="delete_season_form">
            <p onclick="closeDeleteSeasonForm()">&times;</p>
            <form class="pop-up-form" action="includes/admin.inc.php" method="POST">
                <label for="season_delete_list">Season to Delete: </label>
                <select name="season_delete_list">
                <!-- php code to get season names -->
                <?php
                    $l_id = $_SESSION["league_id"];
                    $sql = "SELECT season_name FROM seasons WHERE league_id = '$l_id'";
                    $result = mysqli_query($conn, $sql);

                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<option>".$row['season_name']."</option>";
                        }
                    }
                ?>
                </select>
                <a class="add-drop-btn" onclick="openDeleteSeasonConfirm()">Submit</a>
                <!-- confirm delete -->
                <div class="confirm-delete-pop-up" id="confirm-delete-season">
                    <h3>Are you certain?</h3>
                    <a class="cancel-drop-btn" onclick="closeDeleteSeasonConfirm()">Cancel</a>
                    <button type="submit" name="delete_season_submit" value="delete_season">Confirm</button>
                </div>
            </form>
        </div>
    </div>

    <!-- admin main section, containing two main columns -->
    <div class="admin--tworows">
        <!-- table with information by team -->
        <div class="teamview-col">
            <h2>Team Status</h2>
            <table class="teamview-table">
                <col class="admin-team-column-one">
                <col class="admin-team-column-two">
                <col class="admin-team-column-three">
                <col class="admin-team-column-four">
                <col class="admin-team-column-five">
                <col class="admin-team-column-six">
                <thead>
                    <tr class="header-row">
                        <th class="name-col">Team Name</th>
                        <th>W</th>
                        <th>L</th>
                        <th>T</th>
                        <th>Fees Paid</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- php code to get team info for respective season -->
                    <?php
                        $seshid = $_SESSION['season_id'];

                        // get team info from teams in current season_id by fees paid
                        $sql = "SELECT id, team_name, wins, losses, ties, fees_paid FROM teams WHERE season_id = '$seshid' ORDER BY fees_paid";
                        $team_result = mysqli_query($conn, $sql);

                        // get the total season fees to check if team has paid
                        $sql = "SELECT fees_total FROM seasons WHERE id = '$seshid'";
                        $season_fees = mysqli_query($conn, $sql);

                        // iterate through teams
                        if (mysqli_num_rows($team_result) > 0) {
                            $season_row = mysqli_fetch_assoc($season_fees);
                            while ($row = mysqli_fetch_assoc($team_result)) {
                                // add new table row
                                echo "<tr>";
                                echo "<td class=\"name-col\">".$row['team_name']."</td>";
                                echo "<td class=\"centered\">".$row['wins']."</td>";
                                echo "<td class=\"centered\">".$row['losses']."</td>";
                                echo "<td class=\"centered\">".$row['ties']."</td>";
                                if ($row['fees_paid'] >= $season_row['fees_total']) {
                                    echo "<td class=\"centered fees paid\">".$row["fees_paid"]."</td>";
                                } else {
                                    echo "<td class=\"centered fees not-paid\">".$row["fees_paid"]."</td>";
                                }
                                // pass wins, losses, ties, fees, and id from each respective team into the editTeamForm
                                // to place as 'value' tags in corresponding html form inputs
                                echo "<td class=\"functional\" onclick=\"openEditForm(".$row['wins'].", ".$row['losses'].", ".$row['ties'].", ".$row['fees_paid'].", ".$row['id'].")\">Edit</td>";
                                echo "</tr>";
                            }
                        }
                    ?>
                </tbody>
            </table>
            <!-- pop up form for editing teams -->
            <div class="team-pop-up edit" id="edit-team-form">
                <p onclick="closeEditTeamForm()">&times;</p>
                <!-- main form to redirect to .inc for the updateTeam() function -->
                <form action="includes/admin.inc.php" method="POST">
                    <h3 class="team-edit">Edit Team</h3>
                    <label for="edit-team-wins">Wins:</label>
                    <input id="edit-team-wins" name="edit-team-wins" type="number" step="1" min="0" />
                    <label for="edit-team-losses">Losses:</label>
                    <input id="edit-team-losses" name="edit-team-losses" type="number" step="1" min="0" />
                    <label for="edit-ties">Ties:</label>
                    <input id="edit-team-ties" name="edit-team-ties" type="number" step="1" min="0" />
                    <label for="edit-team-fees">Fees:</label>
                    <input id="edit-team-fees" name="edit-team-fees" type="number" step="0.01" min="0" />
                    <input id="edit-team-id "name="edit-team-id" type="hidden" />
                    <button type="submit" id="change_team_info" name="change_team_info">Submit</button>
                </form>
            </div>
        </div>
        <!-- various forms for inserting teams, games, and players -->
        <div class="statview-col">
            <!-- add game section -->
            <h2>Add Game</h2>
            <form action="includes/admin.inc.php" method="POST">
                <div class="add-game-row">
                    <select name="home_team" aria-placeholder="home team">
                        <!-- php code to get team names -->
                        <?php
                            $seshid = $_SESSION['season_id'];

                            $sql = "SELECT team_name from teams WHERE season_id = '$seshid'";

                            $result = mysqli_query($conn, $sql);

                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<option>".$row["team_name"]."</option>";
                                }
                            }
                        ?>
                    </select>
                    <input type="number" name="home_score" id="home_score" min="0" step="0.01" placeholder="score"/>
                </div>
                <h3>Versus</h3>
                <div class="add-game-row">
                    <select name="away_team" aria-placeholder="away team">
                        <!-- php code to get team names -->
                        <!-- GET THE TEAMS FROM RESPECTIVE SEASON -->
                        <?php
                            $seshid = $_SESSION['season_id'];

                            $sql = "SELECT team_name from teams  WHERE season_id = '$seshid'";
                            $result = mysqli_query($conn, $sql);

                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<option>".$row["team_name"]."</option>";
                                }
                            }
                        ?>
                    </select>
                    <input type="number" name="away_score" id="away_score" min="0" step="0.01" placeholder="score"/>
                </div>
                <button type="submit" name="add_game" value="add_game">Submit</button>
                <p class="game-delete" onclick="openDeleteGameForm()">Remove Game</p>
            </form>
            <!-- pop up form to delete games -->
            <div class="delete-game-form" id="delete_game">
                <form action="includes/admin.inc.php" method="POST">
                    <label for="game_delete_list">Game to Delete:</label>
                    <select name="game_delete_list" id="game_delete_list">
                        <?php
                            $sql = "SELECT id, home_team_id, away_team_id, dateCreated from matches";
                            $result = mysqli_query($conn, $sql);
        
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {

                                    $home_id = $row['home_team_id'];
                                    $away_id = $row['away_team_id'];
                                    $sesh_id = $_SESSION['season_id'];

                                    $sql = "SELECT team_name FROM teams WHERE id='$home_id' AND season_id = '$sesh_id'";
                                    $hn_query = mysqli_query($conn, $sql);
                                    // $home_name = mysqli_fetch_row($hn_query);

                                    $sql = "SELECT team_name FROM teams WHERE id='$away_id' AND season_id = '$sesh_id'";
                                    $an_query = mysqli_query($conn, $sql);
                                    // $away_name = mysqli_fetch_row($an_query);

                                    if ($away_name = mysqli_fetch_assoc($an_query) and $home_name = mysqli_fetch_assoc($hn_query)) {
                                        echo "<option value=\"".$row['id']."\">".$home_name['team_name']." vs ".$away_name['team_name']." (".$row['dateCreated'].")</option>";
                                    }

                                    //echo "<option value=\"".$row['id']."\">".$home_name[0]." vs ".$away_name[0]." (".$row['dateCreated'].")</option>";
                                }
                            }
                        ?>
                    </select>
                    <button type="submit" name="delete_game">Confirm</button>
                    <p onclick="closeDeleteGameForm()">Cancel</p>
                </form>
            </div>
            <!-- add team section -->
            <h2 class="team-group-title">Add Team</h2>
            <form class="new-team-form" action="includes/admin.inc.php" method="POST">
                <input class="team-input" type="text" name="team_name" placeholder="Team Name" />
                <input class="team-input" type="number" min="0" step=".01" name="fees_paid" placeholder="Fees Paid (#.##)" />
                <button type="submit" name="add_team" value="add_team">Submit</button>
                <p onclick="openDeleteTeamForm()">Remove Team</p>
            </form>
            <!-- pop up form to delete teams -->
            <div class="delete-team-form" id="delete_team">
                <form action="includes/admin.inc.php" method="POST">
                    <label for="team_delete_list">Team to Delete:</label>
                    <select name="team_delete_list" id="team_delete_list">
                        <!-- php code to get all teams -->
                        <?php
                            $sesh_id = $_SESSION['season_id'];

                            $sql = "SELECT * FROM teams WHERE season_id = $sesh_id";
                            $result = mysqli_query($conn, $sql);
        
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<option>".$row['team_name']."</option>";
                                }
                            }
                        ?>
                    </select>
                    <button type="submit" name="delete_team">Confirm</button>
                    <p onclick="closeDeleteTeamForm()">Cancel</p>
                </form>
            </div>
        </div>
    </div>
</section>

</body>
<script type="text/javascript" src="scripts/mainfile.js"></script>
</html>