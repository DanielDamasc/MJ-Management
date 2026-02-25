<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PmocPlan extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 'pmoc_plans';

    protected $fillable = [
        'plan',
        'descricao',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
    }

    public function tasks()
    {
        return $this->belongsToMany(PmocTask::class,
            'plan_tasks',
            'plan_id',
            'task_id',
            'id',
            'id'
        )->withPivot('periodicidade');
    }

    public function airConditioners()
    {
        return $this->hasMany(AirConditioning::class, 'plano_id');
    }
}
