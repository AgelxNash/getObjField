<?php
include_once(dirname(__FILE__)."/modResourceData.class.php");

class modResourceCustomData extends modResourceData{
	
	protected static $localConfigDefault = array(
		'top' => 0
	);
	
	public function __construct(\modX &$modx, array $config = array()) {
		parent::__construct($modx, $config);
		parent::$ConfigDefault = array_merge(parent::$ConfigDefault, self::$localConfigDefault);
	}
	
	public function checkConfig($config){
		parent::checkConfig($config);
		$this->_config['object'] = 'modResource';
		$this->_config['top'] = $this->getOption('top', $config);
	}
	
	public function getData($id, $field){
		$value = null;
		if($this->getOption('top')>0){
			$top = $this->getOption('top');
			for($i=0; $i<$top; $i++){
				$id = \getObjField\defaultData::getData($id, 'parent');
			}
		}
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
				$value = \getObjField\defaultData::getData($id, 'parent');
				$value = ($value>0) ? $this->getData($value, 'title') : '';
				break;
			}
			default:{
				$value = parent::getData($id,$field);
			}
		}
		return $value;
	}
}