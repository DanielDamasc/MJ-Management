<?php

namespace App\Livewire;

use App\Livewire\Forms\PlanForm;
use App\Livewire\Forms\TaskForm;
use App\Models\PmocPlan;
use App\Models\PmocTask;
use App\Services\PlanService;
use App\Services\TaskService;
use Exception;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
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
    public $showDelete = false;

    public $planId = null;
    public $taskId = null;

    public function closeModal()
    {
        $this->showPlanCreate = $this->showTaskCreate = $this->showDelete = false;
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

    #[On('confirm-plan-delete')]
    public function confirmPlanDelete($id)
    {
        $this->planId = $id;
        $this->showDelete = true;
    }

    #[On('confirm-task-delete')]
    public function confirmTaskDelete($id)
    {
        $this->taskId = $id;
        $this->showDelete = true;
    }

    public function delete()
    {
        // Fluxo para deletar um plano.
        if ($this->planId) {
            try {
                $plan = PmocPlan::findOrFail($this->planId);

                $this->planService->delete($plan);
                $this->dispatch('notify-success', 'Plano de Manutenção deletado com sucesso.');
                $this->dispatch('plan-refresh');

            } catch (Exception $e) {
                $this->dispatch('notify-error', $e->getMessage());

            } finally {
                $this->planId = null;
                $this->closeModal();

            }

            return ;
        }

        // Fluxo para deletar uma tarefa.
        if ($this->taskId) {
            try {
                $task = PmocTask::findOrFail($this->taskId);

                $this->taskService->delete($task);
                $this->dispatch('notify-success', 'Tarefa de Manutenção deletada com sucesso.');
                $this->dispatch('task-refresh');

            } catch (Exception $e) {
                $this->dispatch('notify-error', $e->getMessage());

            } finally {
                $this->taskId = null;
                $this->closeModal();

            }

            return ;
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
