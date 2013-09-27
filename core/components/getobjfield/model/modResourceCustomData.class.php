<?php
include_once(dirname(__FILE__)."/modResourceData.class.php");

class modResourceCustomData extends modResourceData{
	public function checkConfig($config){
		parent::checkConfig($config);
		$this->_config['object'] = 'modResource';
	}
	
	public function getData($id, $field){
		$value = null;
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