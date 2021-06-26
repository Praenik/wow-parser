<?php

namespace Parser;

/**
 * @Entity
 */
class Player
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    public $id;

    /** @Column(type="string", length=255, nullable=false) */
    public $nickname;

    /** @Column(type="string", length=255, nullable=false) */
    public $guild;

    /** @Column(type="integer", length=255, nullable=false) */
    public $guild_rank;

    /** @Column(type="string", length=255, nullable=true) */
    public $class;

    /** @Column(type="string", length=255, nullable=true) */
    public $spec;

    /** @Column(type="integer", length=255, nullable=true) */
    public $gear;

    /** @Column(type="float", length=255, nullable=true) */
    public $rio;

    /** @Column(type="string", length=255, nullable=true) */
    public $progress;

    public function getSpecs() {
        if(!$this->class) return null;
        return self::SPECS_LIST[$this->class];
    }

    private const SPECS_LIST = [
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

}