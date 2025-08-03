<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskSession extends Model
{
    protected $fillable = ['status'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
