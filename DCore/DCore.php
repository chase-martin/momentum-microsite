<?php

if (!defined('__FRAMEWORK_PATH'))
    define('__FRAMEWORK_PATH', dirname(__FILE__));
if (!defined('DCORE'))
    define('DCORE', dirname(__FILE__));
if (!defined('__REROUTE_SUBDOMAIN'))
    define('__REROUTE_SUBDOMAIN', 0);
if (!defined('__SASS_PATH'))
    define('__SASS_PATH', dirname(__FILE__).'/sass');




/**
 * DCore is the base static class for the entire framework.     
 *           
 * The DCore framework revolvers around the static class DCore and       
 * a singlton instance of class global $registry  
 * @package DCore        
 */
class DCore {
   
    
    /**
     *
     * @var cache
     */
    static public $cache = null;
    static private $_fileCache = null;
    /**
     * Hard security check  
     *                                                      
     * Will check if the current users can access the passed GUID    
     * R = read  M = modify C = comment A add to                     
     * @param guid $guid the guid of the object u are accessing      
     * @param char $access the type of access required               
     * @return  boolean true if allowed                              
     *
     */
    static function gate($guid, $access = 'R') {
        return true;
    }

    /**
     * not implemented
     * 
     * @param type $array
     * @param type $field
     * @param type $access
     * @return type 
     */
    static function authFilter($array, $field, $access = 'R') {
        return $array;
    }

    /**
     * addSearchPath()
     * 
     * Add a path to the class autoload path
     * @param string $path  the path to include
     * 
     *
     */
    function addSearchPath($path) {
        global $CONFIG;
        $paths = explode(':', $path);
        if ((strlen($paths[0]) > 1 ) and (count($paths) > 1)) {
            $paths[0] = self::$_aliases[$paths[0]];

            $path = implode('/', $paths);
            ;
        }
        $CONFIG['searchPaths'][] = realpath($path);
    }

    /**
     * ExpandUrl()
     * creates a url  that is on a different sub domain
     * 
     * the sub domain can be change with **$alt** . instead of starting the url with  "www." start the url with  $alt
     * this is good for loading images , css etc on different domains.
     * 
     * @param string $url  the url to modify
     * @param string $alt  the sub-domain to use in new url
     * @return string new url
     *
     */
    function ExpandUrl($url, $alt = null) {

        if ((!empty($alt)) and (__REROUTE_SUBDOMAIN)) {
            $domain = $_SERVER['HTTP_HOST'];
            $domain = preg_replace('/^(www|dev)./', $alt, $domain);
            return 'http://' . $domain . $url;
            //  $domain = $alt . $domain;
        }
        return $url;
    }

    /**
     * getFilePath()
     * 
     * converts a dcore name space to a file path
     * 
     * This function should be used to get most file paths
     * 
     * namespace syntax  alias:path
     * 
     * if type is specified then the result will be  (alias + "/" + type "/"+ path) 
     * can be a file type or folder
     * 
     * if $type is "views" then (alias + "/" + type "/"+ view_type + "/" + path)
     * 
     * @param string $namespace
     * @param string $type   such as "views" or "controller" or "helpers" or "models"
     * @param string $view_type if $type if "views" then it will add the view view to the path 
     * @param string $ext if not specicifed with use CLASS_FILE_EXT usually ".php"
     * @param boolean $throwEx  if should through exeption defualt true
     * @return string  a local system file path
     *
     */
    static function getFilePath($namespace, $type = '', $view_type = 'default', $ext = self::CLASS_FILE_EXT, $throwEx = true) {
        if (DCORE::file_exists(realpath($namespace)))
            return realpath($namespace);
        $view_info = explode(':', $namespace);
        if (count($view_info) > 1) {
            //only use registered alias or plugins
            $plugpath = self::$_aliases[$view_info[0]];
            if (empty($plugpath)) {
                $plugpath = __PROTECTED_PATH . 'plugins/' . $view_info[0];

                if (!DCORE::file_exists(realpath($plugpath)))
                    $plugpath = __FRAMEWORK_PATH . 'core/' . $view_info[0] . '/';
            }

            if ($type == 'views') {
                $file = $plugpath . '/' . $type . '/' . $view_type . '/' . $view_info[1] . $ext;
                if (!DCORE::file_exists($file))
                    $file = $plugpath . '/' . $type . '/default/' . $view_info[1] . $ext;
                if (!DCORE::file_exists($file))
                    $file = $plugpath . '/' . $type . '/' . $view_info[1] . $ext;

                if (DCORE::file_exists($file))
                    return $file;
            }
            else {
                $file = $plugpath . '/' . $type . '/' . $view_info[1] . $ext;



                if (!DCORE::file_exists($file))
                    $file = __PROTECTED_PATH . $type . '/' . $view_info[1] . $ext;

                  if (DCORE::file_exists($file))
                    return $file;
            }
        }
        else {
            $file = __PROTECTED_PATH . $type . '/' . $namespace . $ext;

            if (DCORE::file_exists($file))
                return $file;
        }
        if (DCORE::file_exists($file) == false) {

            if ($throwEx)
                throw new Exception('Path not found in ' . $namespace);
            return false;
        }
    }

    /**
     * File extension for php class files.
     */

    const CLASS_FILE_EXT = '.php';

    /**
     * @var array list of path aliases
     */
    static $_aliases = array('DCORE' => DCORE);

    /**
     * @var array list of namespaces currently in use
     */
    static $_usings = array();

    /**
     * @var array list of class exists checks
     */
    static $classExists = array();

    /**
     * helper function to return the global registry
     * @global Registry $registry
     * @return Registry 
     */
    public static function getRegistry() {
        global $registry;
        return $registry;
    }

    /**
     * getPathOfAlias()
     * gets the path of a dcore alias
     * 
     * @param string $alias the alias name wanted
     * @return string patth of alias
     *
     */
    public static function getPathOfAlias($alias) {
        return isset(self::$_aliases[$alias]) ? self::$_aliases[$alias] : null;
    }

    /**
     * returns all aliases 
     * key is alias 
     * value is path
     * @return string[] 
     */
    protected static function getPathAliases() {
        return self::$_aliases;
    }

    /**
     * Uses a namespace.
     * A namespace ending with an asterisk '*' refers to a directory, otherwise it represents a PHP file.
     * If the namespace corresponds to a directory, the directory will be appended
     * to the include path. If the namespace corresponds to a file, it will be included (include_once).
     * @param string namespace to be used
     * @param boolean whether to check the existence of the class after the class file is included
     * @throws Exception if the namespace is invalid
     */
    public static function using($namespace, $type = '', $view_type = 'default', $checkClassExistence = true) {
        if (isset(self::$_usings[$namespace]) || class_exists($namespace, false))
            return;
        $pos = strrpos($namespace, '/');
        $pos = $pos ? $pos : strrpos($namespace, ':');
        if (($path = self::getFilePath($namespace, $type, $view_type)) !== null) {
            $className = substr($namespace, $pos + 1);
            if ($className === '*') {  // a directory
                self::$_usings[$namespace] = $path;
                set_include_path(get_include_path() . PATH_SEPARATOR . $path);
                self::addSearchPath($path);
            } else {  // a file
                self::$_usings[$namespace] = $path;
                if (!$checkClassExistence || !class_exists($className, false)) {
                    try {

                        include_once($path);
                    } catch (Exception $e) {

                        throw $e;
                    }
                }
            }
        }
        else
            throw new Exception('using_invalid' . $namespace . ' - ' . $path);
    }

    /**
     * setPathOfAlias()
     * @param string alias to the path
     * @param string the path corresponding to the alias
     * @throws TInvalidOperationException if the alias is already defined
     * @throws TInvalidDataValueException if the path is not a valid file path
     */
    public static function setPathOfAlias($alias, $path) {

        if (($rp = realpath($path)) !== false && is_dir($rp)) {
            if (strpos($alias, '.') === false)
                self::$_aliases[$alias] = $rp;
            else
                throw new Exception('aliasname_invalid' . $alias);
        }
        else
            throw new Exception('alias_invalid ' . $alias . ' ' . $path);
    }

    /**
     * Takes a namespace of a class and loads the php file of that class and returns the classname
     * 
     * <code>
     * 
     *   $class = DCore::loadClass('lib:cache/apcCache');
     * 
     *   $cache = new $class($registry,$options);
     * 
     * </code>
     * 
     * @param type $classNameSpace
     * @return type 
     */
    static function loadClass($classNameSpace) {

        // see if it can be auto loaded if so just return the class name
        $file = can_auto_load($classNameSpace);
        if (!$file) {
            $file = DCore::getFilePath($classNameSpace);
            if (DCORE::file_exists($file))
                require_once $file;


            $classNameSpace = explode(':', $classNameSpace);
            $classNameSpace = $classNameSpace[1];
        }
        else
            require_once $file;

        $x = strrpos($classNameSpace, "/");
        if ($x)
            $classNameSpace = substr($classNameSpace, $x + 1);
        $class = $classNameSpace;
        return $class;
    }

    static function file_exists($filename) {
        if (empty($filename ))
            return false;
        
    $filename = str_replace(array('//', '\\'), '/', $filename);
        if (isset(self::$cache)) {
            if (!isset(self::$_fileCache))
                self::$_fileCache = array();//self::$cache->get('DCore::file_exists');
            if (!isset(self::$_fileCache))
                self::$_fileCache = array();
            if (isset(self::$_fileCache [md5($filename)])) {
                return self::$_fileCache [md5($filename)];
            }
        }

       
        $result = file_exists($filename);
        if (isset(self::$_fileCache)) {
            self::$_fileCache [md5($filename)] = $result;
            self::$cache->set('DCore::file_exists', self::$_fileCache);
        }
        return $result;
    }
    /**
     * redirects a the browser to an new url 
     * 
     * the url is based  of the server root. $_SERVER['HTTP_HOST'] + rootpath + $path
     * 
     * @param string url to redirect to
     *
     */
    static function redirect($path) {
        $host = $_SERVER['HTTP_HOST'];
        $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        header('Location: http://' . $host . $uri . '/' . $path);
        die();
    }

    /**
     * experimental
     * @param type $msg
     * @return type 
     */
    public static function fatalError($msg) {
        echo '<h1>DCore Fatal Error</h1>';
        echo '<p>' . $msg . '</p>';
        if (!function_exists('debug_backtrace'))
            return;
        echo '<h2>Debug Backtrace</h2>';
        echo '<pre>';
        $index = -1;
        foreach (debug_backtrace() as $t) {
            $index++;
            if ($index == 0)  // hide the backtrace of this function
                continue;
            echo '#' . $index . ' ';
            if (isset($t['file']))
                echo basename($t['file']) . ':' . $t['line'];
            else
                echo '<PHP inner-code>';
            echo ' -- ';
            if (isset($t['class']))
                echo $t['class'] . $t['type'];
            echo $t['function'] . '(';
            if (isset($t['args']) && sizeof($t['args']) > 0) {
                $count = 0;
                foreach ($t['args'] as $item) {
                    if (is_string($item)) {
                        $str = htmlentities(str_replace("\r\n", "", $item), ENT_QUOTES);
                        if (strlen($item) > 70)
                            echo "'" . substr($str, 0, 70) . "...'";
                        else
                            echo "'" . $str . "'";
                    }
                    else if (is_int($item) || is_float($item))
                        echo $item;
                    else if (is_object($item))
                        echo get_class($item);
                    else if (is_array($item))
                        echo 'array(' . count($item) . ')';
                    else if (is_bool($item))
                        echo $item ? 'true' : 'false';
                    else if ($item === null)
                        echo 'NULL';
                    else if (is_resource($item))
                        echo get_resource_type($item);
                    $count++;
                    if (count($t['args']) > $count)
                        echo ', ';
                }
            }
            echo ")\n";
        }
        echo '</pre>';
        exit(1);
    }

}

