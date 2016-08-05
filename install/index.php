<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03.08.16
 * Time: 1:27
 */

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config as Conf;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\Application;
use Nordcap\Rss5\LentaTable;
use Nordcap\Rss5\LastNews;


Loc::LoadMessages(__FILE__);


class nordcap_rss5 extends CModule
{
    public $exclusionAdminFiles;

    public function __construct()
    {
        $arModuleVersion = array();
        include(__DIR__ . "/version.php");

        $this->exclusionAdminFiles = array(
            '..',
            '.',
            'menu.php',
            'operation_description.php',
            'task_description.php'
        );

        $this->MODULE_ID = 'nordcap.rss5';
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = Loc::getMessage("NORDCAP_RSS5_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("NORDCAP_RSS5_MODULE_DESC");

        $this->PARTNER_NAME = Loc::getMessage("NORDCAP_RSS5_PARTNER_NAME");
        $this->PARTNER_URI = Loc::getMessage("NORDCAP_RSS5_PARTNER_URI");

        $this->MODULE_SORT = 1;
        $this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = "Y";
        $this->MODULE_GROUP_RIGHTS = "Y";
    }


    public function DoInstall()
    {
        global $APPLICATION;

        if ($this->isVersionD7()) {
            \Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);
            $this->InstallDB();
            $this->InstallEvents();
            $this->InstallFiles();
            CAgent::AddAgent("\\Nordcap\\Rss5\\LastNews::updateDataDB();","nordcap.rss5","N","600");


        } else {
            $APPLICATION->ThrowException(Loc::getMessage("NORDCAP_RSS5_INSTALL_ERROR_VERSION"));
        }

        $APPLICATION->IncludeAdminFile(Loc::getMessage("NORDCAP_RSS5_INSTALL_TITLE"), $this->GetPath() . "/install/step.php");

    }

    public function DoUninstall()
    {
        global $APPLICATION;

        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();

        if ($request['step'] < 2) {
            $APPLICATION->IncludeAdminFile(Loc::getMessage("NORDCAP_RSS5_UNINSTALL_TITLE"), $this->GetPath() . "/install/unstep1.php");
        } elseif ($request['step'] == 2) {
            $this->UnInstallFiles();
            $this->UnInstallEvents();
            CAgent::RemoveAgent("\\Nordcap\\Rss5\\LastNews:updateDataDB();","nordcap.rss5");
        }
        if ($request['savedata'] != "Y") {
            $this->UnInstallDB();
        }

        \Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile(Loc::getMessage("NORDCAP_RSS5_UNINSTALL_TITLE"), $this->GetPath() . "/install/unstep2.php");
    }

    /**
     * Проверяем что система поддерживает D7
     * @return bool
     */
    public function isVersionD7()
    {
        return CheckVersion(\Bitrix\Main\ModuleManager::getVersion('main'), '14.00.00');
    }

    public function InstallDB()
    {
        Loader::includeModule($this->MODULE_ID);

        if (!Application::getConnection(\Nordcap\Rss5\LentaTable::getConnectionName())->isTableExists(Base::getInstance('\Nordcap\Rss5\LentaTable')->getDBTableName())) {
            Base::getInstance('\Nordcap\Rss5\LentaTable')->createDbTable();
        }

        LastNews::addDataDB(); //заполнение данных в БД


    }


    public function InstallEvents()
    {
        \Bitrix\Main\EventManager::getInstance()->registerEventHandler($this->MODULE_ID, 'TestEventNordcapRSS5', $this->MODULE_ID, '\Nordcap\Rss5\Event', 'eventHandler');
        return  true;
    }

    /**
     * Копирование компонентов и административных страниц в системные каталоги
     * @param array $arParams
     * @return bool
     */
    public function InstallFiles($arParams = array())
    {
        $path = $this->GetPath() . "/install/components";

        if (\Bitrix\Main\IO\Directory::isDirectoryExists($path)) {
            CopyDirFiles($path, $_SERVER['DOCUMENT_ROOT'] . "/bitrix/components/", true, true);
        } else {
            throw new \Bitrix\Main\IO\InvalidPathException($path);
        }


        if (\Bitrix\Main\IO\Directory::isDirectoryExists($path = $this->GetPath() . "/admin")) {
            //копирование из nordcap.rss5/install/admin
            CopyDirFiles($this->GetPath() . "/install/admin", $_SERVER['DOCUMENT_ROOT'] . "/bitrix/admin");
            //подключение файлов из nordcap.rss5/admin
            if ($dir = opendir($path)) {
                while (false !== $item = readdir($dir)) {
                    if (in_array($item, $this->exclusionAdminFiles))
                        continue;
                    file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/bitrix/admin/" . $this->MODULE_ID . "_" . $item,
                        '<? require($_SERVER["DOCUMENT_ROOT"]."' . $this->GetPath(true) . '/admin/' . $item . '");?>');
                }
                closedir($dir);
            }
        }
        return true;

    }

    public function UnInstallEvents()
    {
        \Bitrix\Main\EventManager::getInstance()->unRegisterEventHandler($this->MODULE_ID, 'TestEventNordcapRSS5', $this->MODULE_ID, '\Nordcap\Rss5\Event', 'eventHandler');
        return  true;
    }

    /**
     * Удаление компонентов и административных страниц из системы
     * @return bool
     */
    public function UnInstallFiles()
    {
        \Bitrix\Main\IO\Directory::deleteDirectory($_SERVER['DOCUMENT_ROOT'] . "/bitrix/components/nordcap/");
        if (\Bitrix\Main\IO\Directory::isDirectoryExists($path = $this->GetPath() . "/admin")) {
            DeleteDirFiles($this->GetPath() . '/install/admin/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin');
            if ($dir = opendir($path)) {
                while (false !== $item = readdir($dir)) {
                    if (in_array($item, $this->exclusionAdminFiles))
                        continue;
                    \Bitrix\Main\IO\File::deleteFile($_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin' . $this->MODULE_ID . "_" . $item);
                }
                closedir($dir);
            }
        }
        return true;
    }

    public function UnInstallDB()
    {
        Loader::includeModule($this->MODULE_ID);

        Application::getConnection(\Nordcap\Rss5\LentaTable::getConnectionName())->
        queryExecute('drop table if exists ' . Base::getInstance('\Nordcap\Rss5\LentaTable')->getDBTableName());

        Option::delete($this->MODULE_ID);


    }


    /**
     * Определяем место размещения модуля
     * @param bool $notDocumentRoot
     * @return mixed|string
     */
    public function GetPath($notDocumentRoot = false)
    {
        if ($notDocumentRoot) {
            //получаем путь относительно корня сайта
            return str_ireplace(Application::getDocumentRoot(), '', dirname(__DIR__));
        } else {
            return dirname(__DIR__);
        }
    }




}

