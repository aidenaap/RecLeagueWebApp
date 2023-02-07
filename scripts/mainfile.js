// === League pop-up forms === //
// new league form
function openNewLeagueForm() {
    document.getElementById("new_league_form").style.display = 'block';
}

function closeNewLeagueForm() {
    document.getElementById("new_league_form").style.display = 'none';
}

// delete league form
function openDeleteLeagueForm() {
    document.getElementById("delete_league_form").style.display = 'block';
}

function closeDeleteLeagueForm() {
    document.getElementById("delete_league_form").style.display = 'none';
}

// confirm league delete
function openDeleteLeagueConfirm() {
    document.getElementById("confirm-delete-league").style.display = 'flex';
}

function closeDeleteLeagueConfirm() {
    document.getElementById("confirm-delete-league").style.display = 'none';
}

// === Season pop-up forms === //
// new season form
function openNewSeasonForm() {
    document.getElementById("new_season_form").style.display = 'block';
}

function closeNewSeasonForm() {
    document.getElementById("new_season_form").style.display = 'none';
}

// delete sesason form
function openDeleteSeasonForm() {
    document.getElementById("delete_season_form").style.display = 'block';
}

function closeDeleteSeasonForm() {
    document.getElementById("delete_season_form").style.display = 'none';
}

// confirm season delete
function openDeleteSeasonConfirm() {
    console.log("meme");
    document.getElementById("confirm-delete-season").style.display = 'flex';
}

function closeDeleteSeasonConfirm() {
    document.getElementById("confirm-delete-season").style.display = 'none';
}

// === edit teams === //
// edit team popup
function openEditForm(wins, losses, ties, fees, id) {
    document.getElementById("edit-team-form").style.display = 'block';
    document.getElementById("edit-team-wins").value = wins;
    document.getElementById("edit-team-losses").value = losses;
    document.getElementById("edit-team-ties").value = ties;
    document.getElementById("edit-team-fees").value = fees;
    document.getElementById("edit-team-id").value = id;
    // console.log("clicked");
}

function closeEditTeamForm() {
    document.getElementById("edit-team-form").style.display = 'none';
}

// delete team popup
function openDeleteTeamForm() {
    document.getElementById("delete_team").style.display = 'block';
}

function closeDeleteTeamForm() {
    document.getElementById("delete_team").style.display = 'none';
}

// delete game popup
function openDeleteGameForm() {
    document.getElementById("delete_game").style.display = 'block';
}

function closeDeleteGameForm() {
    document.getElementById("delete_game").style.display = 'none';
}