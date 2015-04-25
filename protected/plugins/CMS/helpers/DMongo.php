<?php
class DMongo {
    private $_db;
    private $_uri;
    private $_dbName;
    static function mongoAvailable(){
        return extension_loaded('mongo');
    }

    function init($uri,$dbName){
        $this->_uri = $uri;
        $this->_dbName = $dbName;
    }
    function getCollection($name){
        if (empty($this->_db)){
            $m = new MongoClient($this->_uri); // connect
            $this->_db = $m->selectDB($this->_dbName);
        }
        return $this->_db->$name;
    }
} 