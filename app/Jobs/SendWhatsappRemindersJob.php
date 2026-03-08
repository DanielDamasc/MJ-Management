<?php

namespace App\Jobs;

use App\Enums\ServiceStatus;
use App\Models\ActivityLog;
use App\Models\Client;
use Carbon\Carbon;
use Exception;
use Http;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Log;
use Str;

class SendWhatsappRemindersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3; // quantidade de tentativas em caso de erro.
    public $backoff = 60; // tempo de espera entre tentativas.

    /**
     * Create a new job instance.
     */
    public function __construct(public Client $client)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Puxando configurações da evolution.
        $apiUrl = config('services.evolution.url');
        $apiKey = config('services.evolution.key');
        $instance = config('services.evolution.instance');

        // Dados do cliente.
        $nome = Str::of($this->client->contato)->explode(' ')->first();
        $to = $this->normalizarTelefone($this->client->telefone);
        $mesesCalculados = $this->calcularUltimaHigienizacao();

        $saudacoes = [
            "Olá *{$nome}*, tudo bem?",
            "Oi *{$nome}*, como vai?",
            "Olá *{$nome}*, tudo certo?",
            "Oi *{$nome}*, espero que esteja tudo bem!",
        ];
        $saudacao = Arr::random($saudacoes);

        // Configura dois modelos distintos caso já tenha feito uma higienização anteriormente.
        if (is_null($mesesCalculados)) {

            $corpos = [
                "Passando para lembrar que está na hora de agendar a higienização dos seus ar-condicionados.",
                "Consta em nosso sistema que é o momento ideal para fazer a limpeza preventiva dos seus aparelhos.",
                "Verificamos que já é o momento de realizar a higienização periódica dos seus ar-condicionados."
            ];
            $ctas = [
                "Vamos marcar?",
                "Podemos agendar um horário?",
                "Qual seria o melhor dia para você?",
                "Gostaria de deixar agendado?"
            ];

            $corpo = Arr::random($corpos);
            $cta = Arr::random($ctas);

            $textoFinal = "{$saudacao}\n\n{$corpo} {$cta}";
            $logMeses = 'N/A';
            $templateName = 'aviso_higienizacao_simples';
        } else {
            $textoMeses = (string) $mesesCalculados . ' meses';

            $aberturasMeses = [
                "Já está completando *{$textoMeses}* desde a sua última higienização.",
                "Faz *{$textoMeses}* que realizamos a última limpeza nos seus aparelhos.",
                "Notei aqui no sistema que a sua última manutenção foi há *{$textoMeses}*."
            ];
            $motivos = [
                "Para garantir a saúde do ambiente e o funcionamento do aparelho,",
                "Para manter a qualidade do ar e evitar que ele gaste muita energia,",
                "Para garantir que o aparelho continue gelando bem e evitar fungos,"
            ];
            $fechamentos = [
                "que tal agendarmos uma nova higienização?",
                "vamos marcar a próxima limpeza?",
                "podemos deixar a próxima visita agendada?"
            ];

            $abertura = Arr::random($aberturasMeses);
            $motivo = Arr::random($motivos);
            $fechamento = Arr::random($fechamentos);

            $textoFinal = "{$saudacao}\n\n{$abertura} {$motivo} {$fechamento}";
            $logMeses = $textoMeses;
            $templateName = 'aviso_higienizacao_com_meses';
        }

        $payload = [
            'number' => $to,
            'text' => $textoFinal,
            'delay' => 1500,
            'presence' => 'composing' // Mostra digitando...
        ];

        Log::info("JOB [Cliente {$this->client->id}]: Enviando Payload...", $payload);

        $response = Http::withHeaders([
            'apiKey' => $apiKey
        ])->post("{$apiUrl}/message/sendText/{$instance}", $payload);

        Log::info("
            JOB [Cliente {$this->client->id}]:
            Resposta da API ({$response->status()})",$response->json()
        );

        if ($response->successful()) {

            // Atualiza os dados de notificação do cliente.
            $this->client->update([
                'ultima_notificacao' => now(),
                'qtd_notificacoes' => $this->client->qtd_notificacoes + 1
            ]);

            // Cria um registro de logs para cada notificação realizada.
            ActivityLog::create([
                'log_name'     => 'notificacao_whatsapp',
                'description'  => 'Lembrete de higienização enviado via WhatsApp API',
                'event'        => 'sent',

                // Vincula ao Cliente (Subject)
                'subject_type' => Client::class,
                'subject_id'   => $this->client->id,

                // Define quem fez (Ninguém = Sistema)
                'causer_type'  => null,
                'causer_id'    => null,

                // Dados extras (Payload)
                'properties'   => [
                    'telefone_destino' => $to,
                    'meses_calculados' => $logMeses,
                    'template_usado' => $templateName,
                    'status_api' => 'sucesso'
                ],
            ]);

        } else {
            Log::error("Erro na API do Whatsapp: " . $response->body());
            throw new Exception("Erro na API do Whatsapp: " . $response->body());
        }
    }

    private function normalizarTelefone($tel)
    {
        // Arranca letras, espaços, traços e parênteses. Deixa SÓ os números.
        $telLimpo = preg_replace('/[^0-9]/', '', $tel);

        // Se tiver 11 dígitos ou menos, coloca o 55 do Brasil na frente
        if (strlen($telLimpo) <= 11) {
            return '55' . $telLimpo;
        }

        return $telLimpo;
    }

    private function calcularUltimaHigienizacao()
    {
        $ultimoServico = $this->client->servicos()
            ->where('status', ServiceStatus::CONCLUIDO->value)
            ->where('tipo', 'higienizacao')
            ->latest('data_servico')
            ->first();

        if ($ultimoServico && $ultimoServico->data_servico) {
            $data = Carbon::parse($ultimoServico->data_servico);

            $diff = (int) ceil($data->floatDiffInMonths(now()));

            return $diff;
        }

        // Retorna null e envia outro template para quando não tem último serviço.
        return null;
    }
}
