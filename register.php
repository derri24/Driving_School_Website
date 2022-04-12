<?php
$data = $_POST;
if (isset($_POST['submit'])) {
    $errors = array();
    if (empty($data['name'])) {
        $errors[] = 'Ошибка! Вы не заполнили Имя';
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