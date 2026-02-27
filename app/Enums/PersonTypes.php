<?php

namespace App\Enums;

enum PersonTypes: string
{
    case FISICA = 'F';
    case JURIDICA = 'J';

    public function label()
    {
        return match($this) {
            self::FISICA => 'Pessoa Física (CPF)',
            self::JURIDICA => 'Pessoa Jurídica (CNPJ)',
        };
    }
}
