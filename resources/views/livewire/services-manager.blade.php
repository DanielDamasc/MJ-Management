<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">

        <div>
            <h1 class="text-2xl font-bold text-primary-900 dark:text-white tracking-tight">
                Gerenciamento de Ordens de Serviço
            </h1>
            <p class="text-sm text-primary-600 dark:text-primary-400 mt-1">
                Visualize e gerencie as ordens de serviço da MJ Engenharia.
            </p>
        </div>

        <div>
            <button wire:click="openCreate" class="inline-flex items-center justify-center px-4 py-2
                border border-transparent text-sm font-medium rounded-lg shadow-sm
                text-white bg-secondary-500 hover:bg-secondary-600
                focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-secondary-500 transition-all
                dark:bg-secondary-600 dark:hover:bg-secondary-500 dark:focus:ring-offset-gray-900 dark:focus:ring-secondary-400">
                <x-heroicon-o-plus class="w-5 h-5 mr-1"/>
                Novo Serviço
            </button>
        </div>
    </div>

    <div>
        @livewire('servicesTable')
    </div>

    @if ($showView)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-primary-950/75 backdrop-blur-sm p-4 md:inset-0 h-modal md:h-full transition-opacity">

            <div class="relative w-full max-w-5xl h-auto">

                <div class="relative flex flex-col max-h-[90vh] bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-primary-100 dark:border-gray-700 overflow-hidden">

                    <div class="flex-shrink-0 flex items-center justify-between p-5 border-b border-primary-50 dark:border-gray-700 rounded-t border-b border-gray-100 pb-4">
                        <h3 class="text-xl font-bold text-primary-900 dark:text-white">
                            Visualizar Ordem de Serviço
                        </h3>
                        <button wire:click="closeModal" type="button" class="text-primary-400 dark:text-gray-400 bg-transparent hover:bg-primary-50 dark:hover:bg-gray-700 hover:text-primary-900 dark:hover:text-white rounded-lg text-sm p-1.5 ml-auto inline-flex items-center transition-colors">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto">

                        <div class="p-6 space-y-6">

                            <div>
                                <h4 class="text-sm uppercase tracking-wide text-blue-600 font-bold mb-4 border-b border-primary-100 dark:border-gray-700 pb-2">
                                    1. Cliente e Equipamentos
                                </h4>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-5">

                                    {{-- CLIENTE --}}
                                    <div class="col-span-1 md:col-span-1 lg:col-span-6">
                                        <label class="block mb-1 text-sm font-medium text-primary-700 dark:text-gray-300">Cliente
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <div class="h-10 bg-gray-100 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 cursor-not-allowed select-none rounded-lg flex items-center px-3 text-sm w-full">
                                            {{ $cliente_label }}
                                        </div>
                                    </div>

                                    {{-- EXECUTOR --}}
                                    <div class="col-span-1 md:col-span-1 lg:col-span-6">
                                        <label class="block mb-1 text-sm font-medium text-primary-700 dark:text-gray-300">Técnico Responsável (Executor)
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <div class="h-10 bg-gray-100 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 cursor-not-allowed select-none rounded-lg flex items-center px-3 text-sm w-full">
                                            {{ $executor_label }}
                                        </div>
                                    </div>

                                    {{-- LISTA DE AR-CONDICIONADOS (Checkboxes) --}}
                                    <div class="col-span-1 md:col-span-2 lg:col-span-12">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Equipamentos para o Serviço
                                            <span class="text-red-500">*</span>
                                        </label>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 max-h-60 overflow-y-auto border border-gray-200 dark:border-gray-700 p-3 rounded-lg bg-gray-50 dark:bg-gray-900/50">
                                            @foreach($acs_disponiveis as $ac)
                                                @if (in_array($ac->id, $ac_ids))
                                                    <div class="flex flex-col justify-between p-3 bg-white dark:bg-gray-800 border border-blue-200 dark:border-blue-800/50 rounded-lg shadow-sm ring-1 ring-blue-100 dark:ring-blue-900">

                                                        {{-- Dados do AC --}}
                                                        <div class="flex items-start space-x-3 mb-3">
                                                            <div class="flex-shrink-0">
                                                                <x-heroicon-s-check-circle class="w-5 h-5 text-blue-600 dark:text-blue-500" />
                                                            </div>
                                                            <div class="text-sm">
                                                                <p class="font-bold text-gray-800 dark:text-gray-100">{{ $ac->codigo_ac }}</p>
                                                                <p class="text-gray-600 dark:text-gray-400 text-xs mt-0.5">
                                                                    {{ $ac->ambiente ? $ac->ambiente : 'N/A' }} • {{ $ac->marca ? $ac->marca : 'N/A' }} • {{ $ac->potencia }} BTUs
                                                                </p>
                                                            </div>
                                                        </div>

                                                        {{-- Valor do AC --}}
                                                        <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-700 mt-auto">
                                                            <span class="text-[10px] text-gray-500 dark:text-gray-400 font-bold uppercase">Valor Cobrado</span>
                                                            <div class="text-sm font-bold text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-900/30 px-2 py-1 rounded border border-blue-100 dark:border-blue-800/50">
                                                                R$ {{ number_format($ac_precos[$ac->id] ?? 0, 2, ',', '.') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h4 class="text-sm uppercase tracking-wide text-blue-600 font-bold mb-4 border-b border-primary-100 dark:border-gray-700 pb-2">
                                    2. Detalhes do Serviço
                                </h4>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-5">

                                    {{-- TIPO --}}
                                    <div class="col-span-1 md:col-span-1 lg:col-span-4">
                                        <label class="block mb-1 text-sm font-medium text-primary-700 dark:text-gray-300">Tipo de Serviço
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <div class="h-10 bg-gray-100 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 cursor-not-allowed select-none rounded-lg flex items-center px-3 text-sm w-full">
                                            {{ $tipo_label }}
                                        </div>
                                    </div>

                                    {{-- DATA --}}
                                    <div class="col-span-1 md:col-span-1 lg:col-span-3">
                                        <label class="block mb-1 text-sm font-medium text-primary-700 dark:text-gray-300">Data de Realização
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <div class="h-10 bg-gray-100 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 cursor-not-allowed select-none rounded-lg flex items-center px-3 text-sm w-full">
                                            {{ \Carbon\Carbon::parse($data_servico)->format('d/m/Y') }}
                                        </div>
                                    </div>

                                    {{-- HORÁRIO --}}
                                    <div class="col-span-1 md:col-span-1 lg:col-span-2">
                                        <label class="block mb-1 text-sm font-medium text-primary-700 dark:text-gray-300">
                                            Horário
                                        </label>
                                        <div class="h-10 bg-gray-100 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 cursor-not-allowed select-none rounded-lg flex items-center px-3 text-sm w-full">
                                            <x-heroicon-o-clock class="w-4 h-4 mr-2 text-gray-400"/>
                                            {{ $horario }}
                                        </div>
                                    </div>

                                    {{-- VALOR TOTAL --}}
                                    <div class="col-span-1 md:col-span-1 lg:col-span-2">
                                        <label class="block mb-1 text-sm font-medium text-primary-700 dark:text-gray-300">Valor Total (R$)
                                        </label>
                                        <div class="h-10 bg-gray-100 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 cursor-not-allowed select-none rounded-lg flex items-center px-3 text-sm w-full">
                                            {{ number_format($valor_total, 2, ',', '.') }}
                                        </div>
                                    </div>

                                    {{-- STATUS --}}
                                    <div class="col-span-1 md:col-span-1 lg:col-span-3">
                                        <label class="block mb-1 text-sm font-medium text-primary-700 dark:text-gray-300">Status Atual
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <div class="h-10 bg-gray-100 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 cursor-not-allowed select-none rounded-lg flex items-center px-3 text-sm w-full">
                                            {{ $status_label }}
                                        </div>
                                    </div>

                                    {{-- CAMPOS DINÂMICOS (JSON) --}}
                                    @if($tipo)
                                        <div class="col-span-1 md:col-span-2 lg:col-span-12 mt-2">
                                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/50 rounded-lg p-4 animate-fade-in-down">
                                                <h5 class="text-sm font-bold text-blue-800 dark:text-blue-400 mb-3 flex items-center">
                                                    <x-heroicon-o-clipboard-document-list class="w-4 h-4 mr-2"/>
                                                    Checklist: {{ $tipo_label }}
                                                </h5>

                                                {{-- Para os serviços de higienização, coloca como options as tarefas de manutenção. --}}
                                                @if($tipo === \App\Enums\ServiceTypes::HIGIENIZACAO->value)
                                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                                                        {{-- Assumindo que você está enviando a variável $tarefas do Livewire --}}
                                                        @forelse($tarefas as $tarefa)
                                                            <div class="flex items-start">
                                                                <div class="flex items-center h-5">
                                                                    <input id="view-chk-task-{{ $tarefa->id }}"
                                                                        type="checkbox"
                                                                        disabled
                                                                        @checked(data_get($detalhes, 'tarefas.' . $tarefa->id))
                                                                        class="w-4 h-4 rounded cursor-not-allowed text-blue-600 dark:text-blue-400 bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-600 disabled:opacity-50 disabled:checked:opacity-100 transition-opacity">
                                                                </div>
                                                                <div class="ml-2 text-sm">
                                                                    <label for="view-chk-task-{{ $tarefa->id }}" class="font-medium text-gray-800 dark:text-gray-300 cursor-not-allowed select-none opacity-75">
                                                                        {{ $tarefa->task }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <div class="col-span-full text-sm text-gray-500 dark:text-gray-400 italic">
                                                                Nenhuma tarefa de manutenção cadastrada no sistema.
                                                            </div>
                                                        @endforelse
                                                    </div>
                                                @endif

                                            </div>
                                        </div>
                                    @endif

                                    @if($observacoes_executor)
                                        <div class="col-span-1 md:col-span-2 lg:col-span-12 mt-2">
                                            <label class="block mb-1 text-sm font-medium text-primary-700 dark:text-gray-300">
                                                Observações do Executor
                                            </label>
                                            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800/50 rounded-lg p-4 flex items-start gap-3">
                                                <div class="flex-shrink-0 mt-0.5">
                                                    <x-heroicon-o-chat-bubble-left-ellipsis class="w-5 h-5 text-yellow-600 dark:text-yellow-500" />
                                                </div>
                                                <p class="text-sm text-gray-800 dark:text-gray-200 leading-relaxed break-words">
                                                    {{ $observacoes_executor }}
                                                </p>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            </div>

                        </div>

                        <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-b-xl">
                            <button type="button" wire:click="closeModal" class="text-primary-600 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-primary-50 dark:hover:bg-gray-700 dark:hover:text-white focus:ring-4 focus:outline-none focus:ring-primary-100 dark:focus:ring-gray-700 rounded-lg border border-primary-200 dark:border-gray-600 text-sm font-medium px-5 py-2.5 hover:text-primary-900 focus:z-10 transition-colors">
                                Fechar
                            </button>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    @endif

    @if ($showCreate || $showEdit)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-primary-950/75 backdrop-blur-sm p-4 md:inset-0 h-modal md:h-full transition-opacity">

            <div class="relative w-full max-w-5xl h-auto">

                <div class="relative flex flex-col max-h-[90vh] bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-primary-100 dark:border-gray-700 overflow-hidden">

                    <div class="flex-shrink-0 flex items-center justify-between p-5 border-b border-primary-50 dark:border-gray-700 rounded-t border-b border-gray-100 pb-4">
                        <h3 class="text-xl font-bold text-primary-900 dark:text-white">
                            {{ $showCreate ? 'Nova Ordem de Serviço' : 'Editar Ordem de Serviço' }}
                        </h3>
                        <button wire:click="closeModal" type="button" class="text-primary-400 dark:text-gray-400 bg-transparent hover:bg-primary-50 dark:hover:bg-gray-700 hover:text-primary-900 dark:hover:text-white rounded-lg text-sm p-1.5 ml-auto inline-flex items-center transition-colors">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                        </button>
                    </div>

                    <form wire:submit="{{ $showCreate ? 'save' : 'edit' }}" class="flex-1 overflow-y-auto">

                        <div class="p-6 space-y-6">

                            <div>
                                <h4 class="text-sm uppercase tracking-wide text-blue-600 font-bold mb-4 border-b border-primary-100 dark:border-gray-700 pb-2">
                                    1. Cliente e Equipamentos
                                </h4>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-5">

                                    {{-- CLIENTE --}}
                                    <div class="col-span-1 md:col-span-1 lg:col-span-6">
                                        <label class="block mb-1 text-sm font-medium text-primary-700 dark:text-gray-300">Cliente
                                            <span class="text-red-500">*</span>
                                        </label>
                                        @if ($showEdit)
                                            <div class="bg-gray-100 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 cursor-not-allowed select-none rounded-lg block w-full p-2.5 text-sm">
                                                {{ $cliente_label }}
                                            </div>
                                            <small class="text-xs text-gray-500 dark:text-gray-300 mt-1">O cliente não pode ser alterado na edição.</small>
                                        @else
                                            <select wire:model.live="cliente_id" class="bg-primary-50 dark:bg-gray-900 border border-primary-200 dark:border-gray-600 text-primary-900 dark:text-white text-sm rounded-lg focus:ring-secondary-500 focus:border-secondary-500 block w-full p-2.5">
                                                <option value="">Selecione o cliente...</option>
                                                @foreach($clientes as $client)
                                                    <option value="{{ $client->id }}">{{ $client->cliente }}</option>
                                                @endforeach
                                            </select>
                                            @error('cliente_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                        @endif
                                    </div>

                                    {{-- EXECUTOR --}}
                                    <div class="col-span-1 md:col-span-1 lg:col-span-6">
                                        <label class="block mb-1 text-sm font-medium text-primary-700 dark:text-gray-300">Técnico Responsável (Executor)
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <select wire:model="executor_id" class="bg-primary-50 dark:bg-gray-900 border border-primary-200 dark:border-gray-600 text-primary-900 dark:text-white text-sm rounded-lg focus:ring-secondary-500 focus:border-secondary-500 block w-full p-2.5">
                                            <option value="">Selecione o executor...</option>
                                            @foreach($executores as $executor)
                                                <option value="{{ $executor->id }}">{{ $executor->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('executor_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>

                                    {{-- LISTA DE AR-CONDICIONADOS (Checkboxes) --}}
                                    <div class="col-span-1 md:col-span-2 lg:col-span-12">
                                        <label class="block mb-1 text-sm font-medium text-primary-700 dark:text-gray-300">Equipamentos para o Serviço
                                            <span class="text-red-500">*</span>
                                        </label>

                                        @if(count($acs_disponiveis) > 0)
                                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 max-h-80 overflow-y-auto border border-gray-200 dark:border-gray-700 p-3 rounded-lg bg-gray-100 dark:bg-gray-900/50">
                                                @foreach($acs_disponiveis as $ac)
                                                    <div
                                                        wire:key="ac-{{ $ac->id }}"
                                                        class="flex items-center justify-between p-3 rounded-lg transition-all
                                                        {{ in_array($ac->id, $ac_ids) ?
                                                        'bg-white dark:bg-gray-800 border-blue-500 dark:border-blue-400 shadow-md ring-1 ring-blue-500 dark:ring-blue-400' :
                                                        'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-gray-500' }}"
                                                    >
                                                        {{-- Checkbox e Dados do AC --}}
                                                        <label class="flex items-start space-x-3 cursor-pointer flex-1">
                                                            <div class="flex items-center h-5 mt-1">
                                                                <input type="checkbox" wire:model.live="ac_ids" value="{{ $ac->id }}"
                                                                    class="w-4 h-4 text-blue-600 bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500 dark:focus:ring-offset-gray-800"
                                                                >
                                                            </div>
                                                            <div class="text-sm">
                                                                <p class="font-bold text-gray-800 dark:text-gray-100">{{ $ac->codigo_ac }}</p>
                                                                <p class="text-gray-500 dark:text-gray-400 text-xs">{{ $ac->ambiente }} • {{ $ac->marca ? $ac->marca : 'N/A' }} • {{ $ac->potencia }} BTUs</p>
                                                            </div>
                                                        </label>

                                                        {{-- Campo de Preço (Só aparece se selecionado) --}}
                                                        @if(in_array($ac->id, $ac_ids))
                                                            <div class="ml-4 w-32 animate-fade-in-right">
                                                                <label class="text-[10px] text-gray-500 dark:text-gray-400 font-bold uppercase">Valor (R$)</label>
                                                                <input
                                                                    type="number"
                                                                    step="0.01"
                                                                    wire:model="ac_precos.{{ $ac->id }}"
                                                                    placeholder="0.00"
                                                                    class="h-8 w-full text-right bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/50 text-gray-900 dark:text-blue-100 rounded text-sm focus:ring-blue-500 focus:border-blue-500"
                                                                >
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="flex flex-col items-center justify-center p-8 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50 text-center">
                                                @if($cliente_id)
                                                    <x-heroicon-s-x-circle class="w-8 h-8 text-gray-400 dark:text-gray-500 mb-2" />
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                                        Este cliente não possui equipamentos cadastrados.
                                                    </p>
                                                @else
                                                    <x-ionicon-snow-outline class="w-8 h-8 text-gray-400 dark:text-gray-500 mb-2" />
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                                        Selecione um cliente acima para carregar a lista.
                                                    </p>
                                                @endif
                                            </div>
                                        @endif
                                        @error('ac_ids') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h4 class="text-sm uppercase tracking-wide text-blue-600 font-bold mb-4 border-b border-primary-100 dark:border-gray-700 pb-2">
                                    2. Detalhes do Serviço
                                </h4>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-5">

                                    {{-- TIPO --}}
                                    <div class="col-span-1 md:col-span-1 lg:col-span-4">
                                        <label class="block mb-1 text-sm font-medium text-primary-700 dark:text-gray-300">Tipo de Serviço
                                            <span class="text-red-500">*</span>
                                        </label>
                                        @if ($showEdit)
                                            <div class="bg-gray-100 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 cursor-not-allowed select-none rounded-lg block w-full p-2.5 text-sm">
                                                {{ $tipo_label }}
                                            </div>
                                            <small class="text-xs text-gray-500 dark:text-gray-300 mt-1">O tipo de serviço não pode ser alterado na edição.</small>
                                        @else
                                            <select wire:model.live="tipo" class="bg-primary-50 dark:bg-gray-900 border border-primary-200 dark:border-gray-600 text-primary-900 dark:text-white text-sm rounded-lg focus:ring-secondary-500 focus:border-secondary-500 block w-full p-2.5">
                                                <option value="">Selecione...</option>
                                                @foreach ($tiposServico as $_)
                                                    <option value="{{ $_->value }}">{{ $_->label() }}</option>
                                                @endforeach
                                            </select>
                                            @error('tipo') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                        @endif
                                    </div>

                                    {{-- DATA --}}
                                    <div class="col-span-1 md:col-span-1 lg:col-span-3">
                                        <label class="block mb-1 text-sm font-medium text-primary-700 dark:text-gray-300">Data de Realização
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <input type="date" wire:model="data_servico" class="bg-primary-50 dark:bg-gray-900 border border-primary-200 dark:border-gray-600 text-primary-900 dark:text-white text-sm rounded-lg focus:ring-secondary-500 focus:border-secondary-500 block w-full p-2.5">
                                        @error('data_servico') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>

                                    {{-- HORÁRIO --}}
                                    <div class="col-span-1 md:col-span-1 lg:col-span-2">
                                        <label class="block mb-1 text-sm font-medium text-primary-700 dark:text-gray-300">
                                            Horário
                                        </label>
                                        <input type="time" wire:model="horario" class="bg-primary-50 dark:bg-gray-900 border border-primary-200 dark:border-gray-600 text-primary-900 dark:text-white text-sm rounded-lg focus:ring-secondary-500 focus:border-secondary-500 block w-full p-2.5">
                                        @error('horario') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>

                                    {{-- STATUS --}}
                                    <div class="col-span-1 md:col-span-1 lg:col-span-3">
                                        <label class="block mb-1 text-sm font-medium text-primary-700 dark:text-gray-300">Status Atual
                                            <span class="text-red-500">*</span>
                                        </label>
                                        @if ($showEdit)
                                            <div class="bg-gray-100 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 cursor-not-allowed select-none rounded-lg block w-full p-2.5 text-sm">
                                                {{ $status_label }}
                                            </div>
                                            <small class="text-xs text-gray-500 dark:text-gray-300 mt-1">O status não pode ser alterado na edição.</small>
                                        @else
                                            <select wire:model="status" class="bg-primary-50 dark:bg-gray-900 border border-primary-200 dark:border-gray-600 text-primary-900 dark:text-white text-sm rounded-lg focus:ring-secondary-500 focus:border-secondary-500 block w-full p-2.5">
                                                @foreach($statusServico as $_)
                                                    <option value="{{ $_->value }}">{{ $_->label() }}</option>
                                                @endforeach
                                            </select>
                                            @error('status') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                        @endif
                                    </div>

                                    {{-- CAMPOS DINÂMICOS (JSON) --}}
                                    @if($tipo)
                                        <div class="col-span-1 md:col-span-2 lg:col-span-12 mt-2">
                                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/50 rounded-lg p-4 animate-fade-in-down">
                                                <h5 class="text-sm font-bold text-blue-800 dark:text-blue-400 mb-3 flex items-center">
                                                    <x-heroicon-o-clipboard-document-list class="w-4 h-4 mr-2"/>
                                                    Checklist: {{ $tipo_label }}
                                                </h5>

                                                {{-- Para os serviços de higienização, coloca como options as tarefas de manutenção. --}}
                                                @if($tipo === \App\Enums\ServiceTypes::HIGIENIZACAO->value)
                                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                                                        {{-- Assumindo que você está enviando a variável $tarefas do Livewire --}}
                                                        @forelse($tarefas as $tarefa)
                                                            <div class="flex items-start">
                                                                <div class="flex items-center h-5">
                                                                    <input id="chk-task-{{ $tarefa->id }}"
                                                                        type="checkbox"
                                                                        wire:model="detalhes.tarefas.{{ $tarefa->id }}"
                                                                        class="w-4 h-4 text-blue-600 dark:text-blue-500 bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500 dark:focus:ring-blue-400 dark:focus:ring-offset-gray-900 cursor-pointer transition-all">
                                                                </div>
                                                                <div class="ml-2 text-sm">
                                                                    <label for="chk-task-{{ $tarefa->id }}" class="font-medium text-gray-800 dark:text-gray-200 cursor-pointer select-none">
                                                                        {{ $tarefa->task }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <div class="col-span-full text-sm text-gray-500 italic">
                                                                Nenhuma tarefa de manutenção cadastrada no sistema.
                                                            </div>
                                                        @endforelse
                                                    </div>
                                                @endif

                                            </div>
                                        </div>
                                    @endif

                                </div>
                            </div>

                        </div>

                        <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-b-xl">
                            <button type="button" wire:click="closeModal" class="text-primary-600 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-primary-50 dark:hover:bg-gray-700 dark:hover:text-white focus:ring-4 focus:outline-none focus:ring-primary-100 dark:focus:ring-gray-700 rounded-lg border border-primary-200 dark:border-gray-600 text-sm font-medium px-5 py-2.5 hover:text-primary-900 focus:z-10 transition-colors">
                                Cancelar
                            </button>
                            <button type="submit" wire:loading.attr="disabled" class="text-white bg-secondary-500 hover:bg-secondary-600 dark:bg-secondary-600 dark:hover:bg-secondary-500 focus:ring-4 focus:outline-none focus:ring-secondary-200 dark:focus:ring-gray-900 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-all shadow-md hover:shadow-lg disabled:opacity-50">
                                <span wire:loading.remove wire:target="{{ $showCreate ? 'save' : 'edit' }}">{{ $showCreate ? 'Criar' : 'Salvar Alterações' }}</span>
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    @endif

    @if ($showDelete)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-primary-950/75 backdrop-blur-sm p-4 md:inset-0 h-modal md:h-full transition-opacity">

            <div class="relative w-full max-w-md h-auto">
                <div class="relative flex flex-col max-h-[90vh] bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-primary-100 dark:border-gray-700 overflow-hidden">

                    <div class="p-6 text-center">
                        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30 p-3 text-red-600">
                            <x-ionicon-warning-sharp class="w-8 h-8" />
                        </div>

                        <h3 class="mb-5 text-lg font-normal text-gray-600 dark:text-gray-200">
                            Tem certeza que deseja excluir este serviço?
                        </h3>

                        <p class="text-sm text-gray-800 dark:text-gray-400 mb-6">
                            Essa ação não pode ser desfeita.
                        </p>

                        <div class="flex justify-center gap-3">

                            <button wire:click="closeModal" type="button" class="text-primary-600 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-primary-50 dark:hover:bg-gray-700 dark:hover:text-white focus:ring-4 focus:outline-none focus:ring-primary-100 dark:focus:ring-gray-700 rounded-lg border border-primary-200 dark:border-gray-600 text-sm font-medium px-5 py-2.5 hover:text-primary-900 focus:z-10 transition-colors">
                                Cancelar
                            </button>

                            <button wire:click="delete" type="button" class="text-white bg-red-600 dark:bg-red-700 hover:bg-red-700 dark:hover:bg-red-600 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-gray-900 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-all shadow-md">
                                Sim, excluir
                            </button>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endif

    @if ($showConfirm || $showCancel)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-primary-950/75 backdrop-blur-sm p-4 md:inset-0 h-modal md:h-full transition-opacity">

            <div class="relative w-full max-w-md h-auto">
                <div class="relative flex flex-col max-h-[90vh] bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-primary-100 dark:border-gray-700 overflow-hidden">

                    <div class="p-6 text-center">
                        @if ($showConfirm)
                            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30 p-3 text-green-600">
                                <x-heroicon-o-check-circle class="w-8 h-8" />
                            </div>
                        @else
                            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30 p-3 text-red-600">
                                <x-heroicon-o-x-circle class="w-8 h-8" />
                            </div>
                        @endif

                        <h3 class="mb-5 text-lg font-normal text-gray-600 dark:text-gray-200">
                            {{ $showConfirm ? 'Deseja concluir o serviço?' : 'Deseja cancelar o serviço?' }}
                        </h3>

                        <p class="text-sm text-gray-800 dark:text-gray-400 mb-6">
                            Essa ação mudará o status de forma definitiva, e não pode ser revertida.
                        </p>

                        <div class="flex justify-center gap-3">

                            <button wire:click="closeModal" type="button" class="text-primary-600 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-primary-50 dark:hover:bg-gray-700 dark:hover:text-white focus:ring-4 focus:outline-none focus:ring-primary-100 dark:focus:ring-gray-700 rounded-lg border border-primary-200 dark:border-gray-600 text-sm font-medium px-5 py-2.5 hover:text-primary-900 focus:z-10 transition-colors">
                                Cancelar
                            </button>

                            @if ($showConfirm)
                                <button wire:click="serviceDone" type="button" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-all shadow-md">
                                    Concluir Serviço
                                </button>
                            @else
                                <button wire:click="serviceCancel" type="button" class="text-white bg-red-600 dark:bg-red-700 hover:bg-red-700 dark:hover:bg-red-600 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-gray-900 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-all shadow-md">
                                    Cancelar Serviço
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
