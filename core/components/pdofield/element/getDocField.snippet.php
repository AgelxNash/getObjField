<?php
$scriptProperties['object'] = isset($scriptProperties['object']) ? $scriptProperties['object'] : 'modResource';
$dir = $modx->getOption('pdofield.core_path', null, $modx->getOption('core_path').'components/pdofield/');
$className = $scriptProperties['object'].'Data';

$pdoField = $modx->getService($className, $className, $dir.'model/', $scriptProperties);
$pdoField = (!($pdoField instanceof $className)) ? new \pdoField\xNop() : $pdoField->checkConfig($scriptProperties);

return $pdoField->getData();