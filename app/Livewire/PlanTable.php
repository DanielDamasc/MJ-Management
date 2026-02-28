<?php

namespace App\Livewire;

use App\Models\PmocPlan;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Blade;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class PlanTable extends PowerGridComponent
{
    public string $tableName = 'planTable';

    protected $listeners = ['plan-refresh' => '$refresh'];

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return PmocPlan::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('plan')
            ->add('descricao');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->sortable()
                ->searchable(),

            Column::make('Nome do Plano', 'plan')
                ->sortable()
                ->searchable(),

            Column::make('Descrição', 'descricao')
                ->sortable()
                ->searchable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert('.$rowId.')');
    }

    public function actions(PmocPlan $row): array
    {
        return [
            Button::add('details')
                ->slot(Blade::render('<x-heroicon-m-square-3-stack-3d class="w-5 h-5" />'))
                ->class('p-1 transition-colors text-auxiliar-400 hover:text-auxiliar-600 dark:text-auxiliar-400 dark:hover:text-auxiliar-300')
                ->dispatchTo('pmoc-manager', 'open-plan-tasks', ['id' => $row->id]),

            Button::add('edit')
                ->slot(Blade::render('<x-heroicon-o-pencil-square class="w-5 h-5" />'))
                ->class('p-1 transition-colors text-secondary-400 hover:text-secondary-600 dark:text-secondary-400 dark:hover:text-secondary-300')
                ->dispatchTo('pmoc-manager', 'open-plan-edit', ['id' => $row->id]),

            Button::add('delete')
                ->slot(Blade::render('<x-heroicon-o-trash class="w-5 h-5" />'))
                ->class('p-1 transition-colors text-red-400 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300')
                ->dispatchTo('pmoc-manager', 'confirm-plan-delete', ['id' => $row->id]),
        ];
    }

    /*
    public function actionRules($row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}
