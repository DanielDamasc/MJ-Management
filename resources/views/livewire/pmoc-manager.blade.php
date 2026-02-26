<div>
    <div class="mb-12">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">

            <div>
                <h1 class="text-2xl font-bold text-primary-900 tracking-tight">
                    Planos de Manutenção
                </h1>
                <p class="text-sm text-primary-600 mt-1">
                    Visualize e gerencie os Planos de Manutenção da MJ Engenharia.
                </p>
            </div>

            <div>
                <button wire:click="openCreatePlan" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-secondary-500 hover:bg-secondary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-secondary-500 transition-all">
                    <x-heroicon-o-plus class="w-5 h-5 mr-1"/>
                    Novo Plano
                </button>
            </div>
        </div>

        {{-- table --}}
        <div>
            @livewire('planTable')
        </div>
    </div>

    <div class="mb-12">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">

            <div>
                <h1 class="text-2xl font-bold text-primary-900 tracking-tight">
                    Tarefas de Manutenção
                </h1>
                <p class="text-sm text-primary-600 mt-1">
                    Visualize e gerencie as Tarefas de Manutenção da MJ Engenharia.
                </p>
            </div>

            <div>
                <button wire:click="openCreateTask" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-secondary-500 hover:bg-secondary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-secondary-500 transition-all">
                    <x-heroicon-o-plus class="w-5 h-5 mr-1"/>
                    Nova Tarefa
                </button>
            </div>
        </div>

        {{-- table --}}
        <div>
            @livewire('taskTable')
        </div>
    </div>

    {{-- modals --}}
    @if ($showTaskCreate || $showTaskEdit)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-primary-950/75 backdrop-blur-sm p-4 md:inset-0 h-modal md:h-full transition-opacity">

            <div class="relative w-full max-w-xl h-full md:h-auto">

                <div class="relative bg-white rounded-xl shadow-2xl border border-primary-100">

                    <div class="flex items-center justify-between p-5 border-b border-primary-50 rounded-t">
                        <h3 class="text-xl font-bold text-primary-900">
                            {{ $showTaskCreate ? 'Nova Tarefa de Manutenção' : 'Editar Tarefa de Manutenção' }}
                        </h3>

                        <button wire:click="closeModal" type="button" class="text-primary-400 bg-transparent hover:bg-primary-50 hover:text-primary-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center transition-colors">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                        </button>
                    </div>

                    <form wire:submit="{{ $showTaskCreate ? 'saveTask' : 'editTask' }}" class="p-6 space-y-8">
                        <div class="flex flex-col gap-4">

                            <div>
                                <label class="block mb-1 text-sm font-medium text-primary-700">Nome da Tarefa
                                    <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model="taskForm.task"
                                    class="bg-primary-50 border border-primary-200 text-primary-900 text-sm rounded-lg focus:ring-secondary-500 focus:border-secondary-500 block w-full p-2.5"
                                    placeholder="Ex: Limpeza dos Filtros">
                                @error('taskForm.task') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                        </div>

                        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">

                            <button wire:click="closeModal" type="button" class="text-primary-600 bg-white hover:bg-primary-50 focus:ring-4 focus:outline-none focus:ring-primary-100 rounded-lg border border-primary-200 text-sm font-medium px-5 py-2.5 hover:text-primary-900 focus:z-10 transition-colors">
                                Cancelar
                            </button>

                            <button wire:click="{{ $showTaskCreate ? 'saveTask' : 'editTask' }}" type="button" class="text-white bg-secondary-500 hover:bg-secondary-600 focus:ring-4 focus:outline-none focus:ring-secondary-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-all shadow-md hover:shadow-lg disabled:opacity-50" wire:loading.attr="disabled">

                                <span wire:loading.remove wire:target="{{ $showTaskCreate ? 'saveTask' : 'editTask' }}">Salvar</span>
                                <span wire:loading wire:target="{{ $showTaskCreate ? 'saveTask' : 'editTask' }}">Salvando...</span>

                            </button>

                        </div>
                    </form>

                </div>

            </div>

        </div>
    @endif

    @if ($showPlanCreate || $showPlanEdit)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-primary-950/75 backdrop-blur-sm p-4 md:inset-0 h-modal md:h-full transition-opacity">

            <div class="relative w-full max-w-5xl h-full md:h-auto">

                <div class="relative bg-white rounded-xl shadow-2xl border border-primary-100">

                    <div class="flex items-center justify-between p-5 border-b border-primary-50 rounded-t">
                        <h3 class="text-xl font-bold text-primary-900">
                            {{ $showPlanCreate ? 'Novo Plano de Manutenção' : 'Editar Plano de Manutenção' }}
                        </h3>

                        <button wire:click="closeModal" type="button" class="text-primary-400 bg-transparent hover:bg-primary-50 hover:text-primary-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center transition-colors">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                        </button>
                    </div>

                    <form wire:submit="{{ $showPlanCreate ? 'savePlan' : 'editPlan' }}" class="p-6 space-y-8">
                        <div class="flex flex-col gap-6">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div class="col-span-1 md:col-span-2">
                                    <label class="block mb-1 text-sm font-medium text-primary-700">Nome do Plano
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" wire:model="planForm.plan"
                                        class="bg-primary-50 border border-primary-200 text-primary-900 text-sm rounded-lg focus:ring-secondary-500 focus:border-secondary-500 block w-full p-2.5"
                                        placeholder="Ex: Split Hi Wall">
                                    @error('planForm.plan') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-1 md:col-span-2">
                                    <label class="block mb-1 text-sm font-medium text-primary-700">Descrição (Opcional)</label>
                                    <textarea wire:model="planForm.descricao" rows="3"
                                        class="bg-primary-50 border border-primary-200 text-primary-900 text-sm rounded-lg focus:ring-secondary-500 focus:border-secondary-500 block w-full p-2.5"
                                        placeholder="Detalhes sobre a aplicação deste plano..."></textarea>
                                    @error('planForm.descricao') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                {{-- CHECKBOX DO PLANO PADRÃO --}}
                                <div class="col-span-1 md:col-span-2">
                                    <div class="flex items-start bg-primary-50/50 border border-primary-100 p-4 rounded-lg transition-colors hover:bg-primary-50">
                                        <div class="flex items-center h-5 mt-0.5">
                                            <input id="padrao" type="checkbox" wire:model="planForm.padrao"
                                                class="w-4 h-4 text-secondary-600 bg-white border-primary-300 rounded focus:ring-secondary-500 focus:ring-2 cursor-pointer">
                                        </div>
                                        <div class="ml-3">
                                            <label for="padrao" class="text-sm font-semibold text-primary-900 cursor-pointer">
                                                Definir como Plano Padrão
                                            </label>
                                            <p class="text-sm text-primary-600 mt-1">
                                                Este plano será vinculado automaticamente a todos os equipamentos que não possuem um plano de manutenção definido.
                                                <br><span class="text-primary-400 italic">Nota: Caso já exista outro plano definido como padrão, ele perderá este status.</span>
                                            </p>
                                        </div>
                                    </div>
                                    @error('planForm.padrao') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="pt-4 border-t border-primary-100">
                                <div class="mb-3 flex justify-between items-end">
                                    <div>
                                        <h4 class="text-md font-semibold text-primary-900">Vincular Tarefas</h4>
                                        <p class="text-sm text-primary-500">Selecione as tarefas que farão parte deste plano de manutenção.</p>
                                    </div>
                                </div>

                                <div class="overflow-hidden border border-primary-200 rounded-lg shadow-sm">
                                    <div class="max-h-72 overflow-y-auto">
                                        <table class="w-full text-sm text-left text-primary-700">
                                            <thead class="text-xs text-primary-700 uppercase bg-primary-100 sticky top-0 z-10">
                                                <tr>
                                                    <th scope="col" class="p-4 w-4">
                                                        <x-heroicon-o-check-circle class="w-4 h-4 text-primary-500" />
                                                    </th>
                                                    <th scope="col" class="px-6 py-3">Descrição da Tarefa</th>
                                                    <th scope="col" class="px-6 py-3">Periodicidade</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($tasks as $task)
                                                    <tr class="bg-white border-b border-primary-50">

                                                        <td class="w-4 p-4">
                                                            <div class="flex items-center">
                                                                <input id="task-{{ $task->id }}" type="checkbox"
                                                                    wire:model="planForm.tarefasSelecionadas.{{ $task->id }}.selecionada"
                                                                    value="true"
                                                                    class="w-4 h-4 text-secondary-600 bg-primary-50 border-primary-300 rounded focus:ring-secondary-500 focus:ring-2 cursor-pointer">
                                                            </div>
                                                        </td>

                                                        <td class="px-6 py-4 font-medium text-primary-900">
                                                            <label for="task-{{ $task->id }}" class="cursor-pointer block w-full h-full">
                                                                {{ $task->task }}
                                                            </label>
                                                        </td>

                                                        <td class="px-6 py-4">
                                                            <select wire:model="planForm.tarefasSelecionadas.{{ $task->id }}.periodicidade"
                                                                    class="bg-primary-50 border border-primary-200 text-primary-900 text-sm rounded-lg focus:ring-secondary-500 focus:border-secondary-500 block w-full p-2">
                                                                <option value="">Selecione...</option>
                                                                {{-- Usando os valores inteiros do seu Enum --}}
                                                                @foreach (\App\Enums\PmocPeriodicidade::cases() as $periodo)
                                                                    <option value="{{ $periodo->value }}">{{ $periodo->label() }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>

                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="px-6 py-8 text-center text-primary-500">
                                                            Nenhuma tarefa cadastrada no sistema ainda.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @error('planForm.tarefasSelecionadas')
                                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">

                            <button wire:click="closeModal" type="button" class="text-primary-600 bg-white hover:bg-primary-50 focus:ring-4 focus:outline-none focus:ring-primary-100 rounded-lg border border-primary-200 text-sm font-medium px-5 py-2.5 hover:text-primary-900 focus:z-10 transition-colors">
                                Cancelar
                            </button>

                            <button wire:click="{{ $showPlanCreate ? 'savePlan' : 'editPlan' }}" type="button" class="text-white bg-secondary-500 hover:bg-secondary-600 focus:ring-4 focus:outline-none focus:ring-secondary-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-all shadow-md hover:shadow-lg disabled:opacity-50" wire:loading.attr="disabled">

                                <span wire:loading.remove wire:target="{{ $showPlanCreate ? 'savePlan' : 'editPlan' }}">Salvar Plano</span>
                                <span wire:loading wire:target="{{ $showPlanCreate ? 'savePlan' : 'editPlan' }}">Salvando...</span>

                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if ($showDelete)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-primary-950/75 backdrop-blur-sm p-4 md:inset-0 h-modal md:h-full transition-opacity">

            <div class="relative w-full max-w-md h-full md:h-auto">
                <div class="relative bg-white rounded-xl shadow-2xl border border-primary-100">

                    <div class="p-6 text-center">
                        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-red-100 p-3 text-red-600">
                            <x-ionicon-warning-sharp class="w-8 h-8" />
                        </div>

                        <h3 class="mb-5 text-lg font-normal text-gray-600">
                            {{ $planId ?
                            'Tem certeza que deseja excluir este plano de manutenção?' :
                            'Tem certeza que deseja excluir esta tarefa de manutenção?' }}
                        </h3>

                        <p class="text-sm text-gray-800 mb-6">
                            Essa ação não pode ser desfeita e removerá todos os dados vinculados.
                        </p>

                        <div class="flex justify-center gap-3">

                            <button wire:click="closeModal" type="button" class="text-primary-600 bg-white hover:bg-primary-50 focus:ring-4 focus:outline-none focus:ring-primary-100 rounded-lg border border-primary-200 text-sm font-medium px-5 py-2.5 hover:text-primary-900 focus:z-10 transition-colors">
                                Cancelar
                            </button>

                            <button wire:click="delete" type="button" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-all shadow-md">
                                Sim, excluir
                            </button>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endif

    @if($showPlanTasks)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-primary-950/75 backdrop-blur-sm p-4 md:inset-0 h-modal md:h-full transition-opacity">

            <div class="relative w-full max-w-4xl h-full md:h-auto">

                <div class="relative bg-white rounded-xl shadow-2xl border border-primary-100">

                    <div class="flex items-center justify-between p-5 border-b border-primary-50 rounded-t bg-primary-50/50">
                        <div>
                            <h3 class="text-xl font-bold text-primary-900">
                                Tarefas do Plano
                            </h3>
                            <p class="text-sm text-primary-600 font-medium mt-1">
                                {{ $planLabel }}
                            </p>
                        </div>

                        <button wire:click="closeModal" type="button" class="text-primary-400 bg-transparent hover:bg-primary-100 hover:text-primary-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center transition-colors">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                        </button>
                    </div>

                    <div class="p-6">
                        <div class="overflow-hidden border border-primary-200 rounded-lg shadow-sm">
                            <div class="max-h-[60vh] overflow-y-auto">
                                <table class="w-full text-sm text-left text-primary-700">
                                    <thead class="text-xs text-primary-700 uppercase bg-primary-100 sticky top-0 z-10 shadow-sm">
                                        <tr>
                                            <th scope="col" class="px-6 py-3">Id</th>
                                            <th scope="col" class="px-6 py-3">Descrição da Tarefa</th>
                                            <th scope="col" class="px-6 py-3 w-40 text-center">Periodicidade</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-primary-50">
                                        @forelse($planTasks as $planTask)
                                            <tr class="bg-white hover:bg-primary-50/50 transition-colors">

                                                <td class="px-6 py-4 font-medium text-primary-900">
                                                    {{ $planTask['id'] }}
                                                </td>

                                                <td class="px-6 py-4 font-medium text-primary-900">
                                                    {{ $planTask['task'] }}
                                                </td>

                                                <td class="px-6 py-4 text-center">
                                                    <span class="bg-blue-50 text-blue-700 text-xs font-semibold px-2.5 py-1 rounded-full border border-blue-200">
                                                        {{ $planTask['periodicidade'] }}
                                                    </span>
                                                </td>

                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="px-6 py-8 text-center text-primary-500">
                                                    Nenhuma tarefa vinculada a este plano.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end p-5 border-t border-gray-100 bg-gray-50 rounded-b-xl">
                        <button wire:click="closeModal" type="button" class="text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-all shadow-md">
                            Fechar
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif

</div>
