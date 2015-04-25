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
class nullCache extends base {

    /**
     * Initializes this module.
     * This method is required by the IModule interface.
     * @param TXmlElement configuration for this module, can be null
     * @throws TConfigurationException if apc extension is not installed or not started, check your php.ini
     */
    public function init() {


    }

    function __construct($registry, $options = null) {
        parent::__construct($registry, $options);

    }

    /**
     * Retrieves a value from cache with a specified key.
     * This is the implementation of the method declared in the parent class.
     * @param string a unique key identifying the cached value
     * @return string the value stored in cache, false if the value is not in the cache or expired.
     */
    protected function getValue($key) {
        return null;
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
        return $value;
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
      return $value;
    }

    /**
     * Deletes a value with the specified key from cache
     * This is the implementation of the method declared in the parent class.
     * @param string the key of the value to be deleted
     * @return boolean if no error happens during deletion
     */
    protected function deleteValue($key) {

    }

    /**
     * Deletes all values from cache.
     * Be careful of performing this operation if the cache is shared by multiple applications.
     */
    public function flush() {

    }

}
