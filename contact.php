<?php

$data = $_POST;

if (isset($_POST['submit'])) {
    $group_id = $_POST['groupId'];
    $name = $_COOKIE['LoginCookie'];
    $db = new PDO('sqlite:testDB.db');
    $check_result = $db->query("SELECT * FROM Records WHERE GroupId = $group_id AND UserName = '$name'")->fetchAll();
    if (count($check_result) == 0)
        $db->query("INSERT INTO Records (GroupId,UserName) VALUES ('$group_id', '$name')");
}


$main_template = file_get_contents("templates/main.html");
$schedule_template = file_get_contents("templates/contact/schedule.html");
$schedule_content = "";

$db = new PDO('sqlite:testDB.db');
$result = $db->query("SELECT SGroup, STime, Id FROM Schedules");
$name = $_COOKIE['LoginCookie'];
$selected_groups = $db->query("SELECT GroupId FROM Records WHERE UserName = '$name'")->fetchAll();
$array = $result->fetchAll();
$index = 0;
foreach ($array as $row) {
    $id = $row['Id'];
    $exists = false;
    foreach ($selected_groups as $group_id)
        if ($group_id['GroupId'] == $id)
            $exists = true;
    $schedule_item_template = file_get_contents("templates/contact/schedule_item.html");
    $schedule_item_template = str_replace('{Type_group}', $row['SGroup'], $schedule_item_template);
    $schedule_item_template = str_replace('{Time}', $row['STime'], $schedule_item_template);
    $schedule_item_template = str_replace('{GroupId}', $id, $schedule_item_template);
    if ($index % 2 == 0)
        $schedule_item_template = str_replace('{Color}', 'background-yellow', $schedule_item_template);
    else
        $schedule_item_template = str_replace('{Color}', 'background-light-gray', $schedule_item_template);

    if($exists == true && $name != "")
        $schedule_item_template = str_replace('{Selected}', 'selected-group', $schedule_item_template);
    else
        $schedule_item_template = str_replace('{Selected}', '', $schedule_item_template);

    $index++;
    $schedule_content = $schedule_content . $schedule_item_template;
}

$map_template = file_get_contents("templates/map.html");
$schedule_template = str_replace('{Schedule_content}', $schedule_content, $schedule_template);
$footer_section_template = file_get_contents("templates/contact/footer_section.html");
$footer_section_template = str_replace('{Form}', file_get_contents("templates/contact/form_contact.html"), $footer_section_template);
$footer_section_template = str_replace('{Contact_info}', file_get_contents("templates/contact_info.html"), $footer_section_template);
if (isset($_COOKIE['LoginCookie'])) {
    $user_name_template = file_get_contents("templates/user_name.html");
    $user_name_template = str_replace('{Name}', $_COOKIE['LoginCookie'], $user_name_template);
    $main_template = str_replace('{User}', $user_name_template, $main_template);
} else
    $main_template = str_replace('{User}', file_get_contents("templates/register.html"), $main_template);
$main_template = str_replace('{Content}', $schedule_template . $map_template . $footer_section_template, $main_template);
echo $main_template;