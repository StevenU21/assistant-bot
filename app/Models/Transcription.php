<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Transcription extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'language',
        'audio',
        'slug',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
