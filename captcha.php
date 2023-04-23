<?php
session_start();

define('CAPTCHA_NUMCHARS', 3);
define('CAPTCHA_WIDTH', 108);
define('CAPTCHA_HEIGHT', 48);

#Create security phrase
$pass_phrase = "";
for ($i = 0; $i < CAPTCHA_NUMCHARS; $i++) {
    $pass_phrase .= chr(rand(97, 122)) . rand(0, 9);
}

$_SESSION['pass_phrase'] = sha1($pass_phrase);

#Create image
$img = imagecreatetruecolor(CAPTCHA_WIDTH, CAPTCHA_HEIGHT);

#Setting colors
$bg_color = imagecolorallocate($img, 255, 255, 255);
$text_color = imagecolorallocate($img, 0, 0, 0);
$graphic_color = imagecolorallocate($img, 64, 64, 64);

#Fill background
imagefilledrectangle($img, 0, 0, CAPTCHA_WIDTH, CAPTCHA_HEIGHT, $bg_color);

#Writing the text of the passphrase
imagettftext($img, 18, 5, 5, CAPTCHA_HEIGHT - 15, $text_color,
    'assets/fonts/Courier New Bold.ttf', $pass_phrase);

#Drawing lines
for ($i = 0; $i < 5; $i++) {
    imageline($img, 0, rand() % CAPTCHA_HEIGHT, CAPTCHA_WIDTH,
        rand() % CAPTCHA_HEIGHT, $graphic_color);
}

#Drawing dots
for ($i = 0; $i < 50; $i++) {
    imagesetpixel($img, rand() % CAPTCHA_WIDTH,
        rand() % CAPTCHA_HEIGHT, $graphic_color);
}

#Select image in PNG
header("Content-type: image/png");
imagepng($img);

imagedestroy($img);

