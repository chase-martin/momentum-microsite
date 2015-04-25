<?php

function cleanse($arr) {
    $clean = array();
    foreach($arr as $key => $val) {
        $clean[$key] = stripslashes(strip_tags(trim($val)));
    }
    return $clean;
}

$clean = cleanse($_GET);

foreach ($clean as $key => $val) {
    $key = '_ds_'. $key;
    $registry->session->$key = $val;
}

$registry->router->setController('index','momentum:index');
$registry->router->addRoute("/^(ldn|sf|registration-complete)[\/]?/",'index/index/page/${1}');
