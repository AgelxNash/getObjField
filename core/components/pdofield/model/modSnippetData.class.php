<?php
include_once(dirname(dirname(__FILE__))."/defaultData.abstract.php");

class modSnippetData extends \pdoField\defaultData{
	protected $varname = 'user';
	protected $defaultField = 'name';
}