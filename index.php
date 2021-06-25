<?php
require_once 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Sunra\PhpSimple\HtmlDomParser;

$guilds = require 'guilds.php';

//$arr[] = ['Ник', 'Класс', 'Гильдия']; //Убрал чтобы остались только ники персонажей, добавить мб потом отдельной строкой
$arr = [];

echo "Parsing wowprogress.com".PHP_EOL;
foreach ($guilds as $guild) {
    echo "Parse $guild".PHP_EOL;
    $str = "https://www.wowprogress.com/guild/eu/гордунни/" . str_replace(' ', '+', $guild) . "?roster";
    $html = HtmlDomParser::file_get_html($str, false, null, 0);
    $table = $html->find('#char_list_table')[0];
    $rows = $table->find('tr');

    foreach ($rows as $row) {
        $player['rank'] = $row->find('td', 0);
        if ($player['rank']->plaintext > 2) {
            $player['name'] = str_replace(' (u)','',$row->find('td', 1)->plaintext);
            $player['class'] = $row->class;
            //    echo $player['name'] . ' | ' . $player['class'] . "<br>";
            $arr[] = [$player['name'], $player['class'], $guild];
        }
    }
}

//Парсинг raider.io
echo "Start parse from rio".PHP_EOL;
$client = new Client([
    'base_uri' => "https://raider.io/api/v1/",
]);
for($i=0; $i < count($arr); $i++) {
    $name = $arr[$i][0];
    echo "Parsing $name".PHP_EOL;
    try {
        $response = $client->request('GET', 'characters/profile', [
            'query' => [
                'region' => 'eu',
                'realm' => 'Gordunni',
                'name' => $name,
                'fields' => 'raid_progression'
            ]
        ]);
        $rio_data = json_decode($response->getBody(), true);
        $arr[$i][3] = $rio_data['raid_progression']['castle-nathria']['summary'] ?? '';
    }catch (GuzzleException $e) {
        //тут бы отлов ошибок сделать
        $response = $e->getResponse();
        echo "$name error: {$response->getStatusCode()} - {$response->getReasonPhrase()}".PHP_EOL;
    }

}

echo "Generate output file".PHP_EOL;
$spreadsheet = new Spreadsheet();
$spreadsheet->getActiveSheet()->fromArray(
    $arr,
    NULL,
    'A1'
);
$write = new Xlsx($spreadsheet);
$write->save('output.xlsx');