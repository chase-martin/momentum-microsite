<?php


DCore::addSearchPath("CMS:helpers");
$registry->router->addService('CMS:cmsController', '/cms');
$registry->router->setController('cms','CMS:cms');


