<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03.08.16
 * Time: 1:28
 */

use Bitrix\Main\Localization\Loc;


if (!check_bitrix_sessid())
    return;

Loc::LoadMessages(__FILE__);
?>

<form action="<?= $APPLICATION->GetCurPage() ?>">
    <?= bitrix_sessid_post()?>
    <input type="hidden" name="lang" value="<?= LANGUAGE_ID ?>">
    <input type="hidden" name="id" value="nordcap.rss5">
    <input type="hidden" name="uninstall" value="Y">
    <input type="hidden" name="step" value="2">
    <?= CAdminMessage::ShowMessage(Loc::getMessage("MOD_UNINST_WARN"))?>
    <p><?= Loc::getMessage("MOD_UNINST_SAVE")?></p>
    <p>
        <input type="checkbox" name="savedata" id="savedata" value="Y" checked>
        <label for="savedata"><?= Loc::getMessage("MOD_UNINST_SAVE_TABLES")?></label>
    </p>

    <input type="submit" name="" value="<?= Loc::getMessage("MOD_UNINST_DEL") ?>">
</form>



