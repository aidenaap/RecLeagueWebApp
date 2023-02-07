<!-- stats page -->
<!-- Team view, and player rankings below -->
<?php 
include_once 'header.php';
include_once 'includes/config.inc.php';
 ?>

<section class="view--container">
    <div class="view--header">
        <h1>Team Overview</h1>
        <form class="season-form" action="includes/stats.inc.php" method="POST">
            <label for="team_stat_change">Team Name: </label>
            <select name="team_stat_change">
                <!-- php code to loop through team in users season_id -->
                <?php
                    // show teams according to their season_id
                    $sesh_id = $_SESSION['season_id'];
                    $team_id = $_SESSION['team_id'];

                    $sql = "SELECT team_name, id FROM teams WHERE season_id = '$sesh_id'";
                    $team_result = mysqli_query($conn, $sql);

                    if (mysqli_num_rows($team_result) > 0) {
                        while ($row = mysqli_fetch_assoc($team_result)) {
                            if ($row['id'] == $team_id) {
                                echo "<option selected>".$row['team_name']."</option>";
                            } else {
                                echo "<option>".$row['team_name']."</option>";
                            }
                        }
                    }
                ?>
            </select>
            <button type="submit" name="change_team_view" id="change_team_view">View</button>
        </form>
    </div>
    <div class="team-info--wrapper">
        <div class="team-text">
            <?php
                $team_id = $_SESSION['team_id'];
                
                // if team_id is not 999 then display the team info
                if ($team_id !== 999) {
                    $sql = "SELECT * FROM teams WHERE id = '$team_id'";
                    $result = mysqli_query($conn, $sql);

                    $team_row = mysqli_fetch_assoc($result);

                    echo "<h1>".$team_row['team_name']."</h1>";
                    echo "<h3>Record: </h3>";
                    echo "<p>".$team_row['wins']."<span> W</span> - ".$team_row['losses']."<span> L</span> - ".$team_row['ties']."<span> T</span></p>";
                    echo "<h4>Last 5 Games:</h4>";

                    // get last 5 matches
                    $count = 0;
                    $MAX_DISPLAY = 5;
                    $sql = "SELECT * FROM matches WHERE home_team_id = $team_id OR away_team_id = $team_id";
                    $result = mysqli_query($conn, $sql);

                    if (mysqli_num_rows($result) > 0) {
                        echo "<div class=\"matches\">";
                        while ($row = mysqli_fetch_assoc($result) and $count < $MAX_DISPLAY) {
                            // if blocks to split by home/away team and get outcome of game
                            if ($row["home_team_id"] == $team_id) {
                                if ($row["home_score"] > $row["away_score"]) {
                                    echo "<p>Win: <span>".$row["home_score"]."</span> to ".$row["away_score"]." - <span class=\"game-date\">(".$row["dateCreated"].")</span></p>";
                                } else if ($row["away_score"] > $row["home_score"]) {
                                    echo "<p>Loss: ".$row["away_score"]." to <span>".$row["home_score"]."</span> - <span class=\"game-date\">(".$row["dateCreated"].")</span></p>";
                                } else {
                                    echo "<p>Tie: <span>".$row["home_score"]."</span> to ".$row["away_score"]." - <span class=\"game-date\">(".$row["dateCreated"].")</span></p>";
                                }
                            // if away team
                            } else if ($row["away_team_id"] == $team_id) {
                                if ($row["away_score"] > $row["home_score"]) {
                                    echo "<p>Win: <span>".$row["away_score"]."</span> to ".$row["home_score"]." - <span class=\"game-date\">(".$row["dateCreated"].")</span></p>";
                                } else if ($row["home_score"] > $row["away_score"]) {
                                    echo "<p>Loss: ".$row["home_score"]." to <span>".$row["away_score"]."</span> - <span class=\"game-date\">(".$row["dateCreated"].")</span></p>";
                                } else {
                                    echo "<p>Tie: <span>".$row["away_score"]."</span> to ".$row["home_score"]." - <span class=\"game-date\">(".$row["dateCreated"].")</span></p>";
                                }
                            }
                            $count += 1;
                        }
                        echo "</div>";
                    } else {
                        echo "<p class=\"no-team-msg\">Team has no games input yet.</p>";
                    }

                // display add team message
                } else {
                    echo "<p class=\"no-team-msg\">Add some teams to see them here!</p>";
                }
            ?>
        </div>
    </div>
    <div class="view--header">
        <h1>Recent Games</h1>
    </div>
    <table class="view--table">
        <col class="game-column-one">
        <col class="game-column-two">
        <col class="game-column-three">
        <col class="game-column-four">
        <col class="game-column-five">
        <col class="game-column-six">
        <thead>
            <tr class="header-row">
                <th class="name-col">Team Name</th>
                <th>Score</th>
                <th></th>
                <th>Score</th>
                <th class="name-col right">Team Name</th>
                <th>Date Added</th>
            </tr>
        </thead>
        <tbody>
            <!-- php code to loop through matches -->
            <?php
                $count = 0;
                $MAX_DISPLAY = 10;
                $sesh_id = $_SESSION['season_id'];

                $sql = "SELECT home_team_id, away_team_id, home_score, away_score, dateCreated FROM matches WHERE season_id = $sesh_id ORDER BY dateCreated DESC";
                $matches_result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($matches_result) > 0) {
                    while ($row = mysqli_fetch_assoc($matches_result) and $count < $MAX_DISPLAY) {

                        // get their team names through another sql query
                        // look into replacing this with a join query
                        $home_id = $row['home_team_id'];
                        $away_id = $row['away_team_id'];

                        $sql = "SELECT team_name FROM teams WHERE id='$home_id'";
                        $hn_query = mysqli_query($conn, $sql);
                        $home_name = mysqli_fetch_row($hn_query);

                        $sql = "SELECT team_name FROM teams WHERE id='$away_id'";
                        $an_query = mysqli_query($conn, $sql);
                        $away_name = mysqli_fetch_row($an_query);

                        echo"<tr>";
                        echo"<td class=\"name-col\">".$home_name[0]."</td>";
                        echo"<td class=\"centered\">".$row['home_score']."</td>";
                        echo"<td class=\"centered\">VS.</td>";
                        echo"<td class=\"centered\">".$row['away_score']."</td>";
                        echo"<td class=\"name-col right\">".$away_name[0]."</td>";
                        echo"<td class=\"centered\">".$row['dateCreated']."</td>";
                        echo"</tr>";

                        $count += 1;
                    }
                }
            ?>
        </tbody>
    </table>
</section>