<?php

require_once 'init.php';

use Parser\Factory;
use Parser\Player;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$em = Factory::GetEntityManager();
$players = $em->getRepository(Player::class)->findAll();
$arr = [];
/* @var $player Player */
foreach ($players as $player) {
    $arr[] = [
        $player->nickname,
        $player->guild,
        $player->guild_rank,
        $player->class,
        $player->spec,
        $player->gear,
        $player->rio,
        $player->progress
    ];
}
$spreadsheet = new Spreadsheet();
$spreadsheet->getActiveSheet()->fromArray(
    $arr,
    NULL,
    'A1'
);
$write = new Xlsx($spreadsheet);
$write->save('output.xlsx');