<?php

namespace App\Services;

use App\Models\AirConditioning;
use App\Models\Client;
use App\Models\PmocPlan;
use DB;
use Exception;
use Http;
use Str;

class ClientService
{
    public function create(array $data, array $address)
    {
        return DB::transaction(function() use ($data, $address) {

            // 1. Limpeza do Telefone
            $data['telefone'] = $this->limparInput($data['telefone']);

            // 2. Validação do Telefone
            $this->validateTelefone($data['telefone']);

            // 3. Limpar o input do documento
            if (!empty($data['documento'])) {
                $data['documento'] = $this->limparInput($data['documento']);
            }

            // 4. Cria o cliente com os dados
            $cliente = Client::create($data);

            // 5. Se o cep não for vazio, cria o endereço do cliente
            if (!empty($address['cep'])) {
                $cliente->address()->create($address);
            }

            return $cliente;
        });
    }

    public function update(Client $client, array $data, array $address)
    {
        return DB::transaction(function() use ($client, $data, $address) {

            // 1. Verifica se está virando cliente pmoc nesta edição.
            $virouPmoc = (!$client->pmoc && !empty($data['pmoc']));

            // 1. Limpeza do Telefone
            $data['telefone'] = $this->limparInput($data['telefone']);

            // 2. Validação do Telefone
            $this->validateTelefone($data['telefone']);

            // 3. Limpar o input do documento
            if (!empty($data['documento'])) {
                $data['documento'] = $this->limparInput($data['documento']);
            }

            // 4. Cria ou atualiza o endereço para o cliente.
            if (!empty($address['cep'])) {
                $client->address()->updateOrCreate(
                    // Busca no banco se existe.
                    [
                        'addressable_id' => $client->id,
                        'addressable_type' => Client::class,
                    ],
                    // Caso exista, atualiza, caso contrário, cria.
                    $address
                );
            } else {
                // Deleta caso o usuário tenha limpado os campos.
                $client->address()->delete();
            }

            // 5. Atualiza os dados.
            $client->update($data);

            // 6. Regra de negócio do PMOC padrão.
            if ($virouPmoc) {
                $planoPadrao = PmocPlan::where('padrao', true)->first();

                if ($planoPadrao) {
                    AirConditioning::where('cliente_id', $client->id)
                        ->whereNull('plano_id')
                        ->update(['plano_id' => $planoPadrao->id]);
                }
            }

            // Retorna o objeto cliente.
            return $client->refresh();
        });
    }

    public function delete(Client $client)
    {
        if ($client->servicos()->withTrashed()->exists()) {
            throw new Exception("Não se pode deletar um cliente com serviço vinculado.");
        }

        return DB::transaction(function() use ($client) {
            // Remove o endereço vinculado.
            $client->address()->delete();

            return $client->delete();
        });
    }

    private function limparInput($value)
    {
        return preg_replace('/[^0-9]/', '', $value);
    }

    private function validateTelefone($phone)
    {
        // 1. Validação de tamanho
        if (Str::of($phone)->length() !== 11) {
            throw new Exception("O telefone deve conter 11 dígitos.");
        }
    }

    public function loadCep($value)
    {
        // 1. Limpa o cep
        $cep = preg_replace('/[^0-9]/', '', $value);

        // 2. Validação de tamanho
        if (strlen($cep) != 8) {
            throw new Exception('O CEP deve ter 8 dígitos.');
        }

        // 3. Busca da API
        $response = Http::withOptions([
            'verify' => true,
        ])
        ->withUserAgent('MjEngenharia')
        ->timeout(10)
        ->get("https://viacep.com.br/ws/{$cep}/json/");

        // 4. Retorna a resposta
        return $response;
    }
}
