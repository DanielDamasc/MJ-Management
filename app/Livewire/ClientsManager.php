<?php

namespace App\Livewire;

use App\Enums\PersonTypes;
use App\Models\Client;
use App\Services\ClientService;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Str;

class ClientsManager extends Component
{
    protected ClientService $clientService;

    public function boot(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    // Atributos de Cliente.
    public $cliente = '';
    public $contato = '';
    public $telefone = '';
    public $email = '';
    public $tipo = '';

    // Atributos do cliente PMOC.
    public $pmoc = false;
    public $tipo_pessoa = ''; // F ou J
    public $documento = ''; // CPF ou CNPJ
    public $inicio_pmoc = '';

    // Atributos de Endereço.
    public $cep = '';
    public $rua = '';
    public $numero = '';
    public $bairro = '';
    public $complemento = '';
    public $cidade = '';
    public $uf = '';

    // Outros Atributos Auxiliares.
    public $showAddress = false;
    public $showDetails = false;
    public $showCreate = false;
    public $showDelete = false;
    public $showEdit = false;
    public $clientId = null;

    public $equipmentList = [];

    protected function rules()
    {
        return [
            'cliente' => 'required',
            'contato' => 'required',
            'telefone' => 'required',
            'email' => [
                'nullable',
                'email',
                Rule::unique('clients', 'email')->ignore($this->clientId),
            ],
            'tipo' => [
                'required',
                'string',
                Rule::in(['residencial', 'comercial']),
            ],

            'pmoc' => 'boolean',
            'tipo_pessoa' => [
                'nullable',
                Rule::requiredIf(fn () => $this->pmoc == true),
                new Enum(PersonTypes::class),
            ],
            'documento' => [
                'nullable',
                Rule::requiredIf(fn () => $this->pmoc == true),
                function ($attribute, $value, $fail) {
                    // Remove tudo que não é número.
                    $numeros = preg_replace('/[^0-9]/', '', $value);

                    // Validação para cpf.
                    if ($this->tipo_pessoa === PersonTypes::FISICA->value) {
                        if (strlen($numeros) !== 11) {
                            $fail('O CPF deve conter exatamente 11 números.');
                        }
                    }

                    // Validação para cnpj.
                    elseif ($this->tipo_pessoa === PersonTypes::JURIDICA->value) {
                        if (strlen($numeros) !== 14) {
                            $fail('O CNPJ deve conter exatamente 14 números.');
                        }
                    }
                }
            ],
            'inicio_pmoc' => [
                'nullable',
                'date',
            ],

            'cep' => [
                'nullable','string','max:9'
            ],
            'rua' => [
                'nullable','string','max:255',
            ],
            'numero' => [
                'nullable','string','max:20',
            ],
            'bairro' => [
                'nullable','string','max:100',
            ],
            'complemento' => [
                'nullable','string','max:150',
            ],
            'cidade' => [
                'nullable','string','max:100',
                Rule::requiredIf(fn() =>
                    $this->cep != ''
                ),
            ],
            'uf' => [
                'nullable','string','size:2',
                Rule::requiredIf(fn() =>
                    $this->cep != ''
                ),
            ],
        ];
    }

    protected $messages = [
        'cliente.required' => 'O campo cliente é obrigatório.',
        'contato.required' => 'Informe o nome da pessoa de contato.',
        'telefone.required' => 'O campo telefone é obrigatório.',
        'telefone.unique' => 'O telefone já foi cadastrado.',
        'email.email' => 'Informe um endereço de email válido.',
        'email.unique' => 'O email já foi cadastrado.',
        'tipo.required' => 'O campo tipo é obrigatório.',
    ];

    public function updatedCep($value)
    {
        try {
            $res = $this->clientService->loadCep($value);

            if ($res->successful() && !isset($res['erro'])) {
                $dados = $res->json();

                $this->rua = $dados['logradouro'];
                $this->bairro = $dados['bairro'];
                $this->cidade = $dados['localidade'];
                $this->uf = $dados['uf'];

                $this->resetValidation(['rua', 'bairro', 'cidade', 'uf']);
            }
        } catch (Exception $e) {
            $this->dispatch('notify-error', $e->getMessage());
        }
    }

    public function updatedTipoPessoa($value)
    {
        // 1. Limpa o que o usuário havia digitado no campo documento
        $this->documento = '';

        // 2. Limpa qualquer mensagem de erro vermelha antiga que tenha ficado na tela
        $this->resetValidation('documento');
    }

    public function clearAddress()
    {
        $this->reset([
            'cep',
            'rua',
            'numero',
            'bairro',
            'complemento',
            'cidade',
            'uf'
        ]);
    }

    public function closeModal()
    {
        $this->showCreate = $this->showDelete = $this->showEdit = $this->showAddress = false;
        $this->resetValidation();
    }

    public function closeDetails()
    {
        $this->showDetails = false;
        $this->equipmentList = [];
    }

    #[On('open-details')]
    public function openDetails($id)
    {
        $this->clientId = $id;

        $client = Client::with('airConditioners')->find($this->clientId);
        if ($client) {
            $this->equipmentList = $client->airConditioners;
        }

        $this->showDetails = true;
    }

    public function openCreate()
    {
        $this->reset([
            'cliente',
            'contato',
            'telefone',
            'email',
            'tipo',
            'pmoc',
            'tipo_pessoa',
            'documento',
            'inicio_pmoc',
            'clientId',
        ]);
        $this->clearAddress();
        $this->resetValidation();
        $this->showCreate = true;
    }

    public function save()
    {
        $this->validate();

        // Caso o pmoc esteja desmarcado, envia null nos seus atributos.
        $tipoPessoaFinal = $this->pmoc ? $this->tipo_pessoa : null;
        $documentoFinal = $this->pmoc ? $this->documento : null;
        $inicioPmocFinal = $this->pmoc ? $this->inicio_pmoc : null;

        try {
            $this->clientService->create([
                'cliente' => $this->cliente,
                'contato' => $this->contato,
                'telefone' => $this->telefone,
                'email' => $this->email,
                'tipo' => $this->tipo,
                'pmoc' => $this->pmoc,
                'tipo_pessoa' => $tipoPessoaFinal,
                'documento' => $documentoFinal,
                'inicio_pmoc' => $inicioPmocFinal ?: null,
            ],
            [
                'cep' => $this->cep,
                'rua' => $this->rua,
                'numero' => $this->numero,
                'bairro' => $this->bairro,
                'complemento' => $this->complemento,
                'cidade' => $this->cidade,
                'uf' => $this->uf
            ]);

            $this->closeModal();
            $this->dispatch('notify-success', 'Cliente cadastrado com sucesso!');
            $this->dispatch('client-refresh');

        } catch (Exception $e) {
            $this->dispatch('notify-error', $e->getMessage());
        }
    }

    #[On('open-edit')]
    public function openEdit($id)
    {
        $this->clientId = $id;
        $this->showEdit = true;

        if ($this->clientId) {
            $client = Client::find($this->clientId);
            $this->cliente = $client->cliente;
            $this->contato = $client->contato;
            $this->telefone = $client->telefone;
            $this->email = $client->email ?? ''; // E-mail pode ser null.
            $this->tipo = $client->tipo;

            $this->pmoc = $client->pmoc;
            // Apenas se for pmoc possui razão social e cnpj.
            if ($this->pmoc == true) {
                $this->tipo_pessoa = $client->tipo_pessoa->value;
                $this->documento = $client->documento;
                $this->inicio_pmoc = $client->inicio_pmoc;
            } else {
                $this->reset([
                    'tipo_pessoa',
                    'documento',
                    'inicio_pmoc',
                ]);
            }

            // Buscando dados do endereço.
            if ($client->address) {
                $this->showAddress = true; // Abre o componente de endereço.
                $this->cep = $client->address->cep;
                $this->rua = $client->address->rua;
                $this->numero = $client->address->numero;
                $this->bairro = $client->address->bairro;
                $this->complemento = $client->address->complemento ?? '';
                $this->cidade = $client->address->cidade;
                $this->uf = $client->address->uf;
            } else {
                $this->showAddress = false;
                // Limpa caso tenha aberto um com endereço antes.
                $this->clearAddress();
            }
        }
    }

    public function edit()
    {
        $this->validate();

        $client = Client::find($this->clientId);

        if ($client) {

            // Caso o pmoc esteja desmarcado, envia null nos seus atributos.
            $tipoPessoaFinal = $this->pmoc ? $this->tipo_pessoa : null;
            $documentoFinal = $this->pmoc ? $this->documento : null;
            $inicioPmocFinal = $this->pmoc ? $this->inicio_pmoc : null;

            try {
                $this->clientService->update($client, [
                    'cliente' => $this->cliente,
                    'contato' => $this->contato,
                    'telefone' => $this->telefone,
                    'email' => $this->email,
                    'tipo' => $this->tipo,
                    'pmoc' => $this->pmoc,
                    'tipo_pessoa' => $tipoPessoaFinal,
                    'documento' => $documentoFinal,
                    'inicio_pmoc' => $inicioPmocFinal ?: null,
                ],
                [
                    'cep' => $this->cep,
                    'rua' => $this->rua,
                    'numero' => $this->numero,
                    'bairro' => $this->bairro,
                    'complemento' => $this->complemento,
                    'cidade' => $this->cidade,
                    'uf' => $this->uf
                ]);

                $this->closeModal();
                $this->dispatch('notify-success', 'Dados atualizados com sucesso!');
                $this->dispatch('client-refresh');

            } catch(Exception $e) {
                $this->dispatch('notify-error', $e->getMessage());

            }
        }
    }

    #[On('confirm-delete')]
    public function confirmDelete($id)
    {
        $this->clientId = $id;
        $this->showDelete = true;
    }

    public function delete()
    {
        if ($this->clientId) {
            $client = Client::find($this->clientId);

            try {
                $this->clientService->delete($client);
                $this->dispatch('notify-success', 'Cliente deletado com sucesso.');
                $this->dispatch('client-refresh');

            } catch (Exception $e) {
                $this->dispatch('notify-error', $e->getMessage());

            } finally {
                $this->clientId = null;
                $this->closeModal();

            }
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $tiposPessoa = PersonTypes::cases();

        return view('livewire.clients-manager', [
            'tiposPessoa' => $tiposPessoa
        ]);
    }
}
