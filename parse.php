<?php
require_once 'init.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Parser\Factory;
use Parser\Player;
use Sunra\PhpSimple\HtmlDomParser;

$guilds = require 'guilds.php';

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

$em = Factory::GetEntityManager();
$players = [];

// Парсинг wowprogress.com
echo "Parsing wowprogress.com".PHP_EOL;
foreach ($guilds as $guild) {
    echo "Parse $guild".PHP_EOL;
    $str = "https://www.wowprogress.com/guild/eu/гордунни/" . str_replace(' ', '+', $guild) . "?roster";
    $html = HtmlDomParser::file_get_html($str, false, null, 0);
    $table = $html->find('#char_list_table')[0];
    $rows = $table->find('tr');

    foreach ($rows as $row) {
        $player = new Player();
        $player->guild_rank = (int)$row->find('td', 0)->plaintext;
        $player->nickname = str_replace(' (u)','',$row->find('td', 1)->plaintext);
        $player->guild = $guild;
        $players[] = $player;
    }
}

//Парсинг raider.io
echo "Start parse from rio".PHP_EOL;
$client = new Client([
    'base_uri' => "https://raider.io/api/v1/",
]);

$player_repo = $em->getRepository(Player::class);
foreach ($players as $_player) {
    /* @var $player Player */
    $player = $player_repo->findOneBy(['nickname' => $_player->nickname]) ?: $_player;
    echo "Parsing {$player->nickname}".PHP_EOL;
    try {
        $response = $client->request('GET', 'characters/profile', [
            'query' => [
                'region' => 'eu',
                'realm' => 'Gordunni',
                'name' => $player->nickname,
                'fields' => 'class,gear,active_spec_name,mythic_plus_scores,raid_progression'
            ]
        ]);
        $rio_data = json_decode($response->getBody(), true);
        $player->class = $classes[$rio_data['class']] ?? null;
        $spec = $rio_data['active_spec_name'] ?? null;
        $player->spec =  $player->class && $spec ? $player->getSpecs()[$spec] : null;
        $player->rio = (float)$rio_data['mythic_plus_scores']['all'] ?? null;
        $player->gear = (int)$rio_data['gear']['item_level_equipped'] ?? null;
        $player->progress = $rio_data['raid_progression']['castle-nathria']['summary'] ?? null;
    } catch (GuzzleException $e) {
        //тут бы отлов ошибок сделать
        $response = $e->getResponse();
        echo "{$player->nickname} error: {$response->getStatusCode()} - {$response->getReasonPhrase()}".PHP_EOL;
    }
    $em->persist($player);
    $em->flush();
}

