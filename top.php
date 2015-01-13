<?php
include_once('eng/site.conf.php');

if ($_SERVER['SERVER_NAME'] == 'atmos.dev') {
    $DB = new JF_Database('127.0.0.1', 'atmos.dev', 'atmos.dev');
    $DB->select_db('atmos.dev');
} else {
    $DB = new JF_Database('idb2.majordomo.ru', 'u134474', 'CP4awWNd6G');
    $DB->select_db('b134474_homerule');
}

$SITE = new Site($_POST, $DB);