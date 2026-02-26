<?php

namespace App\Livewire\Forms;

use App\Models\PmocTask;
use Livewire\Attributes\Validate;
use Livewire\Form;

class TaskForm extends Form
{
    #[Validate('required', message: 'O nome da task é obrigatória.')]
    #[Validate('max:255', message: 'O nome da task deve ter no máximo 255 caracteres.')]
    public $task = '';

    public function setTask(PmocTask $task)
    {
        // 1. Preenche para o edit.
        $this->task = $task->task;
    }
}
