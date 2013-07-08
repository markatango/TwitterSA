<?php

require_once('^secrets\secrets.php');
require_once('..\twitteroauth\twitteroauth.php');
require_once('MySQLStrings.php');
require_once('PHPHelpers.php');


//Set up MySQL database
$dScriptsDir = 'MySQLScripts\\';

$link = mysqli_connect('localhost', $mysql_user, $mysql_pw );
if ($link === FALSE) {
    die('Could not connect: ' . mysqli_connect_error() . "<br/>\n");
}
else {
	echo "Connected to MySQL" . "<br/>\n";
}

if (mysqli_query($link, 'USE twitterFeed') === FALSE) {
	die('Could not connect: ' . mysqli_error($link) . "<br/>\n");
}
else {
	echo "Using twitterFeed"  . "<br/>\n";
}

$connection = new TwitterOAuth($consumer_key, $consumer_secret, $oauth_access_token,
$oauth_access_token_secret);

$uri_params = array("q" => "AAA+road", "count" => 100);

$search = $connection->get('search/tweets', $uri_params);
//print_r($search);


$tweetsPerHr = Array();
foreach($search->statuses as $tweet) {
	$currentHr = strtotime($tweet->created_at) /3600;
	$tweetsPerHr [ $currentHr ]++;
}
ksort($tweetsPerHr);

//print_r($tweetsPerHr);
//----- Plot the graph ----
//Import Google API for visualization
?>
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
	google.load('visualization', '1', {packages: ['corechart']});
</script>
<?php
// Define [x,y] coordinates fo every point in the graph
$graph = "[";
foreach($tweetsPerHr as $hour => $nbTweets) {
	$hourTxt = date("j M g:00 A", $hour*3600);
	$graph .= "['$hourTxt', $nbTweets],";
}
$graph .= "]";
//echo $graph;
//output graph
?>
<script type="text/javascript">
	 function drawVisualization() {
	  // Create and popultat the data table
	  var data = google.visualization.arrayToDataTable(<?php echo $graph; ?>);
	  
	  // Create and draw the visulatioandino
	  new google.visualization.LineChart(document.getElementById('visualization')).
		draw(data, {pointSize: 5, legend: {'position': 'none'} });
	 }
	google.setOnLoadCallback(drawVisualization);
</script>

<h1>Tweets per hour</h1>
<div id="visualization" style="width:1800px; height: 500px;"></div>

<?php
/* get last max_id: $l_max_id_str */
if ($rMaxID = mysqli_query($link,'SELECT MAX(id_str) AS id_str FROM tweetdata')) {
    $obj = mysqli_fetch_object($rMaxID);
	$l_max_id_str = $obj->id_str;
    mysqli_free_result($rMaxID);
}

$count = 0;
foreach($search->statuses as $tweet) {
	$count++;
	// echo $count . '):' . "<strong>{$tweet->user->name}</strong>" .
	 // ' tweeted: ' . 
	 // "'<u>{$tweet->text}</u>'" . ' ' . 
	 // $tweet->created_at  . ' ' . 
	 // "from: " . "'<u>{$tweet->user->location}</u>'" . ' ' . 
	 // "id: " . $tweet->id_str . "<br />\n"; 
	 
	 $sInsertTweetData = "INSERT INTO tweetData VALUES (" . 
					"'" . substr(clean($tweet->user->name),0,19) . "', " . 
					"'" . substr(clean($tweet->text),0,139) . "', " . 
					"'" . substr(clean($tweet->user->location),0,139) . "', " . 
					"'" . substr(clean($tweet->created_at),0,19) . "', " . 
					"'" . substr(clean($tweet->id_str),0,19) . "'" . ')';
	//echo $sInsertTweetData . "<br />\n"; 
	//echo "<br />\n"; 
	if (mysqli_query($link, $sInsertTweetData) === FALSE) {
		die("Cannot insert data because" . mysqli_error($link) . "<br/>\n");
	};
}
process_MySQL_script($link, $dScriptsDir . 'DedupAndDe-AAA.sql');

/* Get current max_id: $c_max_id_str*/
if ($rMaxID = mysqli_query($link,'SELECT MAX(id_str) AS id_str FROM tweetdata')) {
	$obj = mysqli_fetch_object($rMaxID);
	$c_max_id_str = $obj->id_str;
    mysqli_free_result($rMaxID);
}

/* Write last and current max_id and last and current since_id*/
$sInsertIDData = "INSERT INTO persist VALUES (" . 
					"'" . $c_max_id_str . "', " . 
					"'" . $l_max_id_str . "'" . ")";
					echo $sInsertIDData . "<br/>\n";
safe_query($link, $sInsertIDData);

if ($rMaxID = mysqli_query($link,'SELECT * FROM persist')) {

    /* fetch associative array */
    while ($obj = mysqli_fetch_object($rMaxID)) {
		echo "max_id: " . $obj->max_id . " | " . "last_ max_id: " . $obj->last_max_id . "<br/>\n";
    }
    /* free result set */
    mysqli_free_result($rMaxID);
}
else {
	echo "Empty max id_str " . "<br/>n";
}
/* close connection */
mysqli_close($link);
