<?php
/**
 *
 */

    DCore::using('twig:Autoloader');
Twig_Autoloader::register();
class DTwig_Loader_Filesystem extends Twig_Loader_Filesystem {

    public function findTemplate($name) {
        if (is_file($name))
            return $this->cache[$name] = $name;
        $path = DCore::getFilePath($name, 'views', 'default', '.twig', false);
        if ($path)
            return $path;

        return parent::findTemplate($name);
    }
}

class DTwig extends baseClass {

    static $loader = null;
    private $twig = null;

    function render($file, $templateClass, $vars = array()) {
        if (!is_array($vars))
            $vars = array();
        $template = $this->twig->loadTemplate($file);
        $result   = $template->display($vars);

        return $result;
    }

    function init() {
        $loader = new DTwig_Loader_Filesystem(DCore::getPathOfAlias('app'));
        global $registry;

        $this->twig = new Twig_Environment($loader, array(
            'cache' => false // DCore::getPathOfAlias('runtime'),
        ));
        $this->twig->addGlobal('template', $registry->template);
        $function = new Twig_SimpleFunction('render', 'DTwig_render', array('is_safe' => array('html'),'needs_environment' => true, 'needs_context' => true));
        $this->twig->addFunction($function);
        $function = new Twig_SimpleFunction('addCSS', 'DTwig_addCSS', array('is_safe' => array('html'),'needs_environment' => true, 'needs_context' => true));
        $this->twig->addFunction($function);
        $function = new Twig_SimpleFunction('addJS', 'DTwig_addJS', array('is_safe' => array('html'),'needs_environment' => true, 'needs_context' => true));
        $this->twig->addFunction($function);
        $function = new Twig_SimpleFunction('renderAssets', 'DTwig_renderAssets', array('is_safe' => array('html'),'needs_environment' => true, 'needs_context' => true));
        $this->twig->addFunction($function);

    }
}

function DTwig_render($enviroment, $context = array(), $templateName = null, $vars = false) {

    global $registry;

    return $registry->template->render($templateName, $vars);

}


function DTwig_addCSS($enviroment, $context = array(), $filename, $media = '') {

    global $registry;

    return $registry->template->addCSS( $filename, $media);

}

function DTwig_addJS($enviroment, $context = array(), $filename) {

    global $registry;

    return $registry->template->addJS( $filename);

}

function DTwig_renderAssets($enviroment, $context = array()) {

    global $registry;
    $result = $registry->template->renderCSS();
    $result .=  $registry->template->renderJS();
    return $result;

}
