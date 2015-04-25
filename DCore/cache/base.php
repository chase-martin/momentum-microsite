<?php

/**
 * cache class file
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005-2011 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @package DCore.cache
 */

/**
 * cache class
 *
 *  used from Prado. xeixei ni Qiang Xue.
 * 
 *
 *
 * The following basic cache operations are implemented:
 * - {@link get} : retrieve the value with a key (if any) from cache
 * - {@link set} : store the value with a key into cache
 * - {@link add} : store the value only if cache does not have this key
 * - {@link delete} : delete the value with the specified key from cache
 * - {@link flush} : delete all values from cache
 *
 * Each value is associated with an expiration time. The {@link get} operation
 * ensures that any expired value will not be returned. The expiration time by
 * the number of seconds. A expiration time 0 represents never expire.
 *
 * By definition, cache does not ensure the existence of a value
 * even if it never expires. Cache is not meant to be an persistent storage.
 *
 *
 * Some usage examples of cache are as follows,
 * <code>
 * $cache=new cache;  // cache may also be loaded as a application module
 * $cache->init();
 * $cache->add('object',$object);
 * $object2=$cache->get('object');
 * </code>
 *
 *
 * usually loadded in the application config
 * <code>
 * $CONFIG['modules'] = array(
 *                         "cache" => array('class'=>"TAPCCache"),
 *                         ...
 *                          );
 * </code>
 * later can be called like this
 * <code>
 * $registry->cache->add('apple',data);
 * </code>
 * 
 * there is not reason you can not run multipe caches
 * <code>
 * $CONFIG['modules'] = array(
 *                         "cache" => array('class'=>"TAPCCache"),
 *                         "redis" => array('class'=>"redisCache"),
 *                         ...
 *                          );
 * </code>
 * later can be called like this.
 * <code>
 * $registry->cache->add('apple',data);
 * $registry->redis->add('apple',data);
 * 
 * </code>

 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id: TSqliteCache.php 2996 2011-06-20 15:24:57Z ctrlaltca@gmail.com $
 * @package DCore.cache
 * @since 3.0
 */
namespace cache;

abstract class base extends \baseClass {

    
        function __construct($registry, $options = null) {
        parent::__construct($registry, $options);
        if (!empty($options['DCoreCache']))
           \DCORE::$cache = $this;
    }

    
    function loadCache($cachelist) {
        if (!is_array($cachelist))
            $cachelist = arrray($cachelist);

        foreach ($cachelist as $key => $item) {
            if (is_numeric($key)) {
                $key = $item;
                $item = array();
            }
            if ($key::isAvalible()) {
                $cache = new $key();
                $cache->init($item);
            }
        }
    }

    private $_salt = '45_{JKjnsdfgvl8osnvsef';

    protected function generateUniqueKey($key) {
        return md5($this->_salt . $key);
    }

    /**
     * Retrieves a value from cache with a specified key.
     * @param string a key identifying the cached value
     * @return mixed the value stored in cache, false if the value is not in the cache or expired.
     */
    public function get($id) {
        if (($value = $this->getValue($this->generateUniqueKey($id))) !== false) {
            $data = unserialize($value);
            if (!is_array($data))
                return false;
            if (!($data[1] instanceof ICacheDependency) || !$data[1]->getHasChanged())
                return $data[0];
        }
        return false;
    }

    /**
     * Stores a value identified by a key into cache.
     * If the cache already contains such a key, the existing value and
     * expiration time will be replaced with the new ones. If the value is
     * empty, the cache key will be deleted.
     *
     * @param string the key identifying the value to be cached
     * @param mixed the value to be cached
     * @param integer the number of seconds in which the cached value will expire. 0 means never expire.
     * @param ICacheDependency dependency of the cached item. If the dependency changes, the item is labeled invalid.
     * @return boolean true if the value is successfully stored into cache, false otherwise
     */
    public function set($id, $value, $expire = 0, $dependency = null) {
        if (empty($value) && $expire === 0)
            $this->delete($id);
        else {
            $data = array($value, $dependency);
            return $this->setValue($this->generateUniqueKey($id), serialize($data), $expire);
        }
    }

    /**
     * Stores a value identified by a key into cache if the cache does not contain this key.
     * Nothing will be done if the cache already contains the key or if value is empty.
     * @param string the key identifying the value to be cached
     * @param mixed the value to be cached
     * @param integer the number of seconds in which the cached value will expire. 0 means never expire.
     * @param ICacheDependency dependency of the cached item. If the dependency changes, the item is labeled invalid.
     * @return boolean true if the value is successfully stored into cache, false otherwise
     */
    public function add($id, $value, $expire = 0, $dependency = null) {
        if (empty($value) && $expire === 0)
            return false;
        $data = array($value, $dependency);
        return $this->addValue($this->generateUniqueKey($id), serialize($data), $expire);
    }

    /**
     * this function returns **true** or **false** if the key $id is in cache.
     * $value is return by reference with the stored value if present.
     * 
     * @param string $id
     * @param mixed $value
     * @return boolean 
     */
    public function getRef($id, &$value) {
        //gets cache arrayx from cache
        if (($value = $this->getValue($this->generateUniqueKey($id))) !== false) {
            //unserialize the object .
            // all data is stored in a cache array
            // if $value is not an array  then it was never stored or expired
            $data = unserialize($value);
            if (!is_array($data))
                return false;

            if (!($data[1] instanceof ICacheDependency) || !$data[1]->getHasChanged()) {
                $value = $data[0];
                return true;
            }
        }
        return false;
    }

    /**
     * Deletes a value with the specified key from cache
     * @param string the key of the value to be deleted
     * @return boolean if no error happens during deletion
     */
    public function delete($id) {
        return $this->deleteValue($this->generateUniqueKey($id));
    }

    abstract protected function getValue($key);

    /**
     * Stores a value identified by a key in cache.
     * This is the implementation of the method declared in the parent class.
     *
     * @param string the key identifying the value to be cached
     * @param string the value to be cached
     * @param integer the number of seconds in which the cached value will expire. 0 means never expire.
     * @return boolean true if the value is successfully stored into cache, false otherwise
     */
    abstract protected function setValue($key, $value, $expire);

    /**
     * Stores a value identified by a key into cache if the cache does not contain this key.
     * This is the implementation of the method declared in the parent class.
     *
     * @param string the key identifying the value to be cached
     * @param string the value to be cached
     * @param integer the number of seconds in which the cached value will expire. 0 means never expire.
     * @return boolean true if the value is successfully stored into cache, false otherwise
     */
    abstract protected function addValue($key, $value, $expire);

    /**
     * Deletes a value with the specified key from cache
     * This is the implementation of the method declared in the parent class.
     * @param string the key of the value to be deleted
     * @return boolean if no error happens during deletion
     */
    abstract protected function deleteValue($key);

    /**
     * Deletes all values from cache.
     * Be careful of performing this operation if the cache is shared by multiple applications.
     */
    abstract protected function flush();
}

