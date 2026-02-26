<?php

namespace App\Services;

use App\Models\Client;
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

            // 3. Remove os dados do PMOC caso ele seja desmarcado.
            $data = $this->uncheckPmoc($data);

            // 4. Valida o cnpj.
            if ($data['cnpj']) {
                $data['cnpj'] = $this->limparInput($data['cnpj']);
                $this->validateCnpj($data['cnpj']);
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
            // 1. Limpeza do Telefone
            $data['telefone'] = $this->limparInput($data['telefone']);

            // 2. Validação do Telefone
            $this->validateTelefone($data['telefone']);

            // 3. Remove os dados do PMOC caso ele seja desmarcado.
            $data = $this->uncheckPmoc($data);

            // 4. Valida o cnpj.
            if ($data['cnpj']) {
                $data['cnpj'] = $this->limparInput($data['cnpj']);
                $this->validateCnpj($data['cnpj'], $client->id);
            }

            // 5. Se o cep não for vazio, cria ou atualiza o endereço para o cliente.
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

            return $client->update($data);
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

        // 2. Validação de unicidade
        // $query = Client::where('telefone', $phone);

        // if ($ignoreId) {
        //     $query->where('id', '!=', $ignoreId);
        // }

        // if ($query->exists()) {
        //     throw new Exception("O telefone já foi cadastrado.");
        // }
    }

    private function validateCnpj($cnpj, $ignoreId = null) {

        // 1. Validação de tamanho.
        if (Str::of($cnpj)->length() !== 14) {
            throw new Exception("O CNPJ deve conter 14 dígitos.");
        }

        // 2. Validação de unicidade
        $query = Client::where('cnpj', $cnpj);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        if ($query->exists()) {
            throw new Exception("CNPJ já cadastrado.");
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

    private function uncheckPmoc(array $data): array
    {
        if ($data['pmoc'] == false) {
            $data['razao_social'] = $data['cnpj'] = null;
        }

        return $data;
    }
}
