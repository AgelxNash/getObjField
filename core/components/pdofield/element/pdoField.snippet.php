<?php
$scriptProperties['object'] = isset($scriptProperties['object']) ? $scriptProperties['object'] : 'modResource';
$dir = $modx->getOption('pdofield.core_path', null, $modx->getOption('core_path').'components/pdofield/');
$className = $scriptProperties['object'].'Data';

$pdoField = $modx->getService($className, $className, $dir.'model/', $scriptProperties);
if(!($pdoField instanceof $className)){
	$pdoField = new \pdoField\xNop();
}
$pdoField->checkConfig($scriptProperties);

return $pdoField->prepareValue($pdoField->getData($pdoField->getOption('id'), $pdoField->getOption('field')));