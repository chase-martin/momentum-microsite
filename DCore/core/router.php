<?php

/**
 *  router dispatches http request
 * 
  1.  determine which controller to use
  2.  determine action to be called on controller
  3.  add url params to template   name/value/name/value/name/value


  router will first search the registered controllers and then look in the default
  controller path  for a file "controlname.class.php"



  REGISTER CONTROLLER
  -------------------
  calling setController will set or overide if exists the the controller class.

  $this->registry->router->setController('controllerName','pluginname:controllerFilename');

  if ROOT/{controllerName} is requested the router would look in
  protected/plugins/{pluginname}/controller/{controllerFilename}Controller.php

  a controller has actions that are called by ROOT/{controllerName}/{action}
 * 
 * actions are functions in a controller ending with Action
 * 
 * example  ROOT/index/edit  would fire the editAction function
 * <code>
 * class indexController extends baseController
 * public function editAction()
 * {
 *  
 * }
 * </code> 
 *  
 * 
  Plugins may override controllers with this scheama. So the order of plugin initalization can effect this.

 * @package DCore/core
 */
class router extends baseClass {
    /*
     * @the controller path
     */

    private $path;
    private $args = array();
    private $controllers = array();
    public $file;
    public $controller;
    public $action;
    public $rewriteOn = true;
    private $routes = array();
    /**
     * Only required if you do not register controllers and place the controller in the default path
     * @set controller directory path
     *
     * @param string $path
     *
     * @return void
     *
     */
    function setPath($path) {
        if ($path == null)
            $path = __PROTECTED_PATH . '/controller/';
        /*         * * check if path i sa directory ** */
        if (is_dir($path) == false) {
            throw new Exception('Invalid controller path: `' . $path . '`');
        }
        /*         * * set the path ** */
        $this->path = $path;
    }

    /**
     * sets the controllers url path
     * 
     * $controllerName is the added url path to the controller
     * 
     * rooturl + $controllerName  
     * 
     * @param type $controllerName
     * @param type $controllerLocal 
     */
    function setController($controllerName, $controllerLocal = null) {
		if ( $controllerLocal == null)
			 $controllerLocal = $controllerName.':'.$controllerName;

        $this->controllers[$controllerName] = $controllerLocal;
    }
/**
 * Dummy function when not making a REST request
 * 
 * @param type $class
 * @param type $basePath 
 */
    public function addService($class, $basePath = '') {
        
    }

    /**
     * loader is called to:
     * -  Find the controller
     * -  Find the action
     * -  Call the action
     * 
     * @load the controller
     *
     * @access public
     *
     * @return void
     *
     */
    public function loader() {
        /*         * * check the route ** */
        $this->getController();

        /*         * * if the file is not there  ** */
        if (is_readable($this->file) == false) {
            $this->file = $this->path . '/error404.php';
            $this->controller = 'error404';
        }

        /*         * * include the controller ** */
        include $this->file;

        /*         * * a new controller class instance ** */
        $class = $this->controller . 'Controller';
        $controller = new $class($this->registry);

        /*         * * check if the action is callable ** */
        if (is_callable(array($controller, $this->action . 'Action'))) {
            $action = $this->action . 'Action';
        } else {
            $action = 'index';
        }
        /*         * * run the action ** */

        $result = $controller->$action();
        $resultType = gettype($result);

        // $result can only have three result types
        // if returns an array then it should return a ajax (maybe json)
        // if returns is empty then do nothing and assume that a templete call wil be fired
        // if neither of these then it should be a xhp object which is hard to check for. And we echo it
        if ($resultType == 'array') {
            // todo::  do ajax
        } else if (!empty($result)) {
            echo $result;
        }

        return $result;
    }

    /**
     * does a redirect using  {@link router::buildUrl()}
     * 
     * if $controller_url = '/' then redirects to controller "index"
     * 
     * 
     * @param type $controller_url
     * @param type $action
     * @param type $params 
     */
    public function redirect($controller_url, $action = null, $params = array()) {
        if ($action)
            $controller_url = $this->buildUrl($controller_url, $action, $params);
        if ($controller_url == '/')
            $controller_url = $this->buildUrl('index');

        header("Location: " . $controller_url);
        die;
    }

    /**
     * bulds a full url directed to the $controller, $action , $params
     * 
     * rewrite aware
     * 
     * @create a url to call a controller
     *
     *
     *
     * @return string
     *
     */
    public function buildUrl($controller, $action = null, $params = array()) {
        if ($action)
            $url = $controller . '/' . $action;
        else if (count($params))
            $url = $controller . '/index';
        else
            $url = $controller;

        $p = '';


        if ($this->rewriteOn) {
            foreach ($params as $key => $param) {
                $p .= '/' . $key . '/' . $param;
            }
            $result = URL_ROOT . $url . $p;
        } else {
            foreach ($params as $key => $param) {
                $p .= '&' . $key . '=' . $param;
            }
            $result = URL_ROOT . '?rt=' . $url . $p;
        }
        return $result;
    }
   public function addRoute($path,$newpath){
       $this->routes[$path] = array('path'=>$newpath);
   }

    private function getRoute($path) {
        foreach ($this->routes as $key => $route) {
            if (substr($key, 0, 1) === "/") {
                $matches = array();
                $path    = trim($path,'/');
                if (preg_match($key, $path, $matches)) {
                    return preg_replace($key, $route['path'], $path);
                }
            }
            else {
                if ($key == $path) {
                    return $route['path'];
                }
            }
        }

    }
    /**
     *
     *      
     * sets the controller that is pointed to in $_GET['rt']
     * 
     * it does not actually return the controll but sets $this->controller , $this->action
     * 
     * if no action the index is action called
     * 
     * if no controller the index controller
     * 
     * @set the controller
     *
     * @access private
     *
     * @return void
     *
     */
    private function getController() {

        /*         * * get the route from the url ** */
        $route = (empty($_GET['rt'])) ? '' : $_GET['rt'];
        $route = $this->getRoute($route);
        if (empty($route)) {
            $route = 'index';
        } else {
            /*             * * get the parts of the route ** */
            $parts = explode('/', $route);
            $this->controller = $parts[0];
            if (isset($parts[1])) {
                $this->action = $parts[1];
            }
        }
        $index = 2;
        if (!$this->registry->request)
            $this->registry->request = new request();
        $this->registry->request->addPOST($_POST);
        // check if template is even loaded
        // a pure xhp app may not need to load it
        if ($this->registry->template) {
            while (count($parts) > $index + 1) {
                $cc = ('get_' . $parts[$index]);
                $this->registry->request->setGET($parts[$index],$parts[$index + 1]);
                $this->registry->template->$cc = $parts[$index + 1];
                $index += 2;
            }
        }
        
        if (empty($this->controller)) {
            $this->controller = 'index';
        }



        /*         * * Get action ** */
        if (empty($this->action)) {
            $this->action = 'index';
        }


        /*         * * set the file path ** */
        $setControllerPath = $this->controllers[$this->controller];


        if (!empty($setControllerPath)) {
            $this->file = DCore::getFilePAth($setControllerPath . 'Controller', 'controller');

            //     __PROTECTED_PATH . 'plugins/' . $Controller_info[0] . '/controller/' . $Controller_info[1] . 'Controller.php';
        } else {
            $this->setPath($this->path);
            $this->file = $this->path . '/' . $this->controller . 'Controller.php';
        }
    }

}

