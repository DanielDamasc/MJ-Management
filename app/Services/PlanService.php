<?php

namespace App\Services;

use App\Models\PmocPlan;
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
                if (!empty($opcoes['selecionada']) && !empty($opcoes['periodicidade'])) {
                    $tarefasSync[$tarefaId] = [
                        'periodicidade' => $opcoes['periodicidade']
                    ];
                }
            }

            // Verifica se formou o array das tarefas.
            if (empty($tarefasSync)) {
                throw new Exception("Ocorreu um erro. Nenhuma tarefa pôde ser vinculada.");
            }

            // Faz o sync das tarefas.
            $plano->tasks()->sync($tarefasSync);

            return $plano;

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
}
