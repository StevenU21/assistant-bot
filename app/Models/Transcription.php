<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Events\TranscriptionCompleted;
use Illuminate\Support\Facades\Auth;

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
