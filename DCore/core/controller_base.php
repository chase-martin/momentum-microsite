<?php

/**
 * abstract class all controllers are extended from including rest controllers
 * this is a super simple controler base class. All controllers must have a 
 * index() function.  
 * 
 * functions that end with Action are the only functions can be called as an action
 * from the router.
 * 
 * <code>
 * 
 * class myController extends baseClass {
 * 
 * function index()
 * { 
 * //the function called if no action is defined
 * }
 * 
 * function viewAction()
 * {
 * // the action called when "view" action is called 
 * //  such as http://mydoomain.com/my/view
 * }
 * }
 * 
 * </code>
 * 
 * Controlers are usually registered in the plugin setup init.php. but can be registered
 * anywhere before $router->loader() is called
 * 
 * <code> 
 * 
 * $registry->router->setController('my1','my2:my3')
 * 
 * </code> 
 * 
 * this would set class my3Controller class in plugin "my2' as controller for url my1 
 * such as http://mydoomain.com/my1/view
 * 
 * @package DCore/core
 */
Abstract Class baseController extends baseClass {

    /**
     * @all controllers must contain an index method
     */
    abstract function index();
}

