<?php

/* * *********

  Template is in charge of rendering views

  An nested view system is used. Meaning that a masterpage is rendered and
  is responisble for rendering all views with in masterpage and those view are
  responisble for the view with in them and so on.


  Calling render($viewname) will return the rendered string of that view and that views nested views.


 * *********************
  registering a view

  setView('viewname','pluginname:viewfile');
  would register
  protected/plugins/{pluginname}/views/default/{viewfile}.php
  as a view for "viewname"

  if pluginname is left off then use the path
  protected/views/{viewfile}.php


  note that a folder named "default" is in the path. This is the view type.
  the view type can be set by calling setViewType($viewType)
  view type "default" is default
  template would look in
  protected/plugins/{pluginname}/views/{viewtype}/{viewfile}.php
  if it does not exist then look in
  protected/plugins/{pluginname}/views/default/{viewfile}.php

  can also call setView('viewname','pluginname:viewfile','viewtype');

  example
  $this->registry->templates->setView('viewname','pluginname:viewfile','mobile');

  if the view type is set to "mobile" then it would render the this file as as
  instead of the file registered else where.



 * *********************
  rendering a view
  when calling render the class will first check if the view is registered if so renders that file
  if the view is not registered it will look for the view as
  protected/views/{viewname}.php


  Plugins may override controllers with this scheama. So the order of plugin initalization can effect this.


  standard practice would be the master template would be registered as "masterpage"
  and the content be registered as "contents"
  plus in master controller should be <?php $this->render('contents')


  templates has a helper function show($viewname) to set the "contents" view. usually
  the controller would call this to set its view


* @package DCore/core
 * ******** */

Class template extends baseClass {
    /*
     * @Variables array
     * @access private
     */

    private $view_type = 'default';
    private $vars = array();
    private $views = array();
    private $CSSFiles = array();
    private $metaTags = array();
    private $JSFiles = array();
    public $useXHP = true;

    /**
     * /**
     * @constructor
     * @access public
     * @return void
     */

    /**
     * @set undefined vars
     * @param string $index
     * @param mixed  $value
     * @return void
     */
    public function __get($index) {
        if (isset($this->vars[$index]))
            return $this->vars[$index];

        return null;
    }

    public function __set($index, $value) {
        $this->vars[$index] = $value;
    }

    function __construct($registry, $options = null) {

        $this->vars = array();
        parent::__construct($registry, $options);
        if (isset($options['useXHP']))
            $this->useXHP = $options['useXHP'];
        if (isset($options['view_type']))
            $this->view_type = $options['view_type'];

        if ($this->useXHP)
            DCORE::using('DCORE:php-lib/init');
    }

    function setViewType($viewtype) {
        $this->view_type = $viewtype;
    }

    function addView($view, $view_path, $view_type = 'default') {
        if (empty($this->views[$view_type][$view]))
            $this->setView($view, $view_path, $view_type);
        else
            $this->views[$view_type][$view][] = $view_path;
    }

    function setView($view, $view_path, $view_type = 'default') {
        $this->views[$view_type][$view] = array($view_path);
    }

    function getView($name, $view_type = null) {
        if (empty($viewtype))
            $view_type = $this->view_type;
        if (isset($this->views[$view_type][$name]))
            $view = $this->views[$view_type][$name];
        if (isset($this->views['default'][$name]))
            $view = $this->views['default'][$name];
        if (empty($view))
            $view = $name;

        if (is_array($view))
            return $view;

        return array($view);

        return array();
    }

    function render($name, $vars_data = null, $cached = false) {
        //   $view_root =  __PROTECTED_PATH ;
        // Load variables
        foreach ($this->vars as $key => $value) {
            $$key = $value;
        }

        $vars = $vars_data;
        //use the buffer to send out the masterpage
        $views   = $this->getView($name, $this->view_type);
        $content = '';

        if ($cached) {
            if (isset($this->registry->cache)) {
                $hash    = md5(seralize($this->vars));
                $content = $this->registry->cache->get('view_cache_' . $name . $hash);
            }
        }
        if (empty($content)) {
            foreach ($views as $view) {
                $path = false;
                // the twig code never tested and not yet used

                if (DCore::getPathOfAlias('twig')) {
                    $path = DCore::getFilePAth($view, 'views', $this->view_type, '.twig', false);
                }
                ob_start();
                // if twig file exists then use a twig template
                // this could be expanded to smarty
                if ($path) {
                    $twig = new DTwig();
                    $twig->init();
                    $twig->render($path, $this, $vars);
                    unset($twig);
                }
                else {
                    $path = DCore::getFilePAth($view, 'views', $this->view_type);
                    if (file_exists($path) == false) {
                        throw new Exception('Template not found in ' . $path);

                        return false;
                    }

                    include($path);
                }
                $content .= ob_get_contents();

                ob_end_clean();
            }

            if ($cached) {
                if (isset($this->registry->cache)) {
                    $hash       = md5(seralize($this->vars));
                    $cache_data = $this->registry->cache->set('view_cache_' . $hash, $content, $cached);
                }
            }
        }

        return $content;
    }

    function show($name) {
        $this->setView('contents', $name);
    }

    function getURLForAsset($filename, $ext) {

        /*      $themecheck = explode(':', $filename);
          if ($themecheck[0] == 'theme') {
          $url = URL_THEME . $themecheck[1] . $ext;
          } else { */
        $subpaths = explode('!', $filename);

        $script = DCore::getFilePath($subpaths[0], '', '', (isset($subpaths[1]) ? '' : $ext));

        $url = $this->registry->assetManager->publishFilePath($script);

        if (isset($subpaths[1]))
            $url = $url . $subpaths[1] . $ext;

        //}
        return $url;
    }

    function addJS($filename) {

        if (empty($this->JSFiles[$filename])) {
            if(!preg_match("/^http[s]?/",$filename))
               $url                      = $this->getURLForAsset($filename, '.js');
            else
                $url = $filename ;
            $this->JSFiles[$filename] = $url;
        }
    }

    function addCSS($filename, $media = '') {

        if (empty($this->CSSFiles[$filename])) {
            if(!preg_match("/^http[s]?/",$filename))
                $url                      = $this->getURLForAsset($filename, '.css');
            else
                $url = $filename ;
            $this->CSSFiles[$filename]['url']   = $url;
            $this->CSSFiles[$filename]['media'] = $media;
        }
    }

    function addMeta($id, $data) {

        if (is_array($id)) {
            $data = $id;
            $id   = $id['ref'];
            if (empty($id))
                $id = 'ID' . rand();
        }
        if (empty($this->metaTags[$id])) {

            $this->metaTags[$id] = $data;

        }
    }

    function renderCSS($build = 0) {
        global $CONFIG;
        $result = '';
        $dir    = trim(URL_ROOT . 'assets', '/');
        if (($this->registry->debugMode) || (!$CONFIG['miniCss'])) {

            foreach ($this->CSSFiles as $css) {
                $result .= "<link rel='stylesheet' type='text/css' href='" . DCore::ExpandUrl($css['url'], 'css.') . "' " .
                    (empty($css['media']) ? '' : "media='print' ") . " />";
            }
        }
        else {
            $minurl = array();
            foreach ($this->CSSFiles as $css) {
                if (empty($css['media']))
                    $css['media'] = 'screen';
                $css['url']            = str_replace($dir, '', $css['url']);
                $minurl[$css['media']] = $minurl[$css['media']] . substr($css['url'], 1, 1000) . ',';
            }

            foreach ($minurl as $key => $u) {
                $result .=
                    "<link rel='stylesheet' type='text/css' href='" . rtrim(URL_ROOT, '/') . "/min/b=" . $dir . "&amp;f=" . rtrim($u, ',') . "&version=" . $build . "'  media='" . $key . "'  /> ";
            }
        }

        return $result;
    }

    function renderJS($build = 0) {
        global $CONFIG;
        $result = '';
        if (($this->registry->debugMode) || (!$CONFIG['miniCss'])) {

            foreach ($this->JSFiles as $script) {

                $result .= '<script type="text/javascript"  src="' . DCore::ExpandUrl($script, 'js.') . '"></script>';
            }
        }
        else {
            $minurl = rtrim(URL_ROOT, '/') . '/min/b=' . $dir . '&amp;f=';
            foreach ($this->JSFiles as $script) {
                $script = str_replace($dir . '/', '', $script);
                $minurl .= substr($script, 1, 1000) . ',';
            }
            $result .= '<script type="text/javascript" src="' . rtrim($minurl, ',') . '&version=' . $build . '"></script>';
        }

        return $result;
    }

    function renderMeta($build = 0) {
        global $CONFIG;

        $result = '';
        foreach ($this->metaTags as $tag) {
            $result .= '<meta ';
            foreach ($tag as $attr => $value) {
                $result .= ' ' . $attr . '="' .$value.'" ';
            }
            $result .= " /> \n";
        }

        return $result;
    }
}
