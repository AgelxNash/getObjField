<?php

$properties = array();

$tmp = array(
	'queryMode' => array(
		'type' => 'list',
		'options' => array(
			array('text' => PKG_NAME_LOWER.'.xpdo', 'value' => '0'),
			array('text' => PKG_NAME_LOWER.'.pdo', 'value' => '1'),
			array('text' => PKG_NAME_LOWER.'.pdo_no_log', 'value' => '2'),
		),
		'value' => '2',
	),
	'output' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'prepare' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'processTV' => array(
		'type' => 'list',
		'options' => array(
			array('text' => PKG_NAME_LOWER.'.no', 'value' => '0'),
			array('text' => PKG_NAME_LOWER.'.yes', 'value' => '1'),
		),
		'value' => '0',
	),
	'isTV' => array(
		'type' => 'list',
		'options' => array(
			array('text' => PKG_NAME_LOWER.'.no', 'value' => '0'),
			array('text' => PKG_NAME_LOWER.'.yes', 'value' => '1'),
		),
		'value' => '0',
	),
);

foreach ($tmp as $k => $v) {
	$properties[] = array_merge(
		array(
			'name' => $k,
			'desc' => PKG_NAME_LOWER . '.prop_' . $k,
			'lexicon' => PKG_NAME_LOWER . ':properties',
		), $v
	);
}

return $properties;