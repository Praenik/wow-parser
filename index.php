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

$classes = [
    'Druid' => 'Друид',
    'Monk' => 'Монах',
    'Demon Hunter' => 'Охотник на демонов',
    'Shaman' => 'Шаман',
    'Hunter' => 'Охотник',
    'Death Knight' => 'Рыцарь смерти',
    'Mage' => 'Маг',
    'Paladin' => 'Паладин',
    'Priest' => 'Жрец',
    'Warrior' => 'Воин',
    'Rogue' => 'Разбойник',
    'Warlock' => 'Чернокнижник'
];

$specs = [
    'Друид' => [
        'Restoration' => 'Исцеление',
        'Balance' => 'Баланс',
        'Feral' => 'Сила зверя',
        'Guardian' => 'Страж'
    ],
    'Монах' => [
        'Windwalker' => 'Танцующий с ветром',
        'Mistweaver' => 'Ткач туманов',
        'Brewmaster' => 'Хмелевар'
    ],
    'Охотник на демонов' => [
        'Havoc' => 'Истребление',
        'Vengeance' => 'Месть'
    ],
    'Охотник' => [
        'Beast Mastery' => 'Повелитель зверей',
        'Marksmanship' => 'Стрельба',
        'Survival' => 'Выживание'
    ],
    'Рыцарь смерти' => [
        'Frost' => 'Лед',
        'Unholy' => 'Нечестивость',
        'Blood' => 'Кровь'
    ],
    'Шаман' => [
        'Restoration' => 'Исцеление',
        'Elemental' => 'Стихии',
        'Enhancement' => 'Совершенствование'
    ],
    'Маг' => [
        'Frost' => 'Лед',
        'Fire' => 'Огонь',
        'Arcane' => 'Тайная магия'
    ],
    'Паладин' => [
        'Retribution' => 'Воздаяние',
        'Holy' => 'Свет',
        'Protection' => 'Защита',
    ],
    'Жрец' => [
        'Shadow' => 'Тьма',
        'Discipline' => 'Послушание',
        'Holy' => 'Свет'
    ],
    'Воин' => [
        'Fury' => 'Неистовство',
        'Arms' => 'Оружие',
        'Protection' => 'Защита'
    ],
    'Разбойник' => [
        'Outlaw' => 'Головорез',
        'Assassination' => 'Ликвидация',
        'Subtlety' => 'Скрытность'
    ],
    'Чернокнижник' => [
        'Destruction' => 'Разрушение',
        'Demonology' => 'Демонология',
        'Affliction' => 'Колдовство'
    ]
];

// Парсинг wowprogress.com
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
            $player['rank'] = (int)$player['rank']->plaintext;
            $arr[] = [$player['name'], $guild, $player['rank']];
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
                'fields' => 'class,gear,active_spec_name,mythic_plus_scores,raid_progression'
            ]
        ]);
        $rio_data = json_decode($response->getBody(), true);
        array_push($arr[$i], $classes[$rio_data['class']] ?? '',
            $specs[$classes[$rio_data['class']]][$rio_data['active_spec_name']] ?? '',
            $rio_data['gear']['item_level_equipped'] ?? '',
            $rio_data['mythic_plus_scores']['all'] ?? '',
            $rio_data['raid_progression']['castle-nathria']['summary'] ?? '');
    } catch (GuzzleException $e) {
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