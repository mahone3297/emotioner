<?php
$action = $_GET['action'];

switch ($action){
    case 'get_normal':
        echo 'http get test success';
        break;
    case 'get_timeout':
        sleep(3);
        break;
    case 'post_normal':
        if ($_POST['key1']=='val1' && $_POST['key2']=='val2'){
            echo 'http post test success';
        }
        break;
}