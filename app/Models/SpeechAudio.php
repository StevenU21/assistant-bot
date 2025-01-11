<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpeechAudio extends Model
{
    protected $table = 'speech_audio';
    protected $fillable = [
        'title',
        'content',
        'voice',
        'slug'
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
