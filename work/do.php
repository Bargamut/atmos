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

    if (count($err) > 0) {
        echo '{ "status": "error", "data": {' . implode(',', $err) . '}}';
    } else {
        echo '{ "status": "ok" }';
    }
}

if ($_POST['type'] == 'del') {
    if (!$SITE->DelController($_POST['cid'])) {
        array_push($err, '"' . $_POST['cid'] . '"');
    }

    if (count($err) > 0) {
        echo '{ "status": "error", "cid": "' . implode(',', $err) . '"}';
    } else {
        echo '{ "status": "ok" }';
    }
}

if ($_POST['type'] == 'getdef') {
    $res = $SITE->getOption($_POST['cid']);

    if (count($res) == 0) {
        array_push($err, '"' . $_POST['cid'] . '"');
    }

    if (count($err) > 0) {
        echo '{ "status": "error", "cid": ' . implode(',', $err) . '}';
    } else {
        echo '{ "status": "ok", "data": ' . json_encode($res) . ' }';
    }
}

if ($_POST['type'] == 'comset') {
    $params = array(
        'cid'           => $_POST['cid'],
        'interval'      => $_POST['interval'],
        'lightness'     => $_POST['lightness'],
        'temperature'   => $_POST['temperature'],
        'humidy'        => $_POST['humidy'],
        'time_watering' => $_POST['time_watering'],
        'time_waiting'  => $_POST['time_waiting']
    );

    $SITE->setOption($params);
}

if ($_POST['type'] == 'setdat') {
    $params = json_decode($_POST['data'], true);

    $res = $SITE->setDatas($params);

    if (count($res) == 0) {
        array_push($err, '"' . $_POST['cid'] . '"');
    }

    if (count($err) > 0) {
        echo '{ "status": "error", "cid": ' . implode(',', $err) . '}';
    } else {
        echo '{ "status": "ok", "data": ' . json_encode($res) . ' }';
    }
}

if ($_POST['type'] == 'getdat') {
    $res = $SITE->getDatas($_POST['cid'], $_POST['min_date'], $_POST['max_date']);

    if (count($res) == 0) {
        array_push($err, '"' . $_POST['cid'] . '"');
    }

    if (count($err) > 0) {
        echo '{ "status": "error", "cid": ' . implode(',', $err) . '}';
    } else {
        echo '{ "status": "ok", "data": ' . json_encode($res) . ' }';
    }
}