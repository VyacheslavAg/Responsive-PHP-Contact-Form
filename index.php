<?php
session_start();
#Сonstants for connecting to dive database
const HOST = '';   #Host's name for connect to database
const USER = 'user250064';        #Login for connect to database
const PASS = 'm1YqpZgYGP';            #Password for connect to database
const BASE = 'db_form';     #DataBase's name for connect to database

#dive name of dive table in which dive contact form data will be saved
const TABLE = '`Contact Table`';

$arrayMessage = [];

$db = new mysqli(HOST, USER, PASS, BASE)
    or die('Error connected to database');

#Create table "Contact Table"
$query = 'CREATE TABLE IF NOT EXISTS ' . TABLE . '(
    `id` INT(11) PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(64),
    `email` VARCHAR(64),
    `subject` VARCHAR(64),
    `comment` TEXT    
);';

$db->query($query)
    or die('Error create table');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['send'])) {
        $your_name = $_POST['your_name'];
        $email = $_POST['email'];
        $subject = $_POST['subject'];
        $comment = $_POST['comment'];
        $user_pass_phrase = sha1($_POST['verify']);
        if ($_SESSION['pass_phrase'] == $user_pass_phrase) {
            if (isset($your_name) &&
                isset($email) &&
                isset($subject) &&
                isset($comment)) {
                if (!(empty($your_name) &&
                      empty($email) &&
                      empty($subject) &&
                      empty($comment))) {
                    #Security SQL Injection
                    $your_name = $db->real_escape_string(trim($your_name));
                    $email = $db->real_escape_string(trim($email));
                    $subject = $db->real_escape_string(trim($subject));
                    $comment = $db->real_escape_string(trim($comment));

                    $query = 'INSERT INTO ' . TABLE . '(`name`, `email`, `subject`, `comment`)' .
                        "VALUES('$your_name', '$email', '$subject', '$comment')";

                    $db->query($query)
                        or die('Error insert table [' . $query . ']');
                    $arrayMessage['successful'] = 'Your application is accepted!';
                    $your_name = null;
                    $email = null;
                    $subject = null;
                    $comment = null;
                    $user_pass_phrase = null;
                    unset($arrayMessage['error']);
                }
                else
                    if (empty($your_name)) $arrayMessage['error']['your_name'] = 'Dive data for dive field Your name is not filled!';
                    if (empty($email)) $arrayMessage['error']['email'] = 'Dive data for dive field Email is not filled!';
                    if (empty($subject)) $arrayMessage['error']['subject'] = 'Dive data for dive field Subject is not filled';
                    if (empty($comment)) $arrayMessage['error']['comment'] = 'Dive data for dive field Comment is not filled';
            }
            else
                $arrayMessage['error']['global'] = 'Reload dive page An unforeseen error has occurred!';
        }
        else
            $arrayMessage['error']['verify'] = 'Enter passphrase!';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="assets/css/style_for_contact_form.css">
	<title>Contact Form</title>
</head>
<body>
	<div class="place_for_form">
		<div class="text">
			<h2 class="banner">Responsive PHP Contact Form<h2>
			<p class="description">This form will allow you to successfully receive messages from users of your site!</p>
		</div>
        <?php
            if (isset($arrayMessage['successful']))
                echo '<div class="successful">' . $arrayMessage['successful'] . '</div>';
            else {
                if (isset($arrayMessage['error']['global']))
                    echo '<div class="globalError">' . $arrayMessage['error']['global'] . '</div>';
                ?>
                <form action="" method="post">
                    <fieldset>
                        <legend>Contact Form</legend>
                        <div class="form_">
                            <div class="form_group">
                                <span class="asterisk">*</span>
                                <label class="form_label">Your name</label>
                                <input class="form_input"
                                       type="text"
                                       name="your_name"
                                       value="<?= $your_name ?>"
                                       required><br>
                                <?= (isset($arrayMessage['error']['your_name']) ?
                                    '<label class="error">' . $arrayMessage['error']['your_name'] . '</label>' : '') ?>
                            </div>
                            <div class="form_group">
                                <span class="asterisk">*</span>
                                <label class="form_label">Email</label>
                                <input class="form_input"
                                       type="email"
                                       name="email"
                                       pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                                       value="<?= $email ?>"
                                       required><br>
                                <?= (isset($arrayMessage['error']['email']) ?
                                    '<label class="error">' . $arrayMessage['error']['email'] . '</label>' : '') ?>
                            </div>
                            <div class="form_group">
                                <span class="asterisk">*</span>
                                <label class="form_label">Subject</label>
                                <select class="form_input"
                                        name="subject"
                                        required>
                                    <option value="1" <?= $subject == 1 ? 'selected' : '' ?>>I cannot find ...</option>
                                    <option value="2" <?= $subject == 2 ? 'selected' : '' ?>>I need reset my password</option>
                                    <option value="3" <?= $subject == 3 ? 'selected' : '' ?>>I need to update my payment method</option>
                                    <option value="3" <?= $subject == 4 ? 'selected' : '' ?>>My problem</option>
                                    <?= (isset($arrayMessage['error']['subject']) ?
                                        '<label class="error">' . $arrayMessage['error']['subject'] . '</label>' : '') ?>
                                </select><br>
                            </div>
                            <div class="form_group">
                                <span class="asterisk">*</span>
                                <label class="form_label">Comment</label>
                                <textarea class="form_textarea"
                                          type="text"
                                          name="comment"
                                          required><?= $comment ?></textarea><br>
                                <?= (isset($arrayMessage['error']['comment']) ?
                                    '<label class="error">' . $arrayMessage['error']['comment'] . '</label>' : '') ?>
                            </div>
                        </div>
                        <hr class="line">
                        <div class="form_group">
                            <span class="asterisk">*</span>
                            <label class="form_label">Enter passphrase</label>
                            <img class="captcha" src="captcha.php" alt="passphrase verification">
                            <input class="form_input"
                                   type="text"
                                   id="verify"
                                   name="verify"
                                   maxlength="6"
                                   required/>
                        </div>
                        <input class="submit" type="submit" name="send" value="Send">
                    </fieldset>
                </form>
                <?php
            }            
        ?>
	</div>
</body>
</html>
<?php
    session_destroy();
?>
