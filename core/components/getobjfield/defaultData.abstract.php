<?php namespace getObjField;
include_once(dirname(__FILE__).'/xNop.class.php');

abstract class defaultData{
	/* @var modX $modx */
	public $modx;
	public $cacheObj = null;
	protected $_config = array();
	
	protected $varname = null;
	
	protected static $ConfigDefault = array(
		'queryMode' => 2,
		'default' => '',
		'output' => '',
		'prepare'=>''
	);
	
	private $mode = 0;
	
	const QUERY_MODE_NOLOG = 2;
	const QUERY_MODE_PDO = 1;
	const QUERY_MODE_XPDO = 0;
	
	protected $defaultField = null;
	/**
	 * @param modX $modx
	 * @param array $config
	 */
	public function __construct(\modX &$modx, array $config = array()) {
		$this->_modx =& $modx;
		$this->_config = $config;
	}
	
	public function getMode(){
		return $this->mode;
	}
	
	public function setMode($mode){
		switch($mode){
			case self::QUERY_MODE_NOLOG:
			case self::QUERY_MODE_PDO:{
				$this->mode = $mode;
				break;
			}
			case self::QUERY_MODE_XPDO:
			default:{
				$this->mode = self::QUERY_MODE_XPDO;
			}
		}
		return $this;
	}
	
	public function _getData($resID, $name='pagetitle', $def='', $object='modResource'){
		if(!isset($this->_cache[$object][$resID]) && ((int)$resID > 0)){
			$this->_cache[$object][$resID] = $this->_query($object, $resID);
		}
		return (isset($this->_cache[$object][$resID][$name])) ? $this->_cache[$object][$resID][$name] : $def;
	}
	public function getColumns($object = 'modResource', $keys = false){
		$data = isset($this->_fields[$object]) ? $this->_fields[$object] : $this->_modx->getFields($object);
		return $keys ? array_keys($data) : $data;
	}
	
	protected function _query($object, $pkID){
		$where = array($this->_modx->getPK($object) => $pkID);
		switch($this->mode){
			case self::QUERY_MODE_PDO:{
				$q = $this->_modx->newQuery($object);
				$q->select("`".implode("`,`",$this->getColumns($object, true))."`")->where($where)->prepare();
				//$q->select($this->_modx->getSelectColumns($object))->where($where)->prepare();
				$q = $this->_modx->query($q->toSQL());
				$out = $q ? $q->fetch(\PDO::FETCH_ASSOC) : array();
				break;
			}
			case self::QUERY_MODE_NOLOG:{
				$q = $this->_modx->newQuery($object);
				$q->select(implode(",", $this->getColumns($object, true)))->where($where)->prepare();
				//$q->select($this->_modx->getSelectColumns($object))->where($where)->prepare();
				$q->stmt->execute();
				$out = $q->stmt->fetch(\PDO::FETCH_ASSOC);
				break;
			}
			case self::QUERY_MODE_XPDO:
			default:{ 
				$tmp = $this->_modx->getObject($object, $where);
				$out = ($tmp instanceof $object) ? $tmp->toArray() : array();
				break;
			}
		}
		return $out;
	}
	
	public function checkConfig($config){
		$this->_config = array();
		$this->_config['id'] = $this->getOption('id', $config, $this->getDefaultId());
		$this->_config['field'] = $this->getOption('field', $config, $this->getDefaultField());
		$this->_config['default'] = $this->getOption('default', $config);
		$this->_config['output'] = $this->getOption('output', $config);
		$this->_config['queryMode'] = $this->getOption('queryMode', $config);
		$this->_config['object'] = $this->getOption('object', $config);
		$this->_config['prepare'] = $this->getOption('prepare', $config);
		$this->setMode($this->getOption('queryMode'));
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
		$default = $this->getOption('default');
		if($value=='' && !empty($default)){
			$value = $this->getData($this->getOption('id'), $default);
		}
		if($value==''){
			$value = $this->getOption('output');
		}
		
		$prepare = $this->getOption('prepare');
		if(!empty($prepare)){
			$value = $this->_modx->runSnippet($prepare, array('input'=>$value));
		}
		return $value;
	}
	
	public function getDefaultField(){
		return is_null($this->defaultField) ? $this->_modx->getPK($this->getOption('object')) : $this->defaultField;
	}
	
	public function getDataID($id){
		return $this->_config['id'] = $id;
	}
	protected function checkData($id, $field){
		$out = $this->_getData(
			$id, 
			$field, 
			'', 
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