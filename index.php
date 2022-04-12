<?php

$main_template = file_get_contents("templates/main.html");
$contact_info = file_get_contents("templates/contact_info.html");
$info = file_get_contents("templates/index/info.html");
$advantages = file_get_contents("templates/index/advantages.html");
$main_page_template = file_get_contents("templates/index/main_page.html");
$main_page_template = str_replace('{Content}', $contact_info . $info, $main_page_template);


$tutors_template = file_get_contents("templates/index/tutors.html");
$tutor_items = "";
$names = array("АНАТОЛИЙ ГУЛЬЕВ", "ПОНИЗОВ СЕРГЕЙ", "БУЯШОВ АЛЕКСАНДР", "КОЗЛОВ АЛЕКСЕЙ", "АНИСИМОВ НИКОЛАЙ", "БУЛАТЕНКО ДМИТРИЙ");
$descriptions = array("Мастер ПОУ МТС, стаж работы автоинструктором более 7 лет.", "Мастер ПОУ МТС, стаж работы автоинструктором 8 лет.", "Преподаватель теории, стаж преподавания ПДД более 25 лет.", "Преподаватель теории, стаж преподавания ПДД более 16 лет.", "Мастер ПОУ МТС, стаж работы автоинструктором 12 лет.", "Преподаватель теории, стаж преподавания ПДД более 14 лет.");
for ($i = 0; $i < 6; $i++) {
    $tutor_template = file_get_contents("templates/index/tutor.html");
    $tutor_template = str_replace('{Index}', $i + 1, $tutor_template);
    $tutor_template = str_replace('{Name}', $names[$i], $tutor_template);
    $tutor_template = str_replace('{Description}', $descriptions[$i], $tutor_template);
    $tutor_items .= $tutor_template;
}
$tutors_template = str_replace('{Tutors}', $tutor_items, $tutors_template);
$map_template = file_get_contents("templates/map.html");
$main_template = str_replace('{Content}', $main_page_template . $advantages . $tutors_template . $map_template, $main_template);
if (isset($_COOKIE['LoginCookie'])) {
    $user_name_template = file_get_contents("templates/user_name.html");
    $user_name_template = str_replace('{Name}', $_COOKIE['LoginCookie'], $user_name_template);
    $main_template = str_replace('{User}', $user_name_template, $main_template);
}
else
    $main_template = str_replace('{User}', file_get_contents("templates/register.html"), $main_template);
echo $main_template;
