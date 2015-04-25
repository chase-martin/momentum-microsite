<?php

class TDocList extends DCMSTypes{

}

class DCMSTypes {
    private $_data;
    function setData($data){
        $this->_data = $data;
    }

    function toString(){
        return $this->_data;
    }

}

class DCms extends baseClass {
    private $_filename = array('variants' => 'variants', 'mapping' => 'mapping', 'language' => 'language');
    private $variants;
    private $mapping;
    private $language;
    private $default;
    private $schema;
    public $editing = true;

    private $_layers;
    private $_contentScheme;

    private $_activeContent;

    function getId() {

        return $this->_id;
    }

    function editOverlay(){
        return $this->registry->template->render('CMS:edit');
    }
    function getContentScheme() {

        return $this->_contentScheme;
    }

    function setFilename($namings) {
        $this->_filename = array_merge($this->_filename, $namings);
    }

    function setMeta() {
        foreach ($this->_activeContent['meta'] as $meta)
            $this->registry->template->addMeta($meta['ref'] ? $meta['ref'] : $meta['name'], $meta);

    }

    private function mergeLayers($layers) {

        $result            = array();
        $result['content'] = array();
        $result['meta']    = array();
        $content           = array();
        $meta              = array();
        $layers = array_reverse($layers);
        foreach ($layers as $layer) {
            if (isset($layer['content']))
                $content = array_merge($content, $layer['content']);
            if (isset($layer['meta']))
                $meta = array_merge($meta, $layer['meta']);

            $result = array_merge($result, $layer);
        }
        $result['content'] = $content;
        $result['meta']    = $meta;

        return $result;
    }
    function loadSchema(&$data,$schema= null){
        $schema = isset($schema)?$schema:$this->schema;

        foreach($schema['fields'] as $fieldName => $field){
              $type = $field['type'];
            if ($type){
                $typeObj = new $type();
                $typeObj->setData($data[$fieldName]);
                $data[$fieldName] = $typeObj;
            }
        }


    }
    function updateField($field,$value){
            $field = str_replace('*.','default.',$field);
            $request =  array('$set'=>array($field => $value));
        $this->registry->mongo->getCollection('documents')->update(array('docId'=>$this->getId()),$request);

    }
    function setup($variant_id) {

        if (!$this->registry->template->cms)
            $this->registry->template->cms = $this;

        $this->_layers = array();
        $mapping       = $this->mapping[$variant_id];

        $variant_id = isset($this->variants[$mapping]) ?  $mapping :  $variant_id;
        $variant = $this->variants[$variant_id];
        $variant = isset($variant) ? $variant : null;
        if ($variant) {
            $variant['layerName'] = 'variants.'.$variant_id;
            $this->_layers[] = $variant;
            if (isset($variant['lang'])) {
                i18::setLanguage($variant['lang']);
            }
        }
        $lang = i18::getLanguage();

        $langObj = isset($this->language[$lang]) ? $this->language[$lang] : null;
        if ($langObj){
            $langObj['layerName'] = 'language.'.$lang;
            $this->_layers[] = $langObj;
        }
        $this->default['layerName'] = 'default';
        $this->_layers[] = $this->default;

        $this->_contentScheme = $this->_id . '!' . $variant_id . '^' . $lang;
        $result = $this->mergeLayers($this->_layers);
        $this->_activeContent = $result;

        $this->loadSchema($result,$this->schema);


        $this->registry->template->setView('content', $result['view']['content']);
        $this->registry->template->setView('main', $result['view']['main']);

        $this->setMeta();

        return $result;
    }

    function load($namespace) {


        $this->_id      = $namespace;

        if ($this->registry->mongo)
           $data = $this->registry->mongo->getCollection('documents')->findOne(array('docId'=>$this->_id));

        if (empty($data)){
             $data           = json_decode(file_get_contents(DCore::getFilePath($namespace, 'config', '', '.json')), true);
            $data['docId'] =    $namespace;
            if ($this->registry->mongo)
                $this->registry->mongo->getCollection('documents')->insert($data);

        }

        $this->variants = $data['variants'];
        $this->mapping  = $data['mappings'];
        $this->language = $data['language'];
        $this->default  = $data['default'];
        $this->schema  = $data['schema'];

    }

    function text($field, $default = null) {
        $default = isset($default) ? $default : $field;
        $field = substr(preg_replace("/[^a-zA-z]/",'_',$field),0,10);
        foreach($this->_layers as $layer){
            if (!empty($layer['content'][$field]))
            {
                $namespace = $layer['layerName'].'.content.'.$field;
                $text = $layer['content'][$field];
                break;
            }

        }
        $namespace = isset($namespace)?$namespace:'*.content.'.$field;

        if (!isset($text))
            $text = isset($default) ? $default : $field;
        if (is_object($text))
            $text = $text->toString();
        $result = i18::local($text);
        if ($this->editing) {
            return '<span class="editableCms" cmsDocId="'.$this->_id .'" cmsNameSpace="' . $namespace . '"  cmsField="' . $field . '">'.  $result.'</span>';
        }

        return $result;
    }

    function value($namespace) {
        return $this->_activeContent[$namespace];
    }
} 