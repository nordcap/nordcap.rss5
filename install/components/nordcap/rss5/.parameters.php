<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(

		"ITEMS_LIMIT" => Array(
			"PARENT" => "BASE",
			"NAME" => "ITEMS_LIMIT",
			"TYPE" => "STRING",
			"DEFAULT" => "10",
		),

		"CACHE_TIME"  =>  Array("DEFAULT"=>3600),

	),
);
?>