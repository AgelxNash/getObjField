<?php
$scriptProperties['object'] = isset($scriptProperties['object']) ? $scriptProperties['object'] : 'modResource';
$dir = $modx->getOption('getobjfield.core_path', null, $modx->getOption('core_path').'components/getobjfield/');
$className = $scriptProperties['object'].'Data';

$getObjField = $modx->getService($className, $className, $dir.'model/', $scriptProperties);
if(!($getObjField instanceof $className)){
	$getObjField = new \getObjField\xNop();
}
$getObjField->checkConfig($scriptProperties);

return $getObjField->prepareValue($getObjField->getData($getObjField->getOption('id'), $getObjField->getOption('field')));