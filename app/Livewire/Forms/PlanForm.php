<?php

namespace App\Livewire\Forms;

use App\Models\PmocPlan;
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

    #[Validate('boolean')]
    public $padrao = false;

    #[Validate('array')]
    #[Validate('min:1', message: 'Pelo menos 1 tarefa deve ser selecionada.')]
    public $tarefasSelecionadas = [];

    public function setPlan(PmocPlan $plan)
    {
        // 1. Preenche para o edit.
        $this->plan = $plan->plan;
        $this->descricao = $plan->descricao;
        $this->padrao = $plan->padrao;

        // 2. Para marcar as checkboxes no edit.
        $tarefasFormatadas = [];

        foreach ($plan->tasks as $task) {
            $tarefasFormatadas[$task->id] = [
                'selecionada' => true,
                'periodicidade' => $task->pivot->periodicidade
            ];
        }

        $this->tarefasSelecionadas = $tarefasFormatadas;
    }
}
