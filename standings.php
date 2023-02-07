<!-- main page of the site where user is redirected upon login -->
<?php 
require 'header.php';
include_once 'includes/config.inc.php';
?>

<section class="view--container">
    <div class="view--header">
        <h1>Standings</h1>
        <!-- get season view form -->
        <form class="season-form" action="includes/standings.inc.php" method="POST">
            <label for="season_choice">Season: </label>
            <select name="season_choice">
                <!-- php code to get seasons -->
                <?php
                    $league_id = $_SESSION['league_id'];
                    $sql = "SELECT id, season_name FROM seasons WHERE league_id = $league_id";
                    $result = mysqli_query($conn, $sql);

                    // if seasons exist iterate through
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            // if season selected show it
                            if ($row["id"] == $_SESSION['season_id']) {
                                echo "<option selected>".$row["season_name"]."</option>";
                            } else {
                                echo "<option>".$row["season_name"]."</option>";
                            }
                        }
                    }
                ?>
            </select>
            <button type="submit" name="change_standings" value="change_standings_season">View</button>
        </form>
        <!-- php to handle error/success flashes -->
        <?php 
            if (isset($_GET["error"])) {
                if ($_GET["error"] == "stmtfailed") {
                    echo "<p class=\"flash fail\">Invalid season</p>";
                }
            }

            if (isset($_GET["success"])) {
                if ($_GET["success"] == "seasonupdated") {
                    echo "<p class=\"flash success\">Season updated!</p>";
                }
            }
        ?>
    </div>
    
    <table class="view--table">
        <!-- check the column widths -->
        <col class="column-one">
        <col class="column-two">
        <col class="column-three">
        <col class="column-four">
        <col class="column-five">
        <!-- table header -->
        <thead>
            <tr class="header-row">
                <th>Rank</th>
                <th class="name-col">Team Name</th>
                <th>Pts</th>
                <th>GP</th>
                <th>Record</th>
            </tr>
        </thead>
        <!-- table body -->
        <tbody>
            <!-- php code to loop through season teams -->
            <?php
                $seshid = $_SESSION['season_id'];

                $sql = "SELECT team_name, wins, losses, ties, points FROM teams WHERE season_id = '$seshid' ORDER BY points DESC";
                $team_result = mysqli_query($conn, $sql);

                $ranking = 1;

                if (mysqli_num_rows($team_result) > 0) {
                    while ($row = mysqli_fetch_assoc($team_result)) {

                        // get pts and gp
                        $gp = $row['wins'] + $row['losses'] + $row['ties'];

                        // add new table row
                        echo "<tr>";
                        echo "<td class=\"centered\">".$ranking.".</td>";
                        echo "<td class=\"name-col\">".$row['team_name']."</td>";
                        echo "<td class=\"centered\">".$row['points']."</td>";
                        echo "<td class=\"centered\">".$gp."</td>";
                        echo "<td class=\"centered\">".$row['wins']."-".$row['losses']."-".$row['ties']."</td>";
                        echo "</tr>";

                        $ranking += 1;
                    }
                }
            ?>
        </tbody>
    </table>
</section>


</body>
</html>