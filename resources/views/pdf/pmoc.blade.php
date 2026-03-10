<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>PMOC - {{ $cliente->cliente }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        h1, h2, h3 {
            text-align: center;
            margin: 5px 0;
            color: #000;
        }
        h1 { font-size: 16px; text-decoration: underline; margin-bottom: 20px; }
        h2 { font-size: 13px; text-align: left; background-color: #f0f0f0; padding: 5px; border: 1px solid #000; margin-top: 15px; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f9f9f9;
            font-weight: bold;
            text-align: center;
        }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }

        .assinaturas {
            page-break-inside: avoid;
            margin-top: 50px;
        }
        .linha-assinatura {
            width: 350px;
            margin: 0 auto;
            border-top: 1px solid #333;
            padding-top: 5px;
        }

        .watermark {
            position: fixed;
            top: 10%;
            left: 0;
            width: 100%;
            text-align: center;
            z-index: -1000;
            opacity: 0.05;
        }
        .watermark img {
            height: 800px;
        }

        /* Força quebra de página se necessário, para não cortar tabelas ao meio */
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

    <div class="watermark">
        {{-- Use public_path() para o DomPDF conseguir ler o arquivo localmente no servidor --}}
        <img src="{{ public_path('img/marcadaguaMJ.jpg') }}" alt="Marca d'água">
    </div>

    <h1>PLANO DE MANUTENÇÃO, OPERAÇÃO E CONTROLE - PMOC</h1>

    <h2>1. Identificação do Cliente / Local</h2>
    <table>
        <tr>
            <td colspan="2"><span class="text-bold">Razão Social / Nome:</span> {{ $cliente->cliente ?: 'N/A' }}</td>
            <td><span class="text-bold">CNPJ/CPF:</span> {{ $cliente->documento ?: 'N/A' }}</td>
        </tr>
        <tr>
            <td colspan="3"><span class="text-bold">Endereço:</span> {{ $cliente->address?->endereco ?: 'N/A' }}</td>
        </tr>
        <tr>
            <td><span class="text-bold">Telefone:</span> {{ $cliente->telefone ?: 'N/A' }}</td>
            <td colspan="2"><span class="text-bold">E-mail:</span> {{ $cliente->email ?: 'N/A' }}</td>
        </tr>
    </table>

    {{-- Dados fixos ! --}}
    <h2>2. Identificação do Responsável Técnico</h2>
    <table>
        <tr>
            <td colspan="2"><span class="text-bold">Nome:</span> Marcos Aurélio Meira da Silva Junior</td>
            <td><span class="text-bold">CPF:</span> 012.669.556-31</td>
        </tr>
        <tr>
            <td colspan="2"><span class="text-bold">Endereço:</span> Rua Valter Silva Santos, 445, LJ 01, Vila Mauriceia, Montes Claros-MG</td>
            <td><span class="text-bold">Registro CREA:</span> MG-95132/D</td>
        </tr>
        <tr>
            <td><span class="text-bold">Telefone:</span> (38) 99886-0302</td>
            <td colspan="2"><span class="text-bold">E-mail:</span> marcosameira@hotmail.com</td>
        </tr>
    </table>

    <h2>3. Relação de Equipamentos e Ambientes</h2>
    <table>
        <thead>
            <tr>
                <th>Cod</th>
                <th>Ambiente</th>
                <th>Área (m²)</th>
                <th>Ocupantes</th>
                <th>Potência (BTUs)</th>
                <th>Marca / Modelo</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cliente->airConditioners as $ac)
                <tr>
                    <td class="text-center">{{ $ac->codigo_ac ?: 'N/A' }}</td>
                    <td>{{ $ac->ambiente ?: 'N/A' }}</td>
                    <td class="text-center">{{ $ac->area_climatizada ?: 'N/A' }}</td>
                    <td class="text-center">{{ $ac->numero_ocupantes ?: 'N/A' }}</td>
                    <td class="text-center">{{ $ac->potencia ?: 'N/A' }}</td>
                    <td>{{ $ac->marca ?: 'N/A' }} / {{ $ac->modelo ?: 'N/A' }}</td>
                </tr>
            @endforeach
            </tbody>
    </table>

    <h2>4. {{ $plano->plan }}</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 70%;">Descrição da Atividade</th>
                <th style="width: 15%;">Periodicidade</th>
                <th style="width: 15%;">Cliente Executa?</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($plano->tasks as $task)
                <tr>
                    <td>{{ $task->task }}</td>
                    <td class="text-center">
                        {{ \App\Enums\PmocPeriodicidade::from($task->pivot->periodicidade)->label() }}
                    </td>
                    <td class="text-center">
                        {{ $task->pivot->cliente_executa ? "Sim" : "Não" }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>5. Cronograma de Manutenção</h2>
    <table>
        <thead>
            <tr>
                <th colspan="12">Meses de Execução</th>
            </tr>
            <tr>
                @for ($i = 0; $i < 12; $i++)
                    <th class="text-center">
                        {{ \Carbon\Carbon::parse($cliente->inicio_pmoc)
                            ->addMonths($i)
                            ->format('m/Y')
                        }}
                    </th>
                @endfor
            </tr>
        </thead>
        <tbody>
            <tr>
                @for ($i = 1; $i <= 12; $i++)

                    @php
                        $letrasDoMes = $plano->tasks
                            ->filter(fn($task) =>
                                $i % $task->pivot->periodicidade == 0
                            )
                            ->map(fn($task) =>
                                \App\Enums\PmocPeriodicidade::from($task->pivot->periodicidade)->firstLetter()
                            )
                            ->unique()
                            ->implode('/');
                    @endphp

                    <td class="text-center">{{ $letrasDoMes }}</td>

                @endfor
            </tr>
            </tbody>
    </table>

    <div style="font-size: 10px; margin-bottom: 20px;">
        <strong>Legenda:</strong> [ M ]Mensal | [ T ]Trimestral | [ S ]Semestral | [ A ]Anual
    </div>

    <div class="page-break"></div>

    <h2>6. Recomendações e Situações de Emergência</h2>
    <div style="padding: 10px; border: 1px solid #000;">
        <p><span class="text-bold">Equipamento não refrigera:</span> Desligar o aparelho e solicitar a presença da equipe técnica.</p>
        <p><span class="text-bold">Equipamento não liga:</span> Desconectar a alimentação do aparelho (se possível) e solicitar a equipe técnica.</p>
        <p><span class="text-bold">Ruído acima do habitual:</span> Desligar o aparelho e solicitar a presença da equipe técnica.</p>
        <p><span class="text-bold">Poeira/sujidade saindo do aparelho:</span> Desligar o aparelho e solicitar a presença da equipe técnica.</p>

        <h3 style="text-align: left; margin-top: 15px;">EM CASO DE PRINCÍPIO DE INCÊNDIO:</h3>
        <p><strong>Pequenas proporções:</strong> Usar o extintor adequado mais próximo. Acionar a Brigada de Incêndio, SESMT e chefia imediata.</p>
        <p><strong>Grandes proporções:</strong> Solicitar ajuda (acionar a Brigada de Incêndio <strong>193</strong>), retirar as pessoas do local, desligar a chave de energia elétrica principal.</p>
    </div>

    <h2>7. Elaboração e Aprovação</h2>
    <p style="text-align: justify; padding: 0 10px;">
        Este documento foi elaborado pelo responsável técnico e aprovado pelo responsável pelas instalações. Ambos confirmam a veracidade das informações aqui contidas e se dispõem a cumprir as recomendações para efeito de manutenção da saúde de todos os ocupantes e preservação do meio ambiente.
    </p>

    <div class="assinaturas" style="text-align: center; margin-top: 60px;">

        <div class="linha-assinatura">
            Assinatura do Técnico Responsável
        </div>

        <br><br><br><br>

        <div class="linha-assinatura">
            Assinatura do Cliente
        </div>

    </div>

</body>
</html>
