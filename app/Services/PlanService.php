<?php

namespace App\Services;

use App\Models\AirConditioning;
use App\Models\PmocPlan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Exception;

class PlanService {
    public function create(array $data)
    {
        // Separa as tarefas do restante dos dados.
        $tarefas = $data['tarefasSelecionadas'];
        unset($data['tarefasSelecionadas']);

        return DB::transaction(function() use ($data, $tarefas) {

            // Cria o plano de Pmoc.
            $plano = PmocPlan::create($data);

            // Prepara o array para fazer o sync das tarefas com o plano.
            $tarefasSync = [];
            foreach ($tarefas as $tarefaId => $opcoes) {
                // Tudo certo.
                if (!empty($opcoes['selecionada']) && !empty($opcoes['periodicidade'])) {
                    $tarefasSync[$tarefaId] = [
                        'periodicidade' => $opcoes['periodicidade']
                    ];
                }

                // Caso tenha selecionado mas não marcou a periodicidade.
                if (!empty($opcoes['selecionada']) && empty($opcoes['periodicidade'])) {
                    throw new Exception("Você selecionou uma tarefa, mas esqueceu de definir a periodicidade dela.");
                }
            }

            // Verifica se formou o array das tarefas.
            if (empty($tarefasSync)) {
                throw new Exception("Ocorreu um erro. Nenhuma tarefa pôde ser vinculada.");
            }

            // Faz o sync das tarefas.
            $plano->tasks()->sync($tarefasSync);

            // Regra de negócio caso o plano seja padrão.
            $this->handlePlanoPadrao($plano);

            return $plano;

        });
    }

    public function update(PmocPlan $plan, array $data)
    {
        // Separa as tarefas do restante dos dados.
        $tarefas = $data['tarefasSelecionadas'];
        unset($data['tarefasSelecionadas']);

        return DB::transaction(function() use ($plan, $data, $tarefas) {
            // Atualiza os dados do plano de manutenção.
            $plan->update($data);

            // Prepara o array para fazer o sync das novas tasks com o plano.
            $tarefasSync = [];
            foreach ($tarefas as $tarefaId => $opcoes) {
                // Tudo certo.
                if (!empty($opcoes['selecionada']) && !empty($opcoes['periodicidade'])) {
                    $tarefasSync[$tarefaId] = [
                        'periodicidade' => $opcoes['periodicidade']
                    ];
                }

                // Caso tenha selecionado mas não marcou a periodicidade.
                if (!empty($opcoes['selecionada']) && empty($opcoes['periodicidade'])) {
                    throw new Exception("Você selecionou uma tarefa, mas esqueceu de definir a periodicidade dela.");
                }
            }

            // Verifica se formou o array das tarefas.
            if (empty($tarefasSync)) {
                throw new Exception("Ocorreu um erro. Nenhuma tarefa pôde ser vinculada.");
            }

            // Faz o sync das novas tarefas.
            $plan->tasks()->sync($tarefasSync);

            // Regra de negócio caso o plano seja padrão.
            $this->handlePlanoPadrao($plan);

            return $plan;
        });
    }

    public function delete(PmocPlan $plan)
    {
        // Validação caso plano já esteja vinculado a ar-condicionado.
        if ($plan->airConditioners()->exists()) {
            throw new Exception("Não se pode deletar um plano já vinculado a um equipamento.");
        }

        return $plan->delete();
    }

    public function showTasks(PmocPlan $plan)
    {
        $tasks = $plan->tasks->map(function($task) {
            return [
                'id' => $task->id,
                'task' => $task->task,
                'periodicidade' => \App\Enums\PmocPeriodicidade::from($task->pivot->periodicidade)->label(),
            ];
        });

        return $tasks->toArray();
    }

    // ==========================================
    // MÉTODOS PRIVADOS
    // ==========================================

    private function handlePlanoPadrao(PmocPlan $plan): void
    {
        if ($plan->padrao == true) {
            // Retira o "padrão" de todos os outros planos.
            PmocPlan::where('id', '!=', $plan->id)->update(['padrao' => false]);

            // Busca todos os equipamentos para aplicar o plano default.
            AirConditioning::whereNull('plano_id')
                ->whereHas('client', function (Builder $q) {
                    $q->where('pmoc', '=', true);
                })
                ->update([
                    'plano_id' => $plan->id
                ]);
        }
    }
}
