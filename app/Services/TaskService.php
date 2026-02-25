<?php

namespace App\Services;

use App\Models\PmocTask;

class TaskService {
    public function create(array $data)
    {
        $tarefa = PmocTask::create($data);

        return $tarefa;
    }
}
