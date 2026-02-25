<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class TaskForm extends Form
{
    #[Validate('required', message: 'O nome da task é obrigatória.')]
    #[Validate('max:255', message: 'O nome da task deve ter no máximo 255 caracteres.')]
    public $task = '';
}
