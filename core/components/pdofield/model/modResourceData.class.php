<?php
include_once(dirname(dirname(__FILE__))."/defaultData.abstract.php");

class modResourceData extends \pdoField\defaultData{
	protected $varname = 'resource';
	protected $defaultField = 'pagetitle';
	
	protected static $localConfigDefault = array(
		'processTV' => false
	);
	
	public function __construct(\modX &$modx, array $config = array()) {
		parent::__construct($modx, $config);
		parent::$ConfigDefault = array_merge(parent::$ConfigDefault, self::$localConfigDefault);
	}
	
	public function checkConfig($config){
		parent::checkConfig($config);
		$this->_config['processTV'] = $this->getOption('processTV', $config);
	}
	
	public function getData(){
		$mainField = $this->cacheObj->getColumns($this->getOption('object'));
		$value = $isTV = $processTV = null;
		$field = $this->getOption('field');
		$id = $this->getOption('id');
		if($field == 'id') {
			$object = null;
			$value = $id;
		}else{
			$object = true;
		}
		
		
		//$isTV = isset($scriptProperties['isTV']) ? $scriptProperties['isTV'] : 0;
		$isTV = !isset($mainField[$field]);
		$processTV = $this->getOption('processTV');
		
		if ($object && ($isTV || $processTV)) {
			$tv = $this->_modx->getObject('modTemplateVar',array('name'=>$field));
			if (!($tv instanceof modTemplateVar)){
				$value = null;
			}else{
				$value = ($processTV) ? $tv->renderOutput($id) : $tv->getValue($id);
			}
			if (is_null($value)) {
				$value = $this->getOption('output');
			}
		}
		return (is_null($value) && $object) ? parent::getData() : $value;
	}
}