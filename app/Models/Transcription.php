<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transcription extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'language',
        'file',
        'slug',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getFilePathAttribute(): string
    {
        return asset('storage/' . $this->file);
    }
}
