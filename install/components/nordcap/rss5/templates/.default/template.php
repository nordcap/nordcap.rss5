<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?foreach ($arResult as $news):?>
    <div class="news-element">
        <a href="<?=$news['LINK']?>" target="_blank"><h4><?=$news['TITLE']?></h4></a>
        <p><?=$news['DESCRIPTION']?></p>
    </div>
<?endforeach;?>