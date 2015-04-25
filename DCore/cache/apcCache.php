<?php

namespace cache;
/**
 * an APC version of cache
 * 
 * will throw an error if APC is not loaded
 * 
 * @package DCore.cache
 *  
 */
class apcCache extends base {

    /**
     * Initializes this module.
     * This method is required by the IModule interface.
     * @param TXmlElement configuration for this module, can be null
     * @throws TConfigurationException if apc extension is not installed or not started, check your php.ini
     */
    public function init() {

        if (!extension_loaded('apc'))
            throw new TConfigurationException('apccache_extension_required');

        if (ini_get('apc.enabled') == false)
            throw new TConfigurationException('apccache_extension_not_enabled');

        if (substr(php_sapi_name(), 0, 3) === 'cli' and ini_get('apc.enable_cli') == false)
            throw new TConfigurationException('apccache_extension_not_enabled_cli');
    }

    function __construct($registry, $options = null) {
        parent::__construct($registry, $options);
        $this->scope = $options['org_id'];
    }

    /**
     * Retrieves a value from cache with a specified key.
     * This is the implementation of the method declared in the parent class.
     * @param string a unique key identifying the cached value
     * @return string the value stored in cache, false if the value is not in the cache or expired.
     */
    protected function getValue($key) {
        return apc_fetch($key);
    }

    /**
     * Stores a value identified by a key in cache.
     * This is the implementation of the method declared in the parent class.
     *
     * @param string the key identifying the value to be cached
     * @param string the value to be cached
     * @param integer the number of seconds in which the cached value will expire. 0 means never expire.
     * @return boolean true if the value is successfully stored into cache, false otherwise
     */
    protected function setValue($key, $value, $expire) {
        return apc_store($key, $value, $expire);
    }

    /**
     * Stores a value identified by a key into cache if the cache does not contain this key.
     * This is the implementation of the method declared in the parent class.
     *
     * @param string the key identifying the value to be cached
     * @param string the value to be cached
     * @param integer the number of seconds in which the cached value will expire. 0 means never expire.
     * @return boolean true if the value is successfully stored into cache, false otherwise
     */
    protected function addValue($key, $value, $expire) {
        if (function_exists('apc_add')) {
            return apc_add($key, $value, $expire);
        } else {
            throw new TNotSupportedException('apccache_add_unsupported');
        }
    }

    /**
     * Deletes a value with the specified key from cache
     * This is the implementation of the method declared in the parent class.
     * @param string the key of the value to be deleted
     * @return boolean if no error happens during deletion
     */
    protected function deleteValue($key) {
        return apc_delete($key);
    }

    /**
     * Deletes all values from cache.
     * Be careful of performing this operation if the cache is shared by multiple applications.
     */
    public function flush() {
        return apc_clear_cache('user');
    }

}
