<?php
$db = new PDO('mysql:host=localhost;dbname=teacher;charset=utf8', 'teacher', 'greet7rain');

function g($s) {
	return urldecode($_POST[$s]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$action = $_POST["action"];
	if ($action == "logResult") {
		$stmt = $db->prepare("INSERT INTO result (student, quiz, time_taken, score) VALUES (?,?,?,?)");
		$stmt->execute(array(g('student'), g('quiz'), g('time_taken'), g('score')));
		echo print_r($_POST);
		echo "affected rows = " . $stmt->rowCount();
	}
	exit;
}
?>

<html>
<head>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script type="text/javascript" src="mespeak.js"></script>
    <script src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
        meSpeak.loadConfig("mespeak_config.json");
        meSpeak.loadVoice("voices/en/en.json");
        meSpeak.speak('hello');
    </script>
    <style>
		body {
		    background-color: black;
		}

		body {
		    text-align: center;
		    font-family: sans-serif;
		    font-weight: bold;
		    font-size: 20px;
		}

		#quiz {
		    display: none;
		}

		#quiz iframe {
			height: 90%; 
			width: 90%;
			border: none;
		}
    </style>
</head>
<body>
<div id="status"></div>

<?php 
foreach($db->query('SELECT * FROM quiz') as $row) {
    echo $row['id'].' '.$row['url']; 
}

?>
<br>
<div id="quiz">
	<iframe src=""></iframe>
</div>
<div id="player"></div>
<select id='interval'>
    <option value="30000">30s</option>
    <option value="60000">1min</option>
    <option value="90000">1m30s</option>
    <option value="120000">2mins</option>
    <option value="300000">5mins</option>
    <option value="600000">10mins</option>
</select>
<script>
var tag = document.createElement('script');
tag.src = "https://www.youtube.com/iframe_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
var player;

function onYouTubeIframeAPIReady() {
    player = new YT.Player('player', {
        height: '490',
        width: '840',
        playerVars: {
            'autoplay': 1,
            // 'controls': 0,
            'disablekb': 0
        },
        videoId: 'FYhEy8bLzMo', //'fhrkDquQ48Q',
        events: {
            'onReady': onPlayerReady,
            'onStateChange': onPlayerStateChange
        }
    });
}

var done = false;

function onPlayerReady (event) { 
	event.target.playVideo(); 
}


function onPlayerStateChange(event) {
    if (event.data == YT.PlayerState.PLAYING && !done) {
        setTimeout(timerTimeout, 9000);
        done = true;
    }
}

function playVideo() {
    if (player) {
        $('#player').show();
        player.playVideo();
        var interval = $('#interval').val();
        setTimeout(timerTimeout, interval);
        $('#quiz').hide();
        $('body').css('background-color', 'black');
    }
}

var count = 0;

var timeStart = 0;

var currentLesson = 0;

function getNextLesson() {
	if (count % 2 == 0) {
		currentLesson = 1;
		return "lessons/learning_keys1.html";
		
	}
	else {
		currentLesson = 2;
		return "lessons/counting1.html";
		
	}
}

function timerTimeout() {
    player.pauseVideo();
    $('#player').hide();
    $('#quiz').show();
    timeStart = new Date().getTime() / 1000;
    count++;
    $('#quiz iframe').prop('src', getNextLesson());
    $('body').css('background-color', 'white');
}

google.load('search', '1');
var imageSearch = 0;

function searchImages(phrase, callback) {
    imageSearch = new google.search.ImageSearch();
    imageSearch.setRestriction(
        google.search.Search.RESTRICT_SAFESEARCH,
        google.search.Search.SAFESEARCH_STRICT
    );
    imageSearch.setSearchCompleteCallback(this, callback, null);
    imageSearch.execute(phrase);
}

function speak(phrase, options, callback) {
    meSpeak.stop();
    meSpeak.speak(phrase, options, callback);
}

function complete(score) {
	var timeEnd = new Date().getTime() / 1000;
	logResult(1, currentLesson, score, timeEnd - timeStart);
	playVideo();
}

function logResult(student, quiz, score, time_taken) {
	var hr = new XMLHttpRequest();
	var vars = "action=logResult&student="+encodeURIComponent(student)+
	"&quiz="+encodeURIComponent(quiz)+
	"&score="+encodeURIComponent(score)+
	"&time_taken="+encodeURIComponent(time_taken);
	hr.open("POST", "index.php", true);
	hr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	hr.onreadystatechange = function() {
	    if(hr.readyState == 4 && hr.status == 200) {
	    	console.log(hr);
	        var return_data = hr.responseText;
	        //document.getElementById("status").innerHTML = return_data;
	    }
	}
	hr.send(vars);
}
</script>
</body>
</html>
