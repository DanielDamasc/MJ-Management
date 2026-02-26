<?php

namespace App\Services;

use App\Models\PmocTask;
use Illuminate\Support\Facades\DB;

class TaskService {
    public function create(array $data)
    {
        $tarefa = PmocTask::create($data);

        return $tarefa;
    }

    public function update(PmocTask $task, array $data)
    {
        return DB::transaction(function() use ($task, $data) {
            return $task->update($data);
        });
    }

    public function delete(PmocTask $task)
    {
        return $task->delete();
    }
}
