<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PmocTask extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 'pmoc_tasks';

    protected $fillable = [
        'task'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
    }

    public function plans()
    {
        return $this->belongsToMany(PmocPlan::class,
            'plan_tasks',
            'task_id',
            'plan_id',
            'id',
            'id'
        )->withPivot(['periodicidade', 'cliente_executa']);
    }
}
