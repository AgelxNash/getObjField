<?php
$mtime = microtime();
$mtime = explode(' ', $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

require_once 'build.config.php';

/* define sources */
$root = dirname(dirname(__FILE__)).'/';
$sources = array(
	'root' => $root,
	'build' => $root . '_build/',
	'data' => $root . '_build/data/',
	'resolvers' => $root . '_build/resolvers/',
	'chunks' => $root.'core/components/'.PKG_NAME_LOWER.'/elements/chunks/',
	'snippets' => $root.'core/components/'.PKG_NAME_LOWER.'/elements/snippets/',
	'plugins' => $root.'core/components/'.PKG_NAME_LOWER.'/elements/plugins/',
	'lexicon' => $root . 'core/components/'.PKG_NAME_LOWER.'/lexicon/',
	'docs' => $root.'core/components/'.PKG_NAME_LOWER.'/docs/',
	'pages' => $root.'core/components/'.PKG_NAME_LOWER.'/elements/pages/',
	'source_core' => $root.'core/components/'.PKG_NAME_LOWER,
);
unset($root);

require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
require_once $sources['build'] . '/includes/functions.php';

$modx= new modX();
$modx->initialize('mgr');
echo '<pre>'; /* used for nice formatting of log messages */
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');
$modx->getService('error','error.modError');

$modx->loadClass('transport.modPackageBuilder','',false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME_LOWER,PKG_VERSION,PKG_RELEASE);
$builder->registerNamespace(PKG_NAME_LOWER,false,true,'{core_path}components/'.PKG_NAME_LOWER.'/');
$modx->log(modX::LOG_LEVEL_INFO,'Created Transport Package and Namespace.');

// Load lexicon for properties
$modx->getService('lexicon','modLexicon');
$modx->lexicon->load(PKG_NAME.':properties');

/* create category */
$modx->log(xPDO::LOG_LEVEL_INFO,'Created category.');
/* @var modCategory $category */
$category= $modx->newObject('modCategory');
$category->set('category',PKG_NAME);
/* create category vehicle */
$attr = array(
	xPDOTransport::UNIQUE_KEY => 'category',
	xPDOTransport::PRESERVE_KEYS => false,
	xPDOTransport::UPDATE_OBJECT => true,
	xPDOTransport::RELATED_OBJECTS => true,
);

/* add snippets */

$attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['Snippets'] = array (
	xPDOTransport::PRESERVE_KEYS => false,
	xPDOTransport::UPDATE_OBJECT => true,
	xPDOTransport::UNIQUE_KEY => 'name',
);
$snippets = include $sources['data'].'transport.snippets.php';
if (!is_array($snippets)) {
	$modx->log(modX::LOG_LEVEL_ERROR,'Could not package in snippets.');
} else {
	$category->addMany($snippets);
	$modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.count($snippets).' snippets.');
}

$vehicle = $builder->createVehicle($category,$attr);

/* now pack in resolvers */
$vehicle->resolve('file',array(
	'source' => $sources['source_core'],
	'target' => "return MODX_CORE_PATH . 'components/';",
));

flush();
$builder->putVehicle($vehicle);

/* now pack in the license file, readme and setup options */
$builder->setPackageAttributes(array(
	'changelog' => file_get_contents($sources['docs'] . 'changelog.txt')
	,'license' => file_get_contents($sources['docs'] . 'license.txt')
	,'readme' => file_get_contents($sources['docs'] . 'readme.txt')
	/*
	,'setup-options' => array(
		'source' => $sources['build'].'setup.options.php',
	),
	*/
));
$modx->log(modX::LOG_LEVEL_INFO,'Added package attributes and setup options.');

/* zip up package */
$modx->log(modX::LOG_LEVEL_INFO,'Packing up transport package zip...');
$builder->pack();

$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tend= $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);

$modx->log(modX::LOG_LEVEL_INFO,"\n<br />Execution time: {$totalTime}\n");
echo '</pre>';