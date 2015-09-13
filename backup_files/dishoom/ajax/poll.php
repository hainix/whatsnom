<?php
include_once '../lib/core/base.php';
include_once '../lib/core/funcs.php';
include_once '../lib/display/render.php';
include_once '../lib/display/units.php';

if (!idx($_POST, 'poll') || !idx($_POST, 'pollid')) {
  $query = mysql_query("SELECT id, question FROM poll_questions ORDER BY RAND() LIMIT 1");
  while ($row = mysql_fetch_assoc($query)) {
    // Display question
    echo '<h4>'.render_mentions_text($row['question']).'</h4>';
    $poll_id = $row['id'];
  }
  if (idx($_GET,'result') == 1 || ($_COOKIE["voted".$poll_id] == 'yes')) {
    // If already voted or asked for result
    show_poll_results($poll_id);
    exit;
  } else {
    // Display options with radio buttons
    $query = mysql_query("SELECT id, value FROM poll_options WHERE poll_id=$poll_id order by RAND()");
    if (mysql_num_rows($query)) {
      echo '<div id="formcontainer" ><form method="post" id="pollform" action="'.$_SERVER['PHP_SELF'].'" >';
      echo '<input type="hidden" name="pollid" value="'.$poll_id.'" />';
      while ($row = mysql_fetch_assoc($query)) {
        echo '<input type="radio" name="poll" value="'.$row['id']
          .'" id="option-'.$row['id'].'" />'
          .'<label for="option-'.$row['id'].'" >'.render_mentions_text($row['value']).'</label><br/>';
      }
      echo '<p><input type="submit"  value="Vote"  class="small red button"/></p></form>';
      echo '<a href="'.$_SERVER['PHP_SELF'].'?result=1" id="viewresult">View result</a>';
      echo '</div>';
    }
  }
} else {
  if (idx($_COOKIE, "voted".$_POST['pollid']) != 'yes') {
    // Check if selected option value is there in database?
    $query = mysql_query("SELECT * FROM poll_options WHERE id='".intval($_POST["poll"])."'");
    if (mysql_num_rows($query)) {
      $query = "INSERT INTO poll_votes(option_id, voted_on, ip) VALUES('".$_POST["poll"]."', '"
        . date('Y-m-d H:i:s')."', '".$_SERVER['REMOTE_ADDR']."')";
      if (mysql_query($query)) {
        // Vote added to database
        setcookie("voted".$_POST['pollid'], 'yes', time()+86400*300);
      } else {
        echo "There was some error processing the query: ".mysql_error();
      }
    }
  }
  show_poll_results(intval($_POST['pollid']));
}
function show_poll_results($poll_id){
  global $link;
  $query = mysql_query("SELECT COUNT(*) as totalvotes FROM poll_votes WHERE option_id "
                       ."IN(SELECT id FROM poll_options WHERE poll_id = '$poll_id')");
  while ($row = mysql_fetch_assoc($query)){
    $total = $row['totalvotes'];
  }
  $query = mysql_query("SELECT poll_options.id, poll_options.value, COUNT(*) as votes FROM poll_votes, "
                       ."poll_options WHERE poll_votes.option_id=poll_options.id AND poll_votes.option_id"
                       ." IN(SELECT id FROM poll_options WHERE poll_id='$poll_id') GROUP BY "
                       ."poll_votes.option_id");
  $results_html = "<div id='poll-results'><dl class='graph'>\n";
  while ($row = mysql_fetch_assoc($query)) {
    $percent = round(($row['votes']*100) / $total);
    $results_html .= "<dt class='bar-title'>". render_mentions_text($row['value'])
      ."</dt><dd class='bar-container'><div id='bar". $row['id'] ."'style='width:$percent%; ";
    if (idx($_POST, 'poll') == $row['id']) {
      $results_html .= 'background-color:#0066cc; ';
    }
    $results_html .= "'>&nbsp;"
      ."</div><strong>$percent%</strong></dd>\n";
  }
  $results_html .= '</dl></div>';
  //  $results_html .= "</dl><h4>Total Votes: ". $total_votes ."</h4></div>\n";
  echo $results_html;
}

