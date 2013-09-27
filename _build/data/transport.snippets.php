<?php
$snippets = array();

$tmp = array(
	'getObjField' => array(
		'file' => 'getObjField',
		'description' => '',
	),
);

foreach ($tmp as $k => $v) {
	/* @avr modSnippet $snippet */
	$snippet = $modx->newObject('modSnippet');
	$snippet->fromArray(array(
		'id' => 0,
		'name' => $k,
		'description' => @$v['description'],
		'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/'.$v['file'].'.snippet.php'),
		'source' => 1
	),'',true,true);

	$properties = include $sources['build'].'properties/properties.'.$v['file'].'.php';
	$snippet->setProperties($properties);

	$snippets[] = $snippet;
}

unset($tmp, $properties);
return $snippets;