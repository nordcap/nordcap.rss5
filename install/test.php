<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03.08.16
 * Time: 22:51
 */
function GetPath($notDocumentRoot = false)
{
    if ($notDocumentRoot) {
        //получаем путь относительно корня сайта
        return str_ireplace(Application::getDocumentRoot(), '', dirname(__DIR__));
    } else {
        return dirname(__DIR__);
    }
}



print GetPath();

print "<br/>";

print $_SERVER['DOCUMENT_ROOT'] . GetPath() . '/install/admin/';