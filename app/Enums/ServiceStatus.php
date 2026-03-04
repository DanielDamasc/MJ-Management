<?php

namespace App\Enums;

enum ServiceStatus: string
{
    case PENDENTE = 'pendente';
    case AGENDADO = 'agendado';
    case CONCLUIDO = 'concluido';
    case CANCELADO = 'cancelado';

    public function label()
    {
        return match($this) {
            self::PENDENTE => 'Pendente',
            self::AGENDADO => 'Agendado',
            self::CONCLUIDO => 'Concluido',
            self::CANCELADO => 'Cancelado',
        };
    }

    public function color()
    {
        return match($this) {
            self::PENDENTE => 'yellow',
            self::AGENDADO => 'blue',
            self::CONCLUIDO => 'green',
            self::CANCELADO => 'red',
        };
    }
}
