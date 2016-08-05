<?php

/**
 * Created by PhpStorm.
 * User: alex
 * Date: 04.08.16
 * Time: 22:23
 */
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Nordcap\Rss5\LentaTable;


class Rss5 extends CBitrixComponent
{
    public $arResult; //переменные и константы клвсса

    protected function checkModules()
    {
        if (!Loader::includeModule('nordcap.rss5'))
            throw new \Bitrix\Main\LoaderException(Loc::getMessage('NORDCAP_RSS5_MODULE_NOT_INSTALLED'));

    }


    public function showNews()
    {
        $result = LentaTable::getList([
            "select" => ["ID", "TITLE", "LINK", "DESCRIPTION"],
            "order" => ["ID" => "ASC"]
        ]);

        return $result;
    }

    public function executeComponent()
    {
        $this->includeComponentLang('class.php');
        $this->checkModules();
        /*Ваш код*/
        $dbResult = $this->showNews();
/*        while ($row = $dbResult->fetch()) {
            $this->arResult[] = $row;
        }*/

        $this->arResult = $dbResult->fetchAll();

        $this->includeComponentTemplate();

    }


}