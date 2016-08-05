<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => "Агрегатор RSS",
	"DESCRIPTION" => "Получение списка последних новостей в формате RSS",
	"ICON" => "/images/icon.gif",
	"SORT" => 10,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "nordcap", // for example "my_project"
		/*"CHILD" => array(
			"ID" => "", // for example "my_project:services"
			"NAME" => "",  // for example "Services"
		),*/
	),
	"COMPLEX" => "N",
);

?>