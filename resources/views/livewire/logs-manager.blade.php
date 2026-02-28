<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-start mb-8">
        <div>
            <h1 class="text-2xl font-bold text-primary-900 dark:text-white tracking-tight">
                Gerenciamento de Logs
            </h1>
            <p class="text-sm text-primary-600 dark:text-primary-400 mt-1">
                Visualize as principais operações realizadas no sistema.
            </p>
        </div>
    </div>

    <div>
        @livewire('logsTable')
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-primary-950/75 backdrop-blur-sm p-4 md:inset-0 h-modal md:h-full transition-opacity">

            <div class="relative w-full max-w-xl h-auto">

                <div class="relative flex flex-col max-h-[90vh] bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-primary-100 dark:border-gray-700 overflow-hidden">

                    <div class="flex-shrink-0 flex items-center justify-between p-5 border-b border-primary-50 dark:border-gray-700 rounded-t">
                        <h3 class="text-xl font-bold text-primary-900 dark:text-white">
                            Detalhes do Log
                        </h3>

                        <button wire:click="closeModal" type="button" class="text-primary-400 dark:text-gray-400 bg-transparent hover:bg-primary-50 dark:hover:bg-gray-700 hover:text-primary-900 dark:hover:text-white rounded-lg text-sm p-1.5 ml-auto inline-flex items-center transition-colors">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                        </button>
                    </div>


                    <div class="flex-1 overflow-y-auto p-6 space-y-6">

                        @if ($createdData)
                            <h4 class="text-sm uppercase tracking-wide text-blue-600 dark:text-blue-400 font-bold mb-4 pt-2">
                                Dados Criados
                            </h4>
                            @foreach ($createdData as $key => $value)
                                @if (is_array($value) || is_object($value))
                                    <label class="block mb-1 text-sm font-medium text-primary-700 dark:text-gray-300">
                                        {{ $key }}
                                    </label>
                                    <pre class="text-xs bg-gray-100 dark:bg-gray-900/80 dark:border dark:border-gray-700 text-gray-800 dark:text-gray-300 p-2 rounded overflow-x-auto">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                @else
                                    <div>
                                        <label class="block mb-1 text-sm font-medium text-primary-700 dark:text-gray-300">
                                            {{ $key }}
                                        </label>
                                        <input type="text"
                                            readonly
                                            value="{{ $value }}"
                                            class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800/50 text-green-900 dark:text-green-400 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                                    </div>
                                @endif
                            @endforeach

                        @elseif ($deletedData)
                            <h4 class="text-sm uppercase tracking-wide text-blue-600 dark:text-blue-400 font-bold mb-4 pt-2">
                                Dados Excluídos
                            </h4>
                            @foreach ($deletedData as $key => $value)
                                @if (is_array($value) || is_object($value))
                                    <label class="block mb-1 text-sm font-medium text-primary-700 dark:text-gray-300">
                                        {{ $key }}
                                    </label>
                                    <pre class="text-xs bg-gray-100 dark:bg-gray-900/80 dark:border dark:border-gray-700 text-gray-800 dark:text-gray-300 p-2 rounded overflow-x-auto">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                @else
                                    <div>
                                        <label class="block mb-1 text-sm font-medium text-primary-700 dark:text-gray-300">
                                            {{ $key }}
                                        </label>
                                        <input type="text"
                                            readonly
                                            value="{{ $value }}"
                                            class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 text-red-900 dark:text-red-400 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                                    </div>
                                @endif
                            @endforeach

                        @elseif ($updatedData)
                            <h4 class="text-sm uppercase tracking-wide text-blue-600 dark:text-blue-400 font-bold mb-4 pt-2">
                                Alterações Realizadas
                            </h4>

                            {{-- Cabeçalho da Tabela Visual --}}
                            <div class="grid grid-cols-2 gap-4 mb-2">
                                <div class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Valor Antigo</div>
                                <div class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Valor Novo</div>
                            </div>

                            {{-- Loop Inteligente: Itera sobre o 'old' e busca o 'new' correspondente --}}
                            @foreach ($updatedData['old'] as $key => $oldValue)

                                @php
                                    // Pega o valor novo usando a mesma chave
                                    $newValue = $updatedData['new'][$key] ?? 'N/A';
                                @endphp

                                <div class="border-b border-gray-100 dark:border-gray-700 pb-4 last:border-0">
                                    <label class="block mb-1 text-sm font-bold text-primary-700 dark:text-gray-300">
                                        {{ $key }}
                                    </label>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                        {{-- COLUNA 1: DADO ANTIGO --}}
                                        <div class="relative">
                                            @if (is_array($oldValue) || is_object($oldValue))
                                                <pre class="text-xs bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 p-2 rounded border border-red-100 dark:border-red-800/50 h-full overflow-x-auto">{{ json_encode($oldValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            @else
                                                <div class="flex items-center">
                                                    <input type="text"
                                                        readonly
                                                        value="{{ $oldValue }}"
                                                        class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 text-red-900 dark:text-red-400 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                                                </div>
                                            @endif
                                        </div>

                                        {{-- COLUNA 2: DADO NOVO --}}
                                        <div class="relative">
                                            @if (is_array($newValue) || is_object($newValue))
                                                <pre class="text-xs bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 p-2 rounded border border-green-100 dark:border-green-800/50 h-full overflow-x-auto">{{ json_encode($newValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            @else
                                                <div class="flex items-center">
                                                    <input type="text"
                                                        readonly
                                                        value="{{ $newValue }}"
                                                        class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800/50 text-green-900 dark:text-green-400 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                                                </div>
                                            @endif
                                        </div>

                                    </div>
                                </div>
                            @endforeach

                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
