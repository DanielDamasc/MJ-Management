<?php

namespace Database\Seeders;

use App\Models\PmocTask;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PmocTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tarefas = [
            'Limpeza dos filtros de ar',
            'Higienização química da evaporadora (Serpentina, ventilador e bandeja)',
            'Teste e desobstrução do sistema de drenagem',
            'Verificação do isolamento térmico das tubulações',
            'Limpeza da condensadora (Gabinete e serpentina)',
            'Revisão e reaperto dos contatos elétricos',
        ];

        foreach ($tarefas as $tarefa) {
            PmocTask::firstOrCreate([
                'task' => $tarefa
            ]);
        }
    }
}
