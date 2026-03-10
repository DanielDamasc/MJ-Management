<?php

namespace App\Enums;

enum PmocPeriodicidade: int
{
    case MENSAL = 1;
    case TRIMESTRAL = 3;
    case SEMESTRAL = 6;
    case ANUAL = 12;

    public function label()
    {
        return match($this) {
            self::MENSAL => 'Mensal',
            self::TRIMESTRAL => 'Trimestral',
            self::SEMESTRAL => 'Semestral',
            self::ANUAL => 'Anual',
        };
    }

    public function firstLetter()
    {
        return match($this) {
            self::MENSAL => 'M',
            self::TRIMESTRAL => 'T',
            self::SEMESTRAL => 'S',
            self::ANUAL => 'A',
        };
    }
}
