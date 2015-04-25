<?php
global $CONFIG;


$CONFIG = array(
    'urls'        => array('URL_ROOT' => '/'),
    'plugins'     => array('momentum','CMS'),
    'miniCss'     => 0,
    'searchPaths' => array(),
    'modules'     => array(
        'cache'        => array('class' => 'cache\nullCache', 'options' => array( 'DCoreCache' => 1)),
        'session',
        'router',
        'restServer'   => array('class' => 'RestServer', 'options' => array('active' => preg_match('/^\/?api\//', $_GET['rt']),
                                                                            'root'   => 'api/')),
        'assetManager' => array('options' => array()),
        'template'     => array('options' => array('useXHP' => 0)),
        'plugins',

    ),
);
