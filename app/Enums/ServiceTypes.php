<?php

namespace App\Enums;

enum ServiceTypes: string
{
    case INSTALACAO = 'instalacao';
    case HIGIENIZACAO = 'higienizacao';
    case MANUTENCAO = 'manutencao';

    public function label()
    {
        return match($this) {
            self::INSTALACAO => 'Instalação',
            self::HIGIENIZACAO => 'Higienização',
            self::MANUTENCAO => 'Manutenção',
        };
    }
}
