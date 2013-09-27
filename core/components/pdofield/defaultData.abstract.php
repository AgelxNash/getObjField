<?php namespace pdoField;
include_once(dirname(__FILE__).'/loadDataObject.class.php');
include_once(dirname(__FILE__).'/xNop.class.php');

abstract class defaultData{
	/* @var modX $modx */
	public $modx;
	public $cacheObj = null;
	protected $_config = array();
	
	protected $varname = null;
	
	protected static $ConfigDefault = array(
		'queryMode' => 2,
		'output' => '',
		'prepare'=>''
	);
	protected $defaultField = null;
	/**
	 * @param modX $modx
	 * @param array $config
	 */
	public function __construct(\modX &$modx, array $config = array()) {
		$this->_modx =& $modx;
		$this->_config = $config;
		$this->cacheObj = $this->loadDataObject();
	}
	
	public function checkConfig($config){
		$this->_config = array();
		$this->_config['id'] = $this->getOption('id', $config, $this->getDefaultId());
		$this->_config['field'] = $this->getOption('field', $config, $this->getDefaultField());
		$this->_config['output'] = $this->getOption('default', $config);
		$this->_config['queryMode'] = $this->getOption('queryMode', $config);
		$this->_config['object'] = $this->getOption('object', $config);
		$this->_config['prepare'] = $this->getOption('prepare', $config);
	}
	
	public function getOption($name, $config = null, $default = null){
		if(is_null($config)){
			$config = $this->_config;
		}
		if(isset($config[$name])){
			$out = $config[$name];
		} else {
			if(is_null($default)){
				$out = isset(self::$ConfigDefault[$name]) ? self::$ConfigDefault[$name] : null;
			}else{
				$out = $default;
			}
		}
		return $out;
	}
	
	public function objVarname(){
		return $this->varname;
	}
	
	public function loadDataObject($mode = null){
		$obj = \pdoField\loadDataObject::getInstance($this->_modx);
		if(!isset($mode)){
			$mode = $this->getOption('queryMode');
		}
		$obj->setMode($mode);
		return $obj;
	}
	
	public function getDefaultId(){
		$out = 0;
		$varname = $this->objVarname();
		if(!is_null($varname) && is_object($this->_modx->$varname)){
			$out = $this->_modx->$varname->get(
				$this->_modx->getPK(
					$this->getOption('object')
				)
			);
		}
		return $out;
	}
	
	public function prepareValue($value){
		$prepare = $this->getOption('prepare');
		if(!empty($prepare)){
			$value = $this->_modx->runSnippet($prepare, array('input'=>$value));
		}
		return $value;
	}
	
	public function getDefaultField(){
		return is_null($this->defaultField) ? $this->_modx->getPK($this->getOption('object')) : $this->defaultField;
	}
	
	protected function checkData($id, $field){
		$out = $this->cacheObj->getData(
			$id, 
			$field, 
			$this->getOption('output'), 
			$this->getOption('object')
		);
		return $out;
	}
	public function getData($id, $field){
		$varname = $this->objVarname(); 
		if(!is_null($varname) && is_object($this->_modx->$varname) && $id == $this->_modx->$varname->get('id')){
			$out = $this->_modx->$varname->get($field);
		} else {
			$out = $this->checkData($id, $field);
		}
		return $out;
	}
}