<?php
include_once(dirname(dirname(__FILE__))."/defaultData.abstract.php");

class modResourceData extends \getObjField\defaultData{
	protected $varname = 'resource';
	protected $defaultField = 'pagetitle';
	
	protected static $localConfigDefault = array(
		'processTV' => false,
		'isTV' => false,
		'top' => 0,
		'topLevel' => 0,
		'parentdata'=> 'id'
	);
	
	public function __construct(\modX &$modx, array $config = array()) {
		parent::__construct($modx, $config);
		parent::$ConfigDefault = array_merge(parent::$ConfigDefault, self::$localConfigDefault);
	}
	
	public function checkConfig($config){
		parent::checkConfig($config);
		$this->_config['processTV'] = $this->getOption('processTV', $config);
		$this->_config['isTV'] = $this->getOption('isTV', $config);
		$this->_config['topLevel'] = $this->getOption('topLevel', $config);
		$this->_config['top'] = $this->getOption('top', $config);
		$this->_config['parentdata'] = $this->getOption('parentdata', $config);
	}
	
	public function getDataID($id){
		switch(true){
			case (($top = $this->getOption('topLevel'))>0):{
				$id = $this->UP_TopLavel($id, $top);
				break;
			}
			case (($top = $this->getOption('top'))>0):{
				$id = $this->UP_Top($id, $top);
				break;
			}
		}
		return $this->_config['id'] = $id;
	}
	
	public function getData($id, $field){
		//$mainField = $this->cacheObj->getColumns($this->getOption('object'));
		$id = $this->getDataID($id);
		$value = $isTV = $processTV = null;
		if($field == 'id') {
			$object = null;
			$value = $id;
		}else{
			$object = true;
			$isTV = $this->getOption('isTV');
			$processTV = $this->getOption('processTV');
			if ($isTV || $processTV) {
				$tv = $this->_modx->getObject('modTemplateVar',array('name'=>$field));
				if (!($tv instanceof modTemplateVar)){
					$value = null;
				}else{
					$value = ($processTV) ? $tv->renderOutput($id) : $tv->getValue($id);
				}
			}
		}
		
		if(is_null($value) && $object){
			switch($field){
				case 'parentdata':{
					$value = \getObjField\defaultData::getData($id, 'parent');
					$value = ($value>0) ? $this->getData($value, $this->getOption('parentdata')) : '';
					break;
				}
				default:{
					$value = parent::getData($id,$field);
				}
			}
		}
		return $value;
	}
	
	protected function UP_TopLavel($id, $top){
		$top += 1;
		$parents = array($id);
		for($i=1; $i <= $top; $i++){
			$parents[$i] = \getObjField\defaultData::getData($parents[$i-1], 'parent');
			if($parents[$i] == ''){
				unset($parents[$i]);
				$top = $i;
				break;
			}
		}
		$top--;
		if($parents[count($parents)-1] != 0){
			$parents[] = 0;
		}
		$parents = array_reverse($parents);
		return $parents[$top];
	}
	
	protected function UP_Top($id, $top){
		$top = $this->getOption('top');
		$parent = $id;
		while($top != $parent && $parent != ''){
			$lastID = $parent;
			$parent = \getObjField\defaultData::getData($lastID, 'parent');
		}
		return $lastID;
	}
}