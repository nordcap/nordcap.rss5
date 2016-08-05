<?php

/**
 * Created by PhpStorm.
 * User: alex
 * Date: 05.08.16
 * Time: 3:11
 */
namespace Nordcap\Rss5;

use Nordcap\Rss5\LentaTable;

class LastNews
{
    const MAX_COUNT_RSS = 5;
    const PATH_LENTA_RSS = "https://lenta.ru/rss";


    /**
     * Получение данных из ресурса
     * @return array
     */
    public static function getLastNews()
    {
        $ch = curl_init(self::PATH_LENTA_RSS);


        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $lentarss = curl_exec($ch);

        curl_close($ch);

        $rss = new \SimpleXMLElement($lentarss);

        $arLenta = array();

        for ($i = 0; $i < self::MAX_COUNT_RSS; $i++) {
            $arLenta[$i]['TITLE'] = trim($rss->channel->item[$i]->title);
            $arLenta[$i]['LINK'] = trim($rss->channel->item[$i]->link);
            $arLenta[$i]['DESCRIPTION'] = trim($rss->channel->item[$i]->description);
        }

        return $arLenta;
    }


    /**
     * Добавление данных в БД
     */
    public static function addDataDB()
    {
        $result = self::getLastNews();

        foreach ($result as $row) {
            LentaTable::add([
                "TITLE" => $row['TITLE'],
                "LINK" => $row['LINK'],
                "DESCRIPTION" => $row['DESCRIPTION']
            ]);
        }
    }

    /**
     * Обновление данных в БД
     */
    public static function updateDataDB()
    {
        $result = self::getLastNews();

        $arID = LentaTable::getList(["select"=>["ID"]])->fetchAll();


        foreach ($arID as $key=>$id) {
            LentaTable::update($id["ID"],[
                "TITLE"=>$result[$key]["TITLE"],
                "LINK"=>$result[$key]["LINK"],
                "DESCRIPTION"=>$result[$key]["DESCRIPTION"]
            ]);
        }

        return "\\Nordcap\\Rss5\\LastNews::updateDataDB();";


    }


}

