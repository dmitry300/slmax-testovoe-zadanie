<?php
require_once('./User.php');
require_once('./People.php');

$user = [
    'name' => 'Dmitry',
    'surname' => 'Har',
    'birthdate' => '18.10.2000',
    'sex' => 1,
    'city' => 'Minsk'
];
$person = new User($user);
print_r($person->find($user));

//print_r(User::getAge('18.10.2000'));
//print_r(User::sexToString(1));
//$user1->delete();
//$people = new People('>',2);
//foreach ($people->getUserList() as $us){
//    print_r($us);
//};
//echo '<pre>'; print_r($people->getUserList()); echo '</pre>';
//echo json_encode($people->getUserList());

