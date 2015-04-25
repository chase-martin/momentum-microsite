<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of i18
 *
 * @author adrian
 */
if (!defined('_DEFAULT_LANGUAGE_'))
    define('_DEFAULT_LANGUAGE_', 'en');

class i18 {

    static $language_id = _DEFAULT_LANGUAGE_;

    static function setLanguage($lang) {
        if (!empty($lang))
            self::$language_id = $lang;
    }

    static function getLanguage() {
        return self::$language_id;
    }

    static function local($text) {
        $result = $text;
        if (is_array($text)) {
            $lang   = self::getLanguage();
            $result = $text[$lang];
            if (empty($result))
                $result = $text['default'];
        }
        return $result;
    }

    static function loadLanguageFile($filename = '') {
        if (empty($filename))
            $filename = self::getLanguage();
        $filename = 'protected:settings/lang/' . self::getLanguage() . '/' . $filename;
        if (DCore::getFilePath($filename, '', '', '.php', false))
            DCore::using($filename);
    }
}


