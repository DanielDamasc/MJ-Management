<?php

namespace App\Console\Commands;

use App\Enums\ServiceStatus;
use App\Enums\ServiceTypes;
use App\Models\Client;
use App\Models\OrderService;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GeneratePmocServices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pmoc:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera as Ordens de Serviço de PMOC para o mês atual com base na periodicidade.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando geração de PMOC para o mês atual...');
        $hoje = Carbon::now();

        // Busca os clientes PMOC que já tem data de início definida.
        $clientes = Client::where('pmoc', true)
            ->whereNotNull('inicio_pmoc')
            ->with(['airConditioners.plan.tasks'])
            ->get();

        // Caso não tenha clientes, retorna sucesso.
        if ($clientes->isEmpty()) {
            $this->info('Nenhum cliente elegível para PMOC encontrado.');
            return Command::SUCCESS;
        }

        try {
            // Devo passar essa variável por referência, tendo em vista que vou mudar ela no final da função.
            $osCriadas = 0;

            DB::transaction(function () use ($clientes, $hoje, &$osCriadas) {

                foreach ($clientes as $cliente) {
                    $dataInicio = Carbon::parse($cliente->inicio_pmoc);

                    // Calcula a quantidade de meses para contratos já vigentes.
                    // O cálculo considera meses diferentes, independente do dia, como 1 mês de diferença.
                    $mesesPassados = (($hoje->year - $dataInicio->year) * 12) + ($hoje->month - $dataInicio->month);

                    // Caso o contrato inicie em um mês futuro, ignora e vai para o próximo cliente.
                    if ($mesesPassados < 0) {
                        continue;
                    }

                    $mesReferencia = $mesesPassados + 1;

                    // Agrupa os equipamentos do cliente pelo plano de manutenção.
                    $equipamentosPorPlano = $cliente->airConditioners->groupBy('plano_id');

                    foreach ($equipamentosPorPlano as $planoId => $equipamentos) {

                        // Ignora se o equipamento não tem plano.
                        if (!$planoId) {
                            continue;
                        }

                        // Pega a referência a partir do primeiro equipamento do grupo.
                        $plano = $equipamentos->first()->plan;
                        $tarefasDoMes = [];

                        foreach ($plano->tasks as $tarefa) {
                            // Pega a periodicidade da task.
                            $periodicidade = $tarefa->pivot->periodicidade;

                            // Caso a task seja para o cliente executar, não inclui na ordem de serviço.
                            if ($tarefa->pivot->cliente_executa) {
                                continue;
                            }

                            // A tarefa entra neste mês caso o resto da divisão dos meses pela periodicidade seja 0.
                            if ($mesReferencia % $periodicidade == 0) {
                                // true indica os checkboxes das tarefas que serão marcados.
                                $tarefasDoMes[$tarefa->id] = true;
                            }
                        }

                        // Só gera a OS se tiver alguma tarefa para este mês.
                        if (!empty($tarefasDoMes)) {

                            // Verifica se a ordem de serviço para este mês já não foi criada.
                            $osExistente = OrderService::where('cliente_id', $cliente->id)
                                ->where('tipo', ServiceTypes::HIGIENIZACAO->value)
                                ->whereMonth('created_at', $hoje->month)
                                ->whereYear('created_at', $hoje->year)
                                ->whereHas('airConditioners', function($query) use ($planoId) {
                                    $query->where('plano_id', $planoId);
                                })
                                ->exists();

                            if ($osExistente) {
                                $this->info("Ordem de Serviço já existente para este mês.");
                            }

                            if (!$osExistente) {

                                // Cria a ordem de serviço.
                                $os = OrderService::create([
                                    // Atributos definidos depois.
                                    'data_servico' => null,
                                    'executor_id' => null,
                                    'horario' => null,

                                    'cliente_id' => $cliente->id,
                                    'detalhes' => [
                                        'tarefas' => $tarefasDoMes
                                    ],
                                    'status' => ServiceStatus::PENDENTE->value,
                                    'tipo' => ServiceTypes::HIGIENIZACAO->value,
                                    'total' => 0,
                                ]);

                                // Vincula os equipamentos dessa OS na pivot.
                                $syncData = [];
                                foreach ($equipamentos as $ac) {
                                    $syncData[$ac->id] = ['valor' => 0];
                                }

                                $os->airConditioners()->sync($syncData);

                                $osCriadas++;

                            }
                        }
                    }
                }

            });

            $this->info("Concluído! {$osCriadas} Ordem(ns) de Serviço geradas.");
            Log::info("CRON PMOC: {$osCriadas} OS geradas com sucesso.");

            return Command::SUCCESS;

        } catch (Exception $e) {
            $this->error('Erro ao gerar PMOC: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
