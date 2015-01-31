<?php
include('../top.php');

$err = array();

if ($_POST['type'] == 'add') {
    $params = json_decode($_POST['data'], true);

    foreach ($params as $k => $v) {
        if (!$SITE->AddController($k, $v['caption'], $v['description'])) {
            array_push($err, '"' . $k . '": { "caption": "' . $v['caption'] . '", "description": "' . $v['description'] . '" }');
        }
    }

    if (count($err)) {
        echo '{ "status": "error", "data": {' . implode(',', $err) . '}}';
    } else {
        echo '{ "status": "ok" }';
    }
}

if ($_POST['type'] == 'del') {
    if (!$SITE->DelController($_POST['cid'])) {
        array_push($err, '"' . $_POST['cid'] . '"');
    }

    if (count($err)) {
        echo '{ "status": "error", "cid": "' . implode(',', $err) . '"}';
    } else {
        echo '{ "status": "ok" }';
    }
}
