<?php
$data = $_POST;
if (isset($_POST['submit'])) {
    $errors = array();
    if (empty($data['name'])) {
        $errors[] = 'Ошибка! Вы не заполнили Имя';
    } else if (empty($data['password'])) {
        $errors[] = 'Ошибка! Вы не заполнили пароль';
    }
    if (empty($errors)) {
        $db = new PDO('sqlite:testDB.db');
        $name = $data["name"];
        $password = password_hash($data["password"], PASSWORD_DEFAULT);
        $sql = "SELECT Password FROM Users WHERE Name = '$name'";
        $result = $db->query($sql);
        $sql_data = $result->fetch();
        $password = $sql_data["Password"];
        if (password_verify($data["password"], $password)) {
            setcookie("LoginCookie", $data["name"]);
            echo '<div class="check-label background-color-green">Вход произошёл успешно</div>';

        }
        else
            echo '<div class="check-label background-color-red">Не верный логин или пароль</div>';
    } else {
        echo '<div class="check-label background-color-red">' . array_shift($errors) . '</div>';
    }
}

$main_template = file_get_contents("templates/main.html");
$login_template = file_get_contents("templates/register/login.html");
if (isset($_COOKIE['LoginCookie'])) {
    $user_name_template = file_get_contents("templates/user_name.html");
    $user_name_template = str_replace('{Name}', $_COOKIE['LoginCookie'], $user_name_template);
    $main_template = str_replace('{User}', $user_name_template, $main_template);
}
else
    $main_template = str_replace('{User}', file_get_contents("templates/register.html"), $main_template);
$main_template = str_replace('{Content}', $login_template, $main_template);
echo $main_template;