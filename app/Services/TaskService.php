<?php

namespace App\Services;

use App\Models\PmocTask;

class TaskService {
    public function create(array $data)
    {
        $tarefa = PmocTask::create($data);

        return $tarefa;
    }

    public function delete(PmocTask $task)
    {
        return $task->delete();
    }
}
