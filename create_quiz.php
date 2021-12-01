<?php
session_start();

$menu = array(
    'home' => array('text'=>'Home', 'url'=>'index.php'),
    'login' => array('text'=>'Login', 'url'=>'login.php'),
    'register' => array('text'=>'Register', 'url'=>'register.php'),
    'take_quiz' => array('text'=>'Take quiz', 'url'=>'take_quiz.php'),
    'create_quiz' => array('text'=>'Create quiz', 'url'=>'create_quiz.php'),
    'modify_quiz' => array('text'=>'Modify quiz', 'url'=>'modify_quiz.php'),
    'logout' => array('text'=>'Log out', 'url'=>'logout.php'),
);

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    if ($_SESSION["is_staff"] === true) {
        $menu = array(
            'home' => array('text'=>'Home', 'url'=>'index.php'),
            'take_quiz' => array('text'=>'Take quiz', 'url'=>'take_quiz.php'),
            'create_quiz' => array('text'=>'Create quiz', 'url'=>'create_quiz.php'),
            'modify_quiz' => array('text'=>'Modify quiz', 'url'=>'modify_quiz.php'),
            'logout' => array('text'=>'Log out', 'url'=>'logout.php'),
        );
    } else {
        $menu = array(
            'home' => array('text'=>'Home', 'url'=>'index.php'),
            'take_quiz' => array('text'=>'Take quiz', 'url'=>'take_quiz.php'),
            'logout' => array('text'=>'Log out', 'url'=>'logout.php'),
        );
    }
} else {
    $menu = array(
        'home' => array('text'=>'Home', 'url'=>'index.php'),
        'login' => array('text'=>'Login', 'url'=>'login.php'),
        'register' => array('text'=>'Register', 'url'=>'register.php'),
    );
}

function generateMenu($items) {
    $html = "<div class=\"topnav\">\n";
    foreach($items as $item) {
        if (strpos($_SERVER['REQUEST_URI'], $item['url']) !== false) {
            $html .= "<a class=active href='{$item['url']}'>{$item['text']}</a>\n";
        } else {
            $html .= "<a href='{$item['url']}'>{$item['text']}</a>\n";
        }
        
    }
    $html .= "</div>\n";
    return $html;
}

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false || !isset($_SESSION["is_staff"]) || $_SESSION["is_staff"] === false){
    header("location: index.php?Message=" . urlencode("Only staff members can create a quiz."));
    exit;
}

# reset current quiz session
$_SESSION["quiz"] = [
    "author" => $_SESSION["uid"],
    "deleted" => false,
    "available" => false,
    "name" => "Quiz",
    "duration" => 0,
    "non-author modifiable" => false,
    "num questions" => 0,
    "questions" => [],
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    # preamble for quiz
    # field checking is already performed in the HTML

    # update session quiz details
    $_SESSION["quiz"]["available"] = strcmp($_POST["is_visible"], "on") === 0? true : false;
    $_SESSION["quiz"]["name"] = $_POST["quiz_title"];
    $_SESSION["quiz"]["duration"] = $_POST["quiz_time"];
    $_SESSION["quiz"]["non-author modifiable"] = strcmp($_POST["is_modifiable"], "on") === 0? true : false;
    $_SESSION["quiz"]["num questions"] = $_POST["num_questions"];

    # prompt for questions
}
?>

<!doctype html>
<html>
    <head>
        <link rel="stylesheet" href="styles.css">
        <title>Quizzy!</title>
    </head>
    <body>
        <?php 
        if (isset($_GET['Message'])) {
            echo '<script>alert("' . $_GET['Message'] . '");</script>';
        }

        echo GenerateMenu($menu);
        ?>

        <form action="create_quiz.php" method="post">
            
            <div class="container">
                <label for="quiz_title"><b>Quiz title</b></label>
                <input type="text" pattern=".*\S+.*" placeholder="Enter quiz title" name="quiz_title" required>

                <label for="num_questions"><b>Number of questions</b></label>
                <input type="number" min="1" step="1" pattern=".*\S+.*" name="num_questions" required>

                <label for="quiz_time"><b>Estimated time to complete (minutes)</b></label>
                <input type="number" min="0" step="1" name="quiz_time" required>

                <label>
                    <input type="checkbox" name="is_visible" checked>
                    <b>Visible to students?</b>
                </label>

                <label>
                    <input type="checkbox" name="is_modifiable">
                    <b>Allow other staff members to modify?</b>
                </label>

                <button type="submit">Next</button>



                <!-- <label for="psw"><b>Question text</b></label>
                <input type="password" placeholder="Enter password" name="psw" required>

                <label for="uid"><b>Answer a</b></label>
                <input type="text" placeholder="Enter username" name="uid" required>

                <label for="uid"><b>Answer b</b></label>
                <input type="text" placeholder="Enter username" name="uid" required>

                <label for="uid"><b>Answer c</b></label>
                <input type="text" placeholder="Enter username" name="uid" required>

                <label for="uid"><b>Answer d</b></label>
                <input type="text" placeholder="Enter username" name="uid" required>

                <label for="uid"><b>Correct answer</b></label>
                <input type="text" placeholder="Enter username" name="uid" required>

                <label for="uid"><b>Estimated time (minutes)</b></label>
                <input type="text" placeholder="Enter username" name="uid" required>

                <button type="submit">Next</button> -->

            </div>
        </form>

    </body>
</html>