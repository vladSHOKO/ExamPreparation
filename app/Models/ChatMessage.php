<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    protected $fillable = ['content', 'created_at', 'role'];

    public function session(): BelongsTo
    {
        return $this->belongsTo(TaskSession::class);
    }
}
