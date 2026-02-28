<?php

namespace App\Livewire;

use App\Models\Client;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Blade;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class ClientTable extends PowerGridComponent
{
    public string $tableName = 'clientTable';

    protected $listeners = ['client-refresh' => '$refresh'];

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
        return Client::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('cliente')
            ->add('contato')
            ->add('telefone')
            ->add('email')
            ->add('tipo')
            ->add('pmoc')
            ->add('tipo_pessoa')
            ->add('documento')
            ->add('ultima_notificacao');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->sortable()
                ->searchable(),

            Column::make('Cliente', 'cliente')
                ->sortable()
                ->searchable(),

            Column::make(title: 'Pessoa de Contato', field: 'contato')
                ->sortable()
                ->searchable(),

            Column::make('Telefone', 'telefone')
                ->sortable()
                ->searchable(),

            // Column::make('Email', 'email')
            //     ->sortable()
            //     ->searchable(),

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

    public function actions(Client $row): array
    {
        return [
            Button::add('details')
                ->slot(Blade::render('<x-heroicon-m-square-3-stack-3d class="w-5 h-5" />'))
                ->class('p-1 transition-colors text-auxiliar-400 hover:text-auxiliar-600 dark:text-auxiliar-400 dark:hover:text-auxiliar-300')
                ->dispatchTo('clients-manager', 'open-details', ['id' => $row->id]),

            Button::add('edit')
                ->slot(Blade::render('<x-heroicon-o-pencil-square class="w-5 h-5" />'))
                ->class('p-1 transition-colors text-secondary-400 hover:text-secondary-600 dark:text-secondary-400 dark:hover:text-secondary-300')
                ->dispatchTo('clients-manager', 'open-edit', ['id' => $row->id]),

            Button::add('delete')
                ->slot(Blade::render('<x-heroicon-o-trash class="w-5 h-5" />'))
                ->class('p-1 transition-colors text-red-400 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300')
                ->dispatchTo('clients-manager', 'confirm-delete', ['id' => $row->id]),
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
