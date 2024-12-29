<?php
session_start();
require_once 'db_connection.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'trangchu';

// Content
switch($page) {
    case 'trangchu':
        include 'trangchu.html';
        break;
    case 'phongtro':
        if(isset($_GET['id'])) {
            include 'phongtro.php';
        } else {
            include 'phongtro.html';
        }
        break;
    case 'quenmatkhau':
        include 'quenmatkhau.php';
        break;
    default:
        include 'trangchu.html';
}
?>