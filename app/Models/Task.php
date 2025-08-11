<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;
    protected $fillable = ['description', 'answer', 'subject', 'type'];

    public function additionalFiles(): hasMany
    {
        return $this->hasMany(AdditionalFile::class);
    }
}
