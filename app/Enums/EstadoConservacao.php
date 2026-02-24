<?php

namespace App\Enums;

enum EstadoConservacao: string
{
    case RUIM = 'ruim';
    case REGULAR = 'regular';
    case BOM = 'bom';

    public function label()
    {
        return match($this) {
            self::RUIM => 'Ruim',
            self::REGULAR => 'Regular',
            self::BOM => 'Bom',
        };
    }

    public function color()
    {
        return match($this) {
            self::RUIM => 'red',
            self::REGULAR => 'blue',
            self::BOM => 'green',
        };
    }
}
