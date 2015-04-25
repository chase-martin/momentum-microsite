<?php

/**
 * An sqlite version of the session class
 * 
 * a drop in replacement
 * 
 * It still uses the $_SESSION global to get the session id. Everything else is stored in a sqlite file
 * 
 * @package DCore/core  
 */
class DBsession extends baseClass {

    function __construct($registry) {

        parent::__construct($registry);
    }

    public function __get($index) {

        return $this->get($index);
    }

    public function __set($index, $value) {
        $this->set($index, $value);
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
    public function set($id, $value, $dependency = null) {

        $data = array($value, $dependency);
        return $this->setValue($this->generateUniqueKey($id), serialize($data));
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
    public function add($id, $value, $dependency = null) {
        if (empty($value) && $expire === 0)
            return false;
        $data = array($value, $dependency);
        return $this->addValue($this->generateUniqueKey($id), serialize($data), $expire);
    }

    /**
     * Deletes a value with the specified key from cache
     * @param string the key of the value to be deleted
     * @return boolean if no error happens during deletion
     */
    public function delete($id) {
        return $this->deleteValue($this->generateUniqueKey($id));
    }

    /**
     * name of the table storing cache data
     */

    const CACHE_TABLE = 'session';
    /**
     * extension of the db file name
     */
    const DB_FILE_EXT = '.db';

    /**
     * @var boolean if the module has been initialized
     */
    private $_initialized = false;
    private $_salt = '45_{JKjnsjhvgb8osnvsef';
    private $currentSesionID = null;

    /**
     * @var SQLiteDatabase the sqlite database instance
     */
    private $_db = null;

    /**
     * @var string the database file name
     */
    private $_file = null;
    public $session_time = 86400;

    protected function generateUniqueKey($key) {
        return md5($this->_salt . $key);
    }

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
    public function init() {
        if (!function_exists('sqlite_open'))
            throw new TConfigurationException('sqlitecache_extension_required');
        if ($this->_file === null)
            $this->_file = DCore::getPathOfAlias('runtime') . '/sqlite.cache';
        $error = '';
        if (($this->_db = new SQLiteDatabase($this->_file, 0666, $error)) === false)
            throw new TConfigurationException('sqlitecache_connection_failed', $error);

        $sql = 'DELETE   FROM ' . self::CACHE_TABLE
                . ' WHERE  ' . self::CACHE_TABLE . '.id in 
                (select ' . self::CACHE_TABLE . '_IDS.key from ' . self::CACHE_TABLE . '_IDS 
                where ' . self::CACHE_TABLE . '_IDS.expire<>0 AND ' . self::CACHE_TABLE . '_IDS.expire<' . (time() - $this->session_time) . ')';
        if (@$this->_db->query($sql) === false) {
            if ($this->_db->query('CREATE TABLE ' . self::CACHE_TABLE . ' (id CHAR(128) PRIMARY KEY,key CHAR(128) , value BLOB)') === false)
                throw new TConfigurationException('sqlitecache_table_creation_failed', sqlite_error_string(sqlite_last_error()));
            if ($this->_db->query('CREATE TABLE ' . self::CACHE_TABLE . '_IDS (key CHAR(128) PRIMARY KEY, expire INT)') === false)
                throw new TConfigurationException('sqlitecache_table_creation_failed', sqlite_error_string(sqlite_last_error()));
        }


        session_start();
        $this->currentSesionID = $_SESSION['databaseSessionID'];
        if (empty($this->currentSesionID)) {
            $this->currentSesionID = str_replace('.', '', uniqid($_SERVER['REMOTE_ADDR'], TRUE));
            $_SESSION['databaseSessionID'] = $this->currentSesionID;
        }

        $time = time();
        $sql = 'REPLACE INTO ' . self::CACHE_TABLE . '_IDS VALUES(\'' . $this->currentSesionID . '\',' . $time . ')';
        $this->_db->query($sql);
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
        $sql = 'SELECT value FROM ' . self::CACHE_TABLE . ' WHERE id=\'' . $this->currentSesionID . '\' and key=\'' . $key . '\'  LIMIT 1';
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
    protected function setValue($key, $value) {

        $sql = 'REPLACE INTO ' . self::CACHE_TABLE . ' VALUES(\'' . $this->currentSesionID . '\', \'' . $key . '\',\'' . sqlite_escape_string($value) . '\')';
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
    protected function addValue($key, $value) {
        $sql = 'INSERT INTO ' . self::CACHE_TABLE . ' VALUES(\'' . $this->currentSesionID . '\', \'' . $key . '\',\'' . sqlite_escape_string($value) . '\')';
        return @$this->_db->query($sql) !== false;
    }

    /**
     * Deletes a value with the specified key from cache
     * This is the implementation of the method declared in the parent class.
     * @param string the key of the value to be deleted
     * @return boolean if no error happens during deletion
     */
    protected function deleteValue($key) {
        $sql = 'DELETE FROM ' . self::CACHE_TABLE . ' WHERE \'' . $this->currentSesionID . '\' and key=\'' . $key . '\'';
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

