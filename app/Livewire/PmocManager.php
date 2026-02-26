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
    public $showPlanEdit = false;
    public $showTaskEdit = false;
    public $showDelete = false;
    public $showPlanTasks = false;

    public $planId = null;
    public $taskId = null;

    public $planTasks = [];
    public $planLabel = '';

    public function closeModal()
    {
        // Se estiver fechando o modal de tasks de um plano, limpa as tasks.
        if ($this->showPlanTasks) {
            $this->reset([
                'planTasks',
                'planLabel'
            ]);
        }

        // Reseta essas variáveis de edit e delete sempre que fecha o modal.
        if ($this->planId || $this->taskId) {
            $this->reset([
                'planId',
                'taskId'
            ]);
        }

        $this->showPlanCreate =
        $this->showTaskCreate =
        $this->showDelete =
        $this->showPlanTasks =
        $this->showPlanEdit =
        $this->showTaskEdit =
        false;

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

    #[On('open-plan-edit')]
    public function openPlanEdit($id)
    {
        // Guarda Id para dar o update.
        $this->planId = $id;

        try {
            // Busca o plano com as tasks.
            $plan = PmocPlan::with('tasks')->findOrFail($this->planId);

            // Chama a função no form para preencher as variáveis.
            $this->planForm->setPlan($plan);

            // Abre o modal.
            $this->showPlanEdit = true;
        } catch (Exception $e) {
            $this->dispatch('notify-error', $e->getMessage());
        }
    }

    #[On('open-task-edit')]
    public function openTaskEdit($id)
    {
        // Guarda Id para dar o update.
        $this->taskId = $id;

        try {
            // Busca a task.
            $task = PmocTask::findOrFail($this->taskId);

            // Chama a função no form para preencher as variáveis.
            $this->taskForm->setTask($task);

            // Abre o modal.
            $this->showTaskEdit = true;
        } catch (Exception $e) {
            $this->dispatch('notify-error', $e->getMessage());
        }
    }

    public function editPlan()
    {
        // Valida os dados.
        $this->planForm->validate();

        try {
            // Busca o plano com as tasks.
            $plan = PmocPlan::findOrFail($this->planId);

            // Chama o método da service passando a model atual e os novos dados.
            $this->planService->update($plan, $this->planForm->all());

            // Mensagens de sucesso e atualização da tabela.
            $this->dispatch('notify-success', 'Plano de Manutenção alterado com sucesso!');
            $this->dispatch('plan-refresh');

            // Fecha modal somente se sucesso.
            $this->closeModal();

        } catch (Exception $e) {
            $this->dispatch('notify-error', $e->getMessage());
        }
    }

    public function editTask()
    {
        // Valida os dados.
        $this->taskForm->validate();

        try {
            // Busca a task.
            $task = PmocTask::findOrFail($this->taskId);

            // Chama o método da service passando a model atual e os novos dados.
            $this->taskService->update($task, $this->taskForm->all());

            // Mensagens de sucesso e atualização da tabela.
            $this->dispatch('notify-success', 'Tarefa de Manutenção alterada com sucesso!');
            $this->dispatch('task-refresh');

            // Fecha modal somente se sucesso.
            $this->closeModal();

        } catch (Exception $e) {
            $this->dispatch('notify-error', $e->getMessage());
        }
    }

    #[On('open-plan-tasks')]
    public function openPlanTasks($id)
    {
        try {
            // Busca os dados.
            $plan = PmocPlan::with('tasks')
            ->findOrFail($id);

            // Prepara as variáveis.
            $this->planLabel = $plan->plan;
            $this->planTasks = $this->planService->showTasks($plan);

            // Mostra o modal.
            $this->showPlanTasks = true;

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
