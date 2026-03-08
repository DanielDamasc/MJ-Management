<?php

namespace App\Console\Commands;

use App\Enums\ServiceStatus;
use App\Enums\ServiceTypes;
use App\Jobs\SendWhatsappRemindersJob;
use App\Models\Client;
use App\Models\OrderService;
use Illuminate\Console\Command;
use Log;

class SendWhatsappReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-whatsapp-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica a data da próxima higienização do cliente e se o status é agendado, caso a próxima higienização esteja agendada para os próximos 7 dias, dispara uma mensagem de lembrete para o cliente.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // --- VARIÁVEIS AUXILIARES ---

        $start = now()->startOfDay(); // Início é o dia de hoje.
        $end = now()->addDays(7)->endOfDay(); // Fim é hoje adicionados 7 dias.
        $segundoAviso = now()->subMonths(2); // Segundo aviso da higienização.

        // --- LÓGICA DE BUSCA ---

        $this->info("Buscando clientes com data de próxima higienização entre {$start} e {$end}");

        $clientes = Client::where(function ($q) use ($start, $end, $segundoAviso) {
            // 1. Primeira notificação, confere também a data da próxima higienização.
            $q->where(function ($sub1) use ($start, $end) {
                $sub1->where('qtd_notificacoes', 0)
                    ->whereNull('ultima_notificacao')
                    ->whereHas('airConditioners', function ($acQuery) use ($start, $end) {
                        $acQuery->whereBetween('prox_higienizacao', [$start, $end]);
                    });
            })

            // 2. Segunda notificação, funciona independente da data da próxima higienização.
            ->orWhere(function ($sub2) use ($segundoAviso) {
                $sub2->where('qtd_notificacoes', 1)
                    ->where('ultima_notificacao', '<', $segundoAviso); // Já se passaram dois meses
            });
        })
        // 3. Funciona bloqueando notificação quando já tem higienizações agendadas.
        ->whereDoesntHave('servicos', function ($servicosQuery) {
            $servicosQuery->where('status', ServiceStatus::AGENDADO->value)
            ->where('tipo', ServiceTypes::HIGIENIZACAO->value);
        })
        ->whereNot('pmoc', true)
        ->get();

        // --- ENVIANDO OS CLIENTES PARA O JOB ---

        $this->info("Total de clientes que serão notificados: " . $clientes->count());
        Log::info("Total de clientes que serão notificados: " . $clientes->count());

        foreach ($clientes as $cliente) {
            SendWhatsappRemindersJob::dispatch($cliente)
                ->delay(now()->addSeconds(rand(15,45)));
        }
    }
}
