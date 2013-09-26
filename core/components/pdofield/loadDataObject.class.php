<?php namespace pdoField;

class loadDataObject{
	/**
* @var cached reference to singleton instance 
	*/
	protected static $instance;
		
	/**
	* gets the instance via lazy initialization (created on first usage)
	*
	* @return self
	*/
	public static function getInstance($modx)
	{	
		if (null === self::$instance) {
			self::$instance = new self($modx);
		}
		return self::$instance;
	}

	/**
	* is not allowed to call from outside: private!
	*
	*/
	private function __construct($modx){
		$this->_modx = $modx;
	}

	/**
	* prevent the instance from being cloned
	*
	* @return void
	*/
	private function __clone(){}

	/**
	* prevent from being unserialized
	*
	* @return void
	*/
	private function __wakeup(){}
		
	protected $_modx = null;
	private $_cache = array();
	private $_fields = array();
	protected $mode = 0;
		
	const QUERY_MODE_NOLOG = 2;
	const QUERY_MODE_PDO = 1;
	const QUERY_MODE_XPDO = 0;
		
	public function clearCache(){
		$this->_cache = array();
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
		
	public function getData($resID, $name='pagetitle', $def='', $object='modResource'){
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
}