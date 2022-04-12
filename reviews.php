<?php
$data = $_POST;
$phone_regex = '/\+375\(\d{2}\)\d{3}-\d{2}-\d{2}/';
$email_regex = '/\w+@\w+\.\w+/';
if (isset($_POST['submit'])){
    $errors = array();
    if (empty($data['name'])) {
        $errors[] = 'Ошибка! Вы не заполнили Ваше Имя';
    } else if (!preg_match_all($phone_regex, $data['phone'])) {
        $errors[] = 'Ошибка! Номер телефона должен иметь формат "+375(XX)XXX-XX-XX"';
    } else if (!preg_match_all($email_regex, $data['email'])) {
        $errors[] = 'Ошибка! Email должен иметь формат "ivanov@.com"';
    } else if (empty($data['review'])) {
        $errors[] = 'Ошибка! Вы не заполнили поле отзыва';
    }
    if (empty($errors)) {
        echo '<div class="check-label background-color-green">Отлично! Вы успешно заполнили все данные!</div>';
    } else {
        echo '<div class="check-label background-color-red">'.array_shift($errors).'</div>';
    }
}

$main_template = file_get_contents("templates/main.html");
$bg_image_template = file_get_contents("templates/review/bg-image.html");
$form_template = file_get_contents("templates/review/form.html");
$bg_image_template = str_replace('{Form}', $form_template, $bg_image_template);
$all_reviews = "";

$reviews_template = file_get_contents("templates/review/reviews.html");
$db = new PDO('sqlite:testDB.db');
$result = $db->query("SELECT RName, Review, RDate FROM Reviews");
$array = $result->fetchAll();
foreach($array as $row){
    $review_template = file_get_contents("templates/review/review.html");
    $review_template = str_replace('{Name}', $row['RName'], $review_template);
    $review_template = str_replace('{Review}',$row['Review'], $review_template);
    $review_template = str_replace('{Date}',$row['RDate'], $review_template);
    $all_reviews.= $review_template;
}
$reviews_template = str_replace('{Reviews}',$all_reviews , $reviews_template);
if (isset($_COOKIE['LoginCookie'])) {
    $user_name_template = file_get_contents("templates/user_name.html");
    $user_name_template = str_replace('{Name}', $_COOKIE['LoginCookie'], $user_name_template);
    $main_template = str_replace('{User}', $user_name_template, $main_template);
}
else
    $main_template = str_replace('{User}', file_get_contents("templates/register.html"), $main_template);
$main_template = str_replace('{Content}',$bg_image_template.$reviews_template, $main_template);
echo $main_template;