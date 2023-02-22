<!-- stats page -->
<!-- Team view, and player rankings below -->
<?php 
include_once 'header.php';
include_once 'includes/config.inc.php';
 ?>

<section class="stats--header">
    <div class="stats-title">
        <h1>Team Overview</h1>
        <form class="season-form" action="includes/stats.inc.php" method="POST">
            <div class="stat-select">
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
                <i class="fa fa-caret-down"></i>
            </div>
            <button type="submit" name="change_team_view" id="change_team_view">View</button>
        </form>
    </div>
    <div class="stats-info">
        <div class="team-text">
            <?php
                $team_id = $_SESSION['team_id'];
                
                // if team_id is not 999 then display the team info
                if ($team_id !== 999) {
                    $sql = "SELECT * FROM teams WHERE id = '$team_id'";
                    $result = mysqli_query($conn, $sql);

                    $team_row = mysqli_fetch_assoc($result);

                    $gp = $team_row['wins'] + $team_row['losses'] + $team_row['ties'];

                    echo "<h1>".$team_row['team_name']."</h1>";
                    echo "<p class=\"record\">".$team_row['wins']."<span> W</span> - ".$team_row['losses']."<span> L</span> - ".$team_row['ties']."<span> T</span></p>";
                    echo "<p class=\"record small-rec\">".$team_row['points']." pts, ".$gp." gp";
                    echo "<h4>Last 5 Games:</h4>";
                // display add team message
                } else {
                    echo "<p class=\"no-team-msg\">Add some teams to see them here!</p>";
                }
            ?>
        </div>
        <table class="team-stat-table">
            <col class="team-col1">
            <col class="team-col2">
            <col class="team-col3">
            <col class="team-col4">
            <col class="team-col5">
            <thead>
                <tr class="header-row team-header">
                    <th>Points For</th>
                    <th></th>
                    <th>Points Against</th>
                    <th class="centered">Against</th>
                    <th class="centered">Date</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $count = 0;
                $MAX_DISPLAY = 5;
                $team_id = $_SESSION['team_id'];

                $sql = "SELECT * FROM matches WHERE home_team_id = $team_id OR away_team_id = $team_id";
                $match_info = mysqli_query($conn, $sql);

                if (mysqli_num_rows($match_info) > 0) {
                    while ($match = mysqli_fetch_assoc($match_info) and $count < $MAX_DISPLAY) {
                        // If home team
                        if ($match['home_team_id'] == $team_id) {
                            // find away team's name
                            $awayid = $match['away_team_id'];
                            $sql = "SELECT team_name FROM teams WHERE id = $awayid";
                            $team = mysqli_query($conn, $sql);
                            $opposing_name = mysqli_fetch_row($team);

                            echo "<tr>";
                            echo "<td class=\"centered\">".$match['home_score']."</td>";
                            echo "<td class=\"centered\">-</td>";
                            echo "<td class=\"centered\">".$match['away_score']."</td>";
                            echo "<td class=\"name-col\">".$opposing_name[0]."</td>";
                            echo "<td class=\"centered\">".$match['dateCreated']."</td>";
                            echo"</tr>";
                            $count += 1;

                        // if away team
                        } else if ($match['away_team_id'] == $team_id) {
                            // find home team's name
                            $homeid = $match['home_team_id'];
                            $sql = "SELECT team_name FROM teams WHERE id = $homeid";
                            $team = mysqli_query($conn, $sql);
                            $opposing_name = mysqli_fetch_row($team);

                            echo "<tr>";
                            echo "<td class=\"centered\">".$match['away_score']."</td>";
                            echo "<td class=\"centered\">-</td>";
                            echo "<td class=\"centered\">".$match['home_score']."</td>";
                            echo "<td class=\"centered\">".$opposing_name[0]."</td>";
                            echo "<td class=\"centered\">".$match['dateCreated']."</td>";
                            echo "</tr>";
                            $count += 1;
                        }
                    }
                }

                // $sql = "SELECT home_team_id, away_team_id, home_score, away_score, dateCreated FROM matches WHERE season_id = $sesh_id ORDER BY dateCreated DESC";
                // $matches_result = mysqli_query($conn, $sql);

                // if (mysqli_num_rows($matches_result) > 0) {
                //     while ($row = mysqli_fetch_assoc($matches_result) and $count < $MAX_DISPLAY) {

                //         // get their team names through another sql query
                //         // look into replacing this with a join query
                //         $home_id = $row['home_team_id'];
                //         $away_id = $row['away_team_id'];

                //         $sql = "SELECT team_name FROM teams WHERE id='$home_id'";
                //         $hn_query = mysqli_query($conn, $sql);
                //         $home_name = mysqli_fetch_row($hn_query);

                //         $sql = "SELECT team_name FROM teams WHERE id='$away_id'";
                //         $an_query = mysqli_query($conn, $sql);
                //         $away_name = mysqli_fetch_row($an_query);

                //         echo"<tr>";
                //         echo"<td class=\"name-col\">".$home_name[0]."</td>";
                //         echo"<td class=\"centered\">".$row['home_score']."</td>";
                //         echo"<td class=\"centered\">VS.</td>";
                //         echo"<td class=\"centered\">".$row['away_score']."</td>";
                //         echo"<td class=\"name-col right\">".$away_name[0]."</td>";
                //         echo"<td class=\"centered\">".$row['dateCreated']."</td>";
                //         echo"</tr>";

                //         $count += 1;
                //     }
                //}
            ?>
            </tbody>
        </table>
    </div>
</section>