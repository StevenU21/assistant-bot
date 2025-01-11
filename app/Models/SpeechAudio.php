<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpeechAudio extends Model
{
    protected $table = 'speech_audio';
    protected $fillable = [
        'text',
        'voice',
    ];

}
