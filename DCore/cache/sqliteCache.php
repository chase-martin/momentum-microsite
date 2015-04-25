<?php

/**
 * an sqlite version od cache
 * 
 * 
 * file key in $options is required for module loading
 * 
 * <code>
 * $cache=new sqliteCache;  // cache may also be loaded as a application module
 * $cache->init(array('file'=>sqlitefilepath);
 * $cache->add('object',$object);
 * $object2=$cache->get('object');
 * </code>
 * 
 * <code>
 * $CONFIG['modules'] = array(
 *                         "cache" => array('class'=>"sqliteCache" , 
 *                                         'options'=>array('file'=>sqlitefilepath)
 *                                           ),
 *                         ...
 *                          );
 * </code>

 * @package DCore.cache
 *  
 */
class sqliteCache extends cache {
    /**
     * name of the table storing cache data
     */

    const CACHE_TABLE = 'cache';
    /**
     * extension of the db file name
     */
    const DB_FILE_EXT = '.db';

    /**
     * @var boolean if the module has been initialized
     */
    private $_initialized = false;
    private $_salt = '45_{JKjnsdfgvl8osnvsef';

    /**
     * @var SQLiteDatabase the sqlite database instance
     */
    private $_db = null;

    /**
     * @var string the database file name
     */
    private $_file = null;

    /**
     * Destructor.
     * Disconnect the db connection.
     */
    public function __destruct() {
        $this->_db = null;
    }

    /**
     * Initializes this module.
     * This method is required by the IModule interface. It checks if the DbFile
     * property is set, and creates a SQLiteDatabase instance for it.
     * The database or the cache table does not exist, they will be created.
     * Expired values are also deleted.
     * @param TXmlElement configuration for this module, can be null
     * @throws TConfigurationException if sqlite extension is not installed,
     *         DbFile is set invalid, or any error happens during creating database or cache table.
     */
    public function init($options) {
        $this->_file = $options['file'];
        if (!function_exists('sqlite_open'))
            throw new TConfigurationException('sqlitecache_extension_required');
        if ($this->_file === null)
            $this->_file = DCore::getPathOfAlias('runtime') . '/sqlite.cache';
        $error = '';
        if (($this->_db = new SQLiteDatabase($this->_file, 0666, $error)) === false)
            throw new TConfigurationException('sqlitecache_connection_failed', $error);
        if (@$this->_db->query('DELETE FROM ' . self::CACHE_TABLE . ' WHERE expire<>0 AND expire<' . time()) === false) {
            if ($this->_db->query('CREATE TABLE ' . self::CACHE_TABLE . ' (key CHAR(128) PRIMARY KEY, value BLOB, expire INT)') === false)
                throw new TConfigurationException('sqlitecache_table_creation_failed', sqlite_error_string(sqlite_last_error()));
        }
        $this->_initialized = true;
    }

    /**
     * @return string database file path (in namespace form)
     */
    public function getDbFile() {
        return $this->_file;
    }

    /**
     * @param string database file path (in namespace form)
     * @throws TInvalidOperationException if the module is already initialized
     * @throws TConfigurationException if the file is not in proper namespace format
     */
    public function setDbFile($value) {
        $this->_file = $value;
    }

    /**
     * Retrieves a value from cache with a specified key.
     * This is the implementation of the method declared in the parent class.
     * @param string a unique key identifying the cached value
     * @return string the value stored in cache, false if the value is not in the cache or expired.
     */
    protected function getValue($key) {
        $sql = 'SELECT value FROM ' . self::CACHE_TABLE . ' WHERE key=\'' . $key . '\' AND (expire=0 OR expire>' . time() . ') LIMIT 1';
        if (($ret = $this->_db->query($sql)) != false && ($row = $ret->fetch(SQLITE_ASSOC)) !== false)
            return $row['value'];
        else
            return false;
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
        $expire = ($expire <= 0) ? 0 : time() + $expire;
        $sql = 'REPLACE INTO ' . self::CACHE_TABLE . ' VALUES(\'' . $key . '\',\'' . sqlite_escape_string($value) . '\',' . $expire . ')';
        return $this->_db->query($sql) !== false;
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
        $expire = ($expire <= 0) ? 0 : time() + $expire;
        $sql = 'INSERT INTO ' . self::CACHE_TABLE . ' VALUES(\'' . $key . '\',\'' . sqlite_escape_string($value) . '\',' . $expire . ')';
        return @$this->_db->query($sql) !== false;
    }

    /**
     * Deletes a value with the specified key from cache
     * This is the implementation of the method declared in the parent class.
     * @param string the key of the value to be deleted
     * @return boolean if no error happens during deletion
     */
    protected function deleteValue($key) {
        $sql = 'DELETE FROM ' . self::CACHE_TABLE . ' WHERE key=\'' . $key . '\'';
        return $this->_db->query($sql) !== false;
    }

    /**
     * Deletes all values from cache.
     * Be careful of performing this operation if the cache is shared by multiple applications.
     */
    public function flush() {
        return $this->_db->query('DELETE FROM ' . self::CACHE_TABLE) !== false;
    }

}
