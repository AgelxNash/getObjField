<?php
include_once(dirname(dirname(__FILE__))."/defaultData.abstract.php");

class modSnippetData extends \getObjField\defaultData{
	protected $varname = 'user';
	protected $defaultField = 'name';
}