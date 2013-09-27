<?php
include_once(dirname(dirname(__FILE__))."/defaultData.abstract.php");

class modResourceData extends \pdoField\defaultData{
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
	}
	
	public function getData($id, $field){
		$mainField = $this->cacheObj->getColumns($this->getOption('object'));
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
			}else{
				switch($field){
					case 'title':{
						$value = parent::getData($id, 'longtitle');
						if($value==''){
							$value = parent::getData($id, 'pagetitle');
						}
						break;
					}
					case 'menuname':{
						$value = parent::getData($id, 'menutitle');
						if($value==''){
							$value = parent::getData($id, 'pagetitle');
						}
						break;
					}
					case 'parentname':{
						$value = parent::getData($id, 'parent');
						if($value>0){
							$value = $this->getData($value, 'title');
						}else{
							$value = '';
						}
						break;
					}
				}
			}
		}
		
		return (is_null($value) && $object) ? parent::getData($id, $field) : $value;
	}
}