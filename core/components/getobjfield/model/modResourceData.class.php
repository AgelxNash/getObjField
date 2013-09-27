<?php
include_once(dirname(dirname(__FILE__))."/defaultData.abstract.php");

class modResourceData extends \getObjField\defaultData{
	protected $varname = 'resource';
	protected $defaultField = 'pagetitle';
	
	protected static $localConfigDefault = array(
		'processTV' => false,
		'isTV' => false
	);
	
	public function __construct(\modX &$modx, array $config = array()) {
		parent::__construct($modx, $config);
		parent::$ConfigDefault = array_merge(parent::$ConfigDefault, self::$localConfigDefault);
	}
	
	public function checkConfig($config){
		parent::checkConfig($config);
		$this->_config['processTV'] = $this->getOption('processTV', $config);
		$this->_config['isTV'] = $this->getOption('isTV', $config);
	}
	
	public function getData($id, $field){
		//$mainField = $this->cacheObj->getColumns($this->getOption('object'));
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
				if (is_null($value)) {
					$value = $this->getOption('output');
				}
			}
		}
		
		return (is_null($value) && $object) ? parent::getData($id, $field) : $value;
	}
}