<?php
$db = new PDO('mysql:host=localhost;dbname=teacher;charset=utf8', 'teacher', 'greet7rain');

function p($s) {
	return urldecode($_POST[$s]);
}
function g($s) {
	return urldecode($_GET[$s]);
}

$studentId = 1;

function currentStudent() {
	global $studentId;
	return $studentId;
}

function query($sql, $params = array()) {
	global $db;
	$stmt = $db->prepare($sql);
	$stmt->execute($params);
	return $stmt->fetchAll();
}

function checkIfMastered($scores) {
	// needs to check if any of the scores are above the 'mastered' level for a quiz
	// if so then (if we haven't already done so) need to get data from quiz_gives_skill and 
	// insert records in student_has_skill for new skills learnt,
	// don't update records but keep historical, and select max later on.
	foreach ($scores as $row) {
		if ($row['score'] >= $row['score_mastered']) {
			foreach (query('select * from quiz_gives_skill qgs where qgs.quiz = :quiz', 
				array(':quiz' => $row['quiz'])) as $qgs) {
				query('insert ignore into student_has_skill (student, skill, level) 
						values (:student, :skill, :level)',
					array(':student' => currentStudent(), ':skill' => $qgs['skill'], ':level' => $qgs['level']));
			}
		}
	}
}

function getAvailableQuizes() {
	// quizes that are available and give a skill higher than what we've got already...
	query('select q.* from quiz q 
			inner join quiz_needs_skill qns on q.id = qns.quiz
            inner join quiz_gives_skill qgs on q.id = qgs.quiz
			inner join student_has_skill shs on qns.skill = shs.skill and shs.level >= qns.level
            inner join student_has_skill shs2 on shs2.skill = shs.skill and shs2.level < qgs.level
			where shs.student = :student', 
			array(':student' => currentStudent()));

	query('select q.* from quiz q 
			inner join quiz_needs_skill qns on q.id = qns.quiz
			inner join student_has_skill shs on qns.skill = shs.skill and shs.level >= qns.level
			where shs.student = :student', 
			array(':student' => currentStudent()));

	query('select * from quiz 
			where id NOT IN (SELECT quiz FROM quiz_needs_skill)');
}

function getStudentScores($student) {
	$sql = "
SELECT
    a.quiz,
    q.name,
    AVG(score) AS score, 
    AVG(time_taken) AS time_taken,
    q.score_mastered 
FROM (
    SELECT 
    	student, 
    	quiz, 
    	IF(@uid = (@uid := student) and @qid = (@qid := quiz), @auto:=@auto + 1, @auto := 1) autoNo, 
    	score, 
    	time_taken
    FROM 
    	result, 
    	(SELECT @uid := 0, @qid := 0, @auto:= 1) A
    WHERE
    	student = :student
    ORDER BY 
    	student, quiz, datetime DESC
    ) AS a
	INNER JOIN quiz q ON q.id = a.quiz
WHERE 
	autoNo <= 10
GROUP BY 
	student, 
    quiz;
	";
	return query($sql, array(':student' => $student));
}

function array2Html($array)
{
	$out = '<table><tr>';
	foreach ($array[0] as $key => $col) if (!is_int($key)) {
			$out .= "<th>$key</th>";
		}
	$out .= '</tr>';
	foreach ($array as $row) {
		$out .= '<tr>';
		foreach ($row as $key => $col) if (!is_int($key)) {
			$out .= "<td>$col</td>";
		}
		$out .= '</tr>';
	}
    return $out . '</table>'; 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (p('action') == "logResult") {
		$stmt = $db->prepare("INSERT INTO result (student, quiz, time_taken, score) VALUES (?,?,?,?)");
		$stmt->execute(array(p('student'), p('quiz'), p('time_taken'), p('score')));
		//echo print_r($_POST);
		//echo print_r(getStudentScores($studentId));
		//$stmt->rowCount();
		$scores = getStudentScores($studentId);
		checkIfMastered($scores);
		echo array2Html($scores);
	}
	exit;
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {

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
    //echo $row['id'].' '.$row['url']; 
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
    finishedQuiz = false;
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

var finishedQuiz = true;

function complete(score) {
	if (finishedQuiz == false) {
		finishedQuiz = true;
		var timeEnd = new Date().getTime() / 1000;
		logResult(1, currentLesson, score, timeEnd - timeStart);
		playVideo();
	}
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
	        document.getElementById("status").innerHTML = return_data;
	    }
	}
	hr.send(vars);
}
</script>
</body>
</html>
