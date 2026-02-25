<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class PlanForm extends Form
{
    #[Validate('required', message: 'O nome do plano é obrigatório.')]
    #[Validate('max:255', message: 'O nome do plano deve ter no máximo 255 caracteres.')]
    public $plan = '';

    #[Validate('nullable')]
    #[Validate('max:500', message: 'A descrição deve ter no máximo 500 caracteres.')]
    public $descricao = '';

    #[Validate('array')]
    #[Validate('min:1', message: 'Pelo menos 1 tarefa deve ser selecionada.')]
    public $tarefasSelecionadas = [];
}
