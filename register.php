<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/Mailer/PHPMailer.php';
require_once __DIR__ . '/Mailer/SMTP.php';
require_once  __DIR__. '/Mailer/Exception.php';

$data = $_POST;
$email_regex = '/\w+@\w+\.\w+/';
if (isset($_POST['submit'])) {
    $errors = array();
    if (empty($data['name'])) {
        $errors[] = 'Ошибка! Вы не заполнили Имя';
    } else if (empty($data['email'])) {
        $errors[] = 'Ошибка! Вы не заполнили Email';
    } else if (!preg_match_all($email_regex, $data['email'])) {
        $errors[] = 'Ошибка! Email должен иметь формат "ivanov@.com"';
    } else if (empty($data['password1'])) {
        $errors[] = 'Ошибка! Вы не заполнили пароль';
    } else if (empty($data['password2'])) {
        $errors[] = 'Ошибка! Вы не повторили пароль';
    } else if ($data['password2'] != $data['password1']) {
        $errors[] = 'Ошибка! Введённые пароли отличаются';
    } else if (strlen($data['password1']) < 8) {
        $errors[] = 'Ошибка! Пароль должен быть длиной 8 и боее символов';
    }
    if (empty($errors)) {

        $db = new PDO('sqlite:testDB.db');
        $name = $data["name"];
        $password = password_hash($data["password1"], PASSWORD_DEFAULT);
        $check_result = $db->query("SELECT * FROM Users WHERE Name = '$name'")->fetchAll();
        if (count($check_result) == 0) {
            $sql = "INSERT INTO Users (Name, Password) VALUES ('$name', '$password')";
            $result = $db->query($sql);
            setcookie("LoginCookie", $data["name"]);

            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.yandex.ru';
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;;
            $mail->Port = 465;
            $mail->Username = 'lab7.vt';
            $mail->Password = 'Hk341KHkb41hl';
            $mail_to = $data['email'];
            $mail->setFrom('lab7.vt@yandex.ru', 'ljjj ihkh');
            $mail->addAddress($mail_to, 'Receiver Name');
            $mail->addReplyTo('lab7.vt@yandex.ru', 'ljjj ihkh');
            $mail->IsHTML(true);

            $mail->Subject = "Регистрация";
            $mail->Body = '<h1>Добро пожаловать на сайт автошколы http://abv-azbuka.com/</h1> Регистрация прошла успешно';
            $mail->AltBody = 'Plain text message body for non-HTML email client. Gmail SMTP email body.';

            $mail->send();


            echo '<div class="check-label background-color-green">Регистрация прошла успешно</div>';
        }
        else
            echo '<div class="check-label background-color-red">Пользователь с указанным именем уже зарегестрирован</div>';
    } else {
        echo '<div class="check-label background-color-red">' . array_shift($errors) . '</div>';
    }
}


$main_template = file_get_contents("templates/main.html");
$register_form = file_get_contents("templates/register/register.html");

$main_template = str_replace('{Content}', $register_form, $main_template);
if (isset($_COOKIE['LoginCookie'])) {
    $user_name_template = file_get_contents("templates/user_name.html");
    $user_name_template = str_replace('{Name}', $_COOKIE['LoginCookie'], $user_name_template);
    $main_template = str_replace('{User}', $user_name_template, $main_template);
}
else
$main_template = str_replace('{User}', file_get_contents("templates/register.html"), $main_template);
echo $main_template;
