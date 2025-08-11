<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdditionalFile extends Model
{
    protected $fillable = ['name', 'path'];

    public function task(): BelongsTo
    {
        return $this->BelongsTo(Task::class);
    }
}
