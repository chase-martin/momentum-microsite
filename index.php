<?php
/**
 * @var Registry $registry
 */
global $registry;
error_reporting(E_ALL);
ini_set("display_errors", 1);

error_reporting(E_ALL  & ~E_NOTICE & ~E_WARNING & ~E_STRICT);
define ('__ROOT_PATH',dirname(__FILE__).'/' );
define ('__PROTECTED_PATH', dirname(__FILE__) .'/protected/');
define ('__DCORE', dirname(__FILE__) .'/DCore/');

include __DCORE . 'init.php';

$registry->load();


$registry->router->loader();
echo $registry->template->render('main');

