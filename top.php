<?php
include_once('eng/site.conf.php');

if ($_SERVER['SERVER_NAME'] == 'avtool.dev') {
    $DB = new JF_Database('127.0.0.1', 'avtool.dev', 'avtool.dev');
    $DB->select_db('avtool.dev');
} else {
    $DB = new JF_Database('idb2.majordomo.ru', 'u134474', 'CP4awWNd6G');
    $DB->select_db('b134474_homerule');
}

$SITE = new Site($_POST, $DB);