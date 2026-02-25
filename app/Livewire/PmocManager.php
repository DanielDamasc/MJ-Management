<?php

namespace App\Livewire;

use App\Livewire\Forms\PlanForm;
use App\Livewire\Forms\TaskForm;
use App\Models\PmocTask;
use App\Services\PlanService;
use App\Services\TaskService;
use Exception;
use Livewire\Attributes\Layout;
use Livewire\Component;

class PmocManager extends Component
{
    // Injeção das services.
    protected PlanService $planService;
    protected TaskService $taskService;

    public function boot(PlanService $planService, TaskService $taskService)
    {
        $this->planService = $planService;
        $this->taskService = $taskService;
    }

    // Injeção dos atributos de cada uma das models.
    public PlanForm $planForm;
    public TaskForm $taskForm;


    // Atributos auxiliares.
    public $showPlanCreate = false;
    public $showTaskCreate = false;

    public function closeModal()
    {
        $this->showPlanCreate = $this->showTaskCreate = false;
        $this->resetValidation();
    }

    public function openCreatePlan()
    {
        $this->planForm->reset();
        $this->resetValidation();
        $this->showPlanCreate = true;
    }

    public function openCreateTask()
    {
        $this->taskForm->reset();
        $this->resetValidation();
        $this->showTaskCreate = true;
    }

    public function savePlan()
    {
        $this->planForm->validate();

        try {
            $this->planService->create($this->planForm->all());

            $this->closeModal();

            $this->dispatch('notify-success', 'Plano de Manutenção cadastrado com sucesso!');
            $this->dispatch('plan-refresh');

        } catch (Exception $e) {
            $this->dispatch('notify-error', $e->getMessage());
        }
    }

    public function saveTask()
    {
        $this->taskForm->validate();

        try {
            $this->taskService->create($this->taskForm->all());

            $this->closeModal();

            $this->dispatch('notify-success', 'Tarefa de Manutenção cadastrada com sucesso!');
            $this->dispatch('task-refresh');

        } catch (Exception $e) {
            $this->dispatch('notify-error', $e->getMessage());
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        // Manda as tasks para o componente para a criação do plano.
        $tasks = PmocTask::get();

        return view('livewire.pmoc-manager', [
            'tasks' => $tasks
        ]);
    }
}
