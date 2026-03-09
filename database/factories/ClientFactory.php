<?php

namespace Database\Factories;

use App\Enums\PersonTypes;
use App\Models\Client;
use Arr;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Client::class;

    public function definition(): array
    {
        $tipo = Arr::random(['residencial', 'comercial']);
        $isComercial = $tipo === 'comercial';

        $nomeCliente = $isComercial ? fake()->company() : fake()->name();

        $temPmoc = $isComercial ? fake()->boolean(80) : fake()->boolean(20);

        // Iniciam como null.
        $tipoPessoa = null;
        $documento = null;
        $inicioPmoc = null;

        if ($temPmoc) {
            $tipoPessoa = $isComercial ? PersonTypes::JURIDICA->value : PersonTypes::FISICA->value;

            // Parâmetro false garante que venha sem mask.
            $documento = $isComercial ? fake('pt_BR')->cnpj(false) : fake('pt_BR')->cpf(false);

            $inicioPmoc = fake()->date('Y-m-d');
        }

        return [
            'cliente' => $nomeCliente,
            'contato' => fake()->firstName(),
            'telefone' => $this->gerarTelefoneCelular(),
            'email' => fake()->unique()->safeEmail(),
            'tipo' => $tipo,

            'pmoc' => $temPmoc,
            'tipo_pessoa' => $tipoPessoa,
            'documento' => $documento,
            'inicio_pmoc' => $inicioPmoc,

            // 70% de chance de ser null e 30% de ter uma data.
            'ultima_notificacao' => fake()->optional(0.3)->dateTimeBetween('-1 year', 'now'),
            'qtd_notificacoes' => fake()->numberBetween(0,2)
        ];
    }

    private function gerarTelefoneCelular(): string
    {
        $ddd = random_int(11, 99);

        $numero = '9' . random_int(10000000, 99999999);

        return (string) $ddd . $numero;
    }

    // Cria um cliente que nunca foi notificado.
    public function nuncaNotificado()
    {
        return $this->state(fn (array $attributes) => [
            'ultima_notificacao' => null,
            'qtd_notificacoes' => 0,
        ]);
    }

    // Cria um cliente que foi notificado ontem para verificar spam.
    public function recemNotificado()
    {
        return $this->state(fn (array $attributes) => [
            'ultima_notificacao' => now()->subDay(),
            'qtd_notificacoes' => 1,
        ]);
    }
}
