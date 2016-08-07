<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 05.08.16
 * Time: 0:59
 */

namespace Nordcap\Rss5;

use Bitrix\Main\Entity;
use Bitrix\Main\Type;

class LentaTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return "lenta_rss5";
    }


    public static function getConnectionName()
    {
        return "default";
    }

    public static function getMap()
    {
        return [
            //id
            new Entity\IntegerField('ID',['primary'=>true, 'autocomplete'=>true]),
            //title
            new Entity\StringField('TITLE'),
            //link
            new Entity\StringField('LINK'),
            //description
            new Entity\TextField('DESCRIPTION')
        ];
    }

}